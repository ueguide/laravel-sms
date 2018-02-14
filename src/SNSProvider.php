<?php

namespace TheLHC\SMS;

use Aws\Sns\SnsClient;

class SNSProvider
{
    protected $config;

    /**
     * AWS SNS client from SDK
     *
     * @var AmazonSNSClient
     */
    private $client;

    public function __construct($config)
    {
        $this->config = $config;
        $this->client = SnsClient::factory([
            'region' => $config['region'],
            'version'=> $config['version']
        ]);
    }

    public function send($message, $phoneNumber, $senderId = null)
    {
        if ( is_null($senderId) ) {
            $senderId = $this->config['sender_id'];
        }
        // strip phoneNumber to digits only
        $phone = (string)preg_replace('#\D#', '', $phoneNumber);

        $result = $this->client->publish([
            'Message' => $message,
            'MessageAttributes' => array(
                // Associative array of custom 'String' key names
                'AWS.SNS.SMS.SenderID' => array(
                    // DataType is required
                    'DataType' => 'String',
                    'StringValue' => $senderId
                ),
                'AWS.SNS.SMS.SMSType' => array(
                    // DataType is required
                    'DataType' => 'String',
                    'StringValue' => 'Transactional'
                ),
            ),
            'PhoneNumber' => $phone,
        ]);

        return $result->get('MessageId');
    }
}
