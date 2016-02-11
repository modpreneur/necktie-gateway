<?php

namespace Necktie\Bundle\GatewayBundle\Command;

use InvalidArgumentException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Exception\AMQPExceptionInterface;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 */
abstract class BaseCommnad extends ContainerAwareCommand
{

    /** @var int */
    protected $maxMessagesPerProcess = 100;

    /** @var int */
    protected $repeater = 0;

    /** @var int */
    protected $counterMessages = 0;

    /** @var OutputInterface */
    protected $output;

    /** @var  AMQPLazyConnection */
    protected $connection;

    /** @var  AMQPChannel */
    protected $channel;

    /** @var bool */
    protected $forceStop = FALSE;

    /** @var string */
    protected $consumerTag;

    /** @var int  */
    protected $idleTimeout = 0;

    /** @var  AMQPMessage */
    public $msg;

    protected $data = [];



    function __destruct()
    {
        if($this->channel)
            $this->channel->close();

        if($this->connection)
            $this->connection->close();

        if(!empty($this->data)){
            $this->close();
        }
    }


    protected function configure()
    {
        $this
            ->addOption(
                'max-messages',
                'm',
                InputOption::VALUE_OPTIONAL,
                'Set max messages per process.',
                100
            )

            ->addOption(
                'without-signals',
                'w',
                InputOption::VALUE_NONE,
                'Disable catching of system signals'
            )

            ->addOption(
                'debug',
                'd',
                InputOption::VALUE_NONE,
                'Enable Debugging'
            )

            ->addOption(
                'route',
                'r',
                InputOption::VALUE_OPTIONAL,
                'Routing Key', ''
            );
    }


    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->consumerTag = sprintf("PHPPROCESS_%s_%s", gethostname(), getmypid());


        if (defined('AMQP_WITHOUT_SIGNALS') === false) {
            define('AMQP_WITHOUT_SIGNALS', $input->getOption('without-signals'));
        }


        if (!AMQP_WITHOUT_SIGNALS && extension_loaded('pcntl')) {
            if (!function_exists('pcntl_signal')) {
                throw new \BadFunctionCallException("Function 'pcntl_signal' is referenced in the php.ini 'disable_functions' and can't be called.");
            }
            pcntl_signal(SIGTERM, [$this, 'signalTerm']);
            pcntl_signal(SIGINT,  [$this, 'signalInt']);
            pcntl_signal(SIGHUP,  [$this, 'signalHup']);
        }


        if (defined('AMQP_DEBUG') === false) { define('AMQP_DEBUG', (bool) $input->getOption('debug')); }

        $max = $input->getOption('max-messages');
        if(!is_numeric($max)){ throw new InvalidArgumentException('Parameter \'max-messages\' must be numeric value.');}
        if($max < 0){throw new InvalidArgumentException('Parameter \'max-messages\' must be greater than 10.');}
        $this->maxMessagesPerProcess = (int)$max;
    }


    public abstract function process(AMQPMessage $msg);


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws AMQPExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $consumer = $input->getArgument('consumer');
        $this->output = $output;

        $this->initConnection();
        $channel = $this->getChannel();
        $channel->queue_declare($consumer, false, true, false, false);
        $output->writeln('[' . (new \DateTime())->format(\DateTime::W3C) . '] <info>Waiting for messages. To exit press CTRL+C</info>');

        $callback = function ($msg) use ($output) {
            $this->msg = $msg;
            $r = $this->process($msg);
            $this->handleProcessMessage($msg, $r);
            $this->counterMessages++;

            if($this->counterMessages >= $this->maxMessagesPerProcess){
                $this->close();
                die(1);
            }
        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($consumer, $this->consumerTag, false, false, false, false, $callback);


        try {
            while (count($channel->callbacks)) {
                $this->maybeStopConsumer();
                $channel->wait(null, false, $this->idleTimeout);
            }
        }catch (AMQPRuntimeException $e) {
            restore_error_handler();
            $this->maybeStopConsumer();
            if (!$this->forceStop) {
                throw $e;
            }
            $this->close();
        } catch (AMQPExceptionInterface $e) {
            restore_error_handler();
            $this->close();
            throw $e;
        }
    }


    /**
     * @param $msg
     * @param bool|string $processFlag
     */
    protected function handleProcessMessage(AMQPMessage $msg, $processFlag)
    {
        if ($processFlag) {
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        }

        if (false == $processFlag && $this->repeater >= 10) {
            $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag'], false);
            $this->repeater = 0;
        } elseif (false == $processFlag) {
            $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag'], true);
            sleep(rand(5, 10));
            $this->repeater++;
        }

    }


    /**
     * @return \PhpAmqpLib\Channel\AMQPChannel
     */
    protected function initConnection()
    {
        $connection = $this->getContainer()->get('necktie.gateway.connection');
        $this->connection = $connection;
        $this->channel = $connection->channel();
    }


    public function signalTerm()
    {
        pcntl_signal(SIGTERM, SIG_DFL);
        $this->forceStopConsumer();
    }


    public function signalInt()
    {
        pcntl_signal(SIGINT, SIG_DFL);
        $this->forceStopConsumer();
    }


    public function signalHup()
    {
        pcntl_signal(SIGHUP, SIG_DFL);
        $this->forceStopConsumer();
    }


    protected function getConnection()
    {
        return $this->connection;
    }


    /**
     * @return AMQPChannel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    public function forceStopConsumer()
    {
        $this->forceStop = true;
    }

    protected function maybeStopConsumer()
    {
        if (extension_loaded('pcntl') && (defined('AMQP_WITHOUT_SIGNALS') ? !AMQP_WITHOUT_SIGNALS : true)) {
            if (!function_exists('pcntl_signal_dispatch')) {
                throw new \BadFunctionCallException(
                    "Function 'pcntl_signal_dispatch' is referenced in the php.ini 'disable_functions' and can't be called."
                );
            }
            pcntl_signal_dispatch();
        }
        if ($this->forceStop) {
            $this->stopConsuming();
        } else {
            return;
        }
    }

    public function stopConsuming()
    {
        $this->getChannel()->basic_cancel($this->consumerTag);
    }


    protected function close(){}

}