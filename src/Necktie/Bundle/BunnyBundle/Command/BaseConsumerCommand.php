<?php


namespace Necktie\Bundle\BunnyBundle\Command;

use InvalidArgumentException;

use Bunny\Channel;
use Bunny\Client;
use Bunny\Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


abstract class BaseConsumerCommand extends ContainerAwareCommand
{
    /** @var int  */
    protected $t     = null;

    /** @var int  */
    protected $count = 0;

    /** @var string */
    protected $consumerTag;

    /** @var int */
    protected $maxMessagesPerProcess = 20;

    /** @var string  */
    protected $exchange = "necktie_exchange";

    /** @var string  */
    protected $queueName = "";


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        
        $p = $this->getContainer()->get('necktie.listener.message_processor');
        $p->setOutput($output);
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

        $this->queueName = $input->getArgument('consumer');
    }


    protected function configure(){
        parent::configure();

        $this
            ->setName('necktie:rabbit:baseConsumer')
            ->setDescription('Start waiting for messages.')
            ->addArgument(
                'consumer',
                InputArgument::REQUIRED,
                'Consumer name.'
            );

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
            );
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output){

        /** @var Client $c */
        $c  =  $this->getContainer()->get('necktie.bunny.client');
        $ex = $this->exchange;
        $queueName = $this->queueName;
        $queueName = "queue_" . $queueName;


        $ch = $c->connect()->channel();
        $ch->queueDeclare($queueName);
        $ch->exchangeDeclare($ex);
        $ch->queueBind($queueName, $ex);


        $t     = null;
        $count = 1;

        $ch->consume(function (Message $msg, Channel $ch, Client $c) use (&$t, &$count, $input, $output) {

            $output->writeln('[' . (new \DateTime())->format(\DateTime::W3C) . '] <info>Message consumed.</info>');
            $output->writeln('[' . (new \DateTime())->format(\DateTime::W3C) . '] <info>' . ($msg->content) . '</info>');
            $this->consume($msg, $input, $output);

            if ($t === null) {
                $t = microtime(true);
            }

            if ($msg->content === "quit" || $count >= $this->maxMessagesPerProcess) {
                printf("Pid: %s, Count: %s, Time: %.4f\n", getmypid(), $count, microtime(true) - $t);
                $c->stop();
            } else {
                ++$count;
            }

        }, $queueName, "", false, true);

        $c->run();
    }


    public abstract function consume(Message $msg, InputInterface $input, OutputInterface $output);

}
