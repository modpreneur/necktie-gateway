<?php
namespace Necktie\Bundle\GatewayBundle\Gateway\RequestProcessor;

/**
 * Class AWeberProcessor
 * @package Necktie\Bundle\GatewayBundle\Gateway\RequestProcessor
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
            return $this->subscribe($account, $message['listID'], $message['data']);
        }

        if(array_key_exists('type', $message) && $message['type'] === 'update') {
            return $this->update($account, $message['userID'], $message['data']);
        }

        if(array_key_exists('type', $message) && $message['type'] === 'unsubscribe') {
            return $this->delete($account, $message['listID'], $message['userID']);
        }
    }


    /**
     * @param $account
     * @param string $listID
     * @param array $data
     * @return array
     */
    public function subscribe($account, string $listID, array $data) : array
    {
        $lists = $account->lists->find(array('id' => $listID));
        $list  = $lists[0];

        $subscribers = $list->subscribers;
        $response    = $subscribers->create($data);

        echo 'API OK - AWeber';
        return ['id' => $data['email'], 'response' => $response ];
    }


    /**
     * @param $account
     * @param string $userID
     * @param array $data
     */
    public function update($account, string $userID, array $data)
    {
        // ? search by tag
        $params            = array('email' => $userID);
        $found_subscribers = $account->findSubscribers($params);

        foreach($found_subscribers as $subscriber) {
            foreach ($data as $key => $value) {
                $subscriber->{$key} = $value;
                $subscriber->save();
            }
        }
    }


    /**
     * @param $account
     * @param string $listID
     * @param string $userID
     */
    public function delete($account, string $listID, string $userID)
    {
        $lists = $account->lists->find(array('id' => $listID));
        $list  = $lists[0];

        $list->find(['email' => $userID])[0]->delete();
    }

}