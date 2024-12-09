<?php
/**
 * redicon.pl
 * @author Patryk <patryk@redicon.pl>
 * @copyright redicon.pl
 * @license redicon.pl
 */

// namespace PrestaShop\Module\Rediconpaypo;
// use PrestaShop\Module\Rediconpaypo\Response\TransactionResponseObject;
// use PrestaShop\Module\Rediconpaypo\Response\StatusValueObject;

require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Response/TransactionResponseObject.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Response/StatusValueObject.php";
require_once _PS_MODULE_DIR_ . "rediconpaypo/api/Helper/PaypoLog.php";


class ApiService
{
    private $settings = [
        'url'    => '',
        'id'     => '',
        'secret' => ''
    ];

    private $accessToken = null;

    private $isPost = false;

    private $post_fields = [];

    private $headers = [];

    private $curl = [];

    private $defaultOptions = [];

    private $customRequest = false;

    public function __construct($settings = [])
    {
        $this->reloadSettings($settings);
    }
  
    private function reloadSettings($settings = [])
    {
        if (!empty($settings)) {
            foreach ($settings as $key => $value) {
                $this->settings[$key] = $value;
            }
        }
    }

    public function setCustomRequest($value)
    {
        $this->customRequest = $value;
        return $this;
    }
    public function setIsPost()
    {
        $this->isPost = true;
        return $this;
    }

    public function setHeaders(String $value)
    {
        $this->headers[] = $value;
        return $this;
    }

    public function setPostFields($key, $value)
    {
        if (is_null($key)) {
            $this->post_fields = $value;
        } else {
            $this->post_fields[$key] = $value;
        }
        
        return $this;
    }

    public function getCurl()
    {
        return $this->curl;
    }

    public function getSetting($key)
    {
        return isset($this->settings[$key]) ? $this->settings[$key] : null;
    }

    public function getResponse($array = true)
    {
        return json_decode($this->curl['response'], $array);
    }

    private function request($url = '')
    {
        $ch = curl_init($url);
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            // CURLOPT_HEADER         => true,
            CURLOPT_FOLLOWLOCATION => true
        ];

        if ($this->customRequest) {
            $options[CURLOPT_CUSTOMREQUEST] = $this->customRequest; //'PATCH'
        }

        if ($this->isPost || !empty($this->post_fields)) {
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = is_array($this->post_fields)?http_build_query($this->post_fields):$this->post_fields;
        }

        if ($this->headers && !empty($this->headers)) {
            $options[CURLOPT_HTTPHEADER] = $this->headers;
        }

        curl_setopt_array($ch, $options);

        $this->curl['response'] = curl_exec($ch);
        $this->curl['info'] = curl_getinfo($ch);
        curl_close($ch);

        return $this;
    }

    public function send(String $url = '')
    {
        if (is_null($this->accessToken)) {
            $token = new self($this->settings);

            $response = $token->setPostFields('grant_type', 'client_credentials')
                            ->setPostFields('client_id', $token->getSetting('id'))
                            ->setPostFields('client_secret', $token->getSetting('secret'))
                            ->request($token->getSetting('url') . 'oauth/tokens');

            if ($responseArray = $response->getResponse()) {
                if (isset($responseArray['access_token'])) {
                    $this->setHeaders('Content-Type: application/json')
                         ->setHeaders(sprintf('Authorization: Bearer %s', $responseArray['access_token']));
                }
            }
        }

        return $this->request($this->settings['url'] . $url);
    }

    public function registerTransaction($registerTransactionPayload)
    {
        $this->setPostFields(null, $registerTransactionPayload)
        ->send('transactions');
        $response = json_decode($this->curl['response'], true);

        if (in_array($this->curl['info']['http_code'], [200, 201])) {
            return new TransactionResponseObject($response['transactionId'], $response['redirectUrl']);
        }
       
        $exception = 'Connection error...';

        if (isset($response['message'])) {
            $exception = $response['message'];
        }

        if (isset($response['message']) && isset($response['errors']) && is_array($response['errors'])) {
            foreach ($response['errors'] as $error) {
                $exception .= ' ' . (isset($error['message']) ? $error['message'] : '');
            }
        }
        $this->curl['post_data'] = $registerTransactionPayload;
        PaypoLog::log(json_encode($this->curl),'response');

        throw new \Exception($exception);
    }

    public function updateTransaction(string $transactionId, $update)
    {
        return $this->setCustomRequest('PATCH')
        ->setPostFields(null, $update)
        ->send(sprintf('transactions/%s', $transactionId));
    }

    public function retrieveTransaction(string $transactionId)
    {
        return $this->send(sprintf('transactions/%s', $transactionId));
    }

    public function refundsTransaction(string $transactionId, $amount)
    {
        $this->setPostFields(null, $amount)
        ->send(sprintf('transactions/%s/refunds', $transactionId));

        $response = json_decode($this->curl['response'], true);

        return new StatusValueObject($this->curl['info']['http_code'], $response['message']);
    }
}
