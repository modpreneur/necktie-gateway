<?php
namespace Necktie\GatewayBundle\Gateway\RequestProcessor;

/**
 * Class AWeberProcessor
 * @package Necktie\GatewayBundle\Gateway\RequestProcessor
 */
class AWeberProcessor extends BaseProcessor
{

    /**
     * @param array $message
     * @return mixed|void
     */
    public function process(array $message = [])
    {
        $consumerKey    = $message['consumerKey'];
        $consumerSecret = $message['consumerSecret'];
        $accessKey      = $message['accessKey'];
        $accessSecret   = $message['accessSecret'];

        $aweber = new \AWeberAPI($consumerKey, $consumerSecret);
        $account = $aweber->getAccount($accessKey, $accessSecret);

        if(array_key_exists('type', $message) && $message['type'] === 'subscribe') {
            return $this->subscribe($account, $message['attributes']['listId'], $message['data'], $message['tag'], $message['attributes']);
        }

        if(array_key_exists('type', $message) && $message['type'] === 'update') {
            return $this->update($account, $message['attributes']['userId'], $message['data']);
        }

        if(array_key_exists('type', $message) && $message['type'] === 'unsubscribe') {
            return $this->delete($account, $message['attributes']['listId'], $message['attributes']['userId'], $message['tag'], $message['attributes']);
        }
    }


    /**
     * @param $account
     * @param string $listID
     * @param array $data
     * @param $tag
     * @param $attributes
     * @return array
     */
    public function subscribe($account, string $listID, array $data, $tag, $attributes) : array
    {
        $lists = $account->lists->find(array('id' => $listID));
        $list  = $lists[0];

        $subscribers = $list->subscribers;
        try {
            $response = $subscribers->create($data);

            echo 'API OK - AWeber';
            return [
                'status'     => 'ok',
                'id'         => $data['email'],
                'url'        => '',
                'response'   => $response,
                'tag'        => $tag,
                'attributes' => $attributes,
            ];

        } catch (\Exception $ex){
            $response = $ex->getMessage();

            echo 'API ERROR - AWeber ' . $ex->getMessage() . PHP_EOL;
            echo 'Email: ' . $data['email'] . PHP_EOL;
            return [
                'status'     => 'error',
                'id'         => $data['email'],
                'url'        => '',
                'response'   => $response,
                'tag'        => $tag,
                'attributes' => $attributes,
            ];
        }
    }


    /**
     * @param $account
     * @param string $userID
     * @param array $data
     * @return array
     */
    public function update($account, string $userID, array $data)
    {
        $params            = array('status' => 'subscribed', 'id' => $userID);
        $found_subscribers = $account->findSubscribers($params);

        $i = 0;

        foreach($found_subscribers as $subscriber) {

            foreach ($data as $key => $value) {
                $subscriber->{$key} = $value;
                $subscriber->save();
                $i++;
            }
        }

        echo 'API OK - AWeber, user was updated in ' . $i . ' lists.' ;

        return [];
    }


    /**
     * @param $account
     * @param string $listID
     * @param string $userID
     * @param string $tag
     * @param $attributes
     * @return array
     */
    public function delete($account, string $listID, string $userID, string $tag, $attributes)
    {
        $status = '';
        try {
            $acu  = $account->url;
            $user = $account->loadFromUrl("$acu/lists/$listID/subscribers/$userID");
            $res  = $user;
            $user->delete();
            echo 'API OK - AWeber' . PHP_EOL;
            $status = 'ok';

        } catch (\Exception $ex) {
            $status = 'error';
            echo 'API ERROR - AWeber ' . $ex->getMessage() . PHP_EOL;
            $res =  $ex->getMessage();
        }

        return [
            'status'     => $status,
            'url'        => '',
            'response'   => $res,
            'tag'        => $tag,
            'attributes' => $attributes,
        ];
    }

}