<?php
/**
 * @author    Innovation Software Sp.z.o.o
 * @copyright 2018 Innovation Software Sp.z.o.o
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @category  apaczkashipment
 * @package   apaczkashipment
 * @version   1.1
 */

date_default_timezone_set('Europe/Warsaw');

class ApaczkaAPI
{
    public $apiKey = "";
    public $login = "";
    public $password = "";
    public $url_test = "http://test.apaczka.pl/webservice/order";
    public $url_prod = "https://www.apaczka.pl/webservice/order";
    public $wsdl_test = "http://test.apaczka.pl/webservice/order?wsdl";
    public $wsdl_prod = "https://www.apaczka.pl/webservice/order?wsdl";
    public $outputFileName = "XOLTResult.log";
    public $soapError = "";

    private $mode
        = array(
            'trace'      => 1,
            'exceptions' => 0,
            'encoding'   => 'UTF-8'
        );
    protected $client;

    private $isTest = 0;
    private $isVerboseMode = 0;

    public function __construct($login = '', $passwd = '', $apiKey = '')
    {
        if ($login != '' && $passwd != '' && $apiKey != '') {
            $this->apiKey   = $apiKey;
            $this->login    = $login;
            $this->password = $passwd;
        }

        $this->init();
    }

    public function init()
    {
        if ($this->isTest) {
            $this->client = new SoapClient($this->wsdl_test, $this->mode);
            $this->client->__setLocation($this->url_test);
        } else {
            $this->client = new SoapClient($this->wsdl_prod, $this->mode);
            $this->client->__setLocation($this->url_prod);
        }
    }

    public function placeOrder(ApaczkaOrder $order)
    {
        $PlaceOrderRequest                  = array();
        $PlaceOrderRequest['authorization'] = $this->bindAuthorization();
        $PlaceOrderRequest['order']         = $order->getOrder();

        $resp = $this->soapCall(
            "placeOrder",
            array(
                'placeOrder' => array(
                    'PlaceOrderRequest' => $PlaceOrderRequest)
            )
        );

        return $resp;
    }

    public function validateAuthData()
    {
        $validateAuthData = array();
        $validateAuthData['validateAuthData']['Authorization']
                          = $this->bindAuthorization();

        $resp = $this->soapCall("validateAuthData", $validateAuthData);

        return $resp;
    }

    public function getCountries()
    {
        $getCountriesData = array();
        $getCountriesData['getCountries']['CountryRequest']['authorization']
                          = $this->bindAuthorization();

        $resp = $this->soapCall("getCountries", $getCountriesData);

        return $resp;
    }

    public function getCollectiveWaybillDocument($idsArray = false)
    {
        $req                  = array();
        $req['authorization'] = $this->bindAuthorization();
        $req['orderIds']      = array();

        if ($idsArray) {
            if (is_array($idsArray)) {
                $req['orderIds'] = array('long' => $idsArray);
            } else {
                $req['orderIds'] = array('long' => $idsArray);
            }
        }

        $getCollectiveWaybillDocumentData = array();
        $getCollectiveWaybillDocumentData['getCollectiveWaybillDocument']['CollectiveWaybillRequest']
                                          = $req;

        $resp = $this->soapCall(
            "getCollectiveWaybillDocument",
            $getCollectiveWaybillDocumentData
        );

        return $resp;
    }

    public function getWaybillDocument($orderId = false)
    {

        if (! is_numeric($orderId) || ! (int)$orderId > 0) {
            throw new Exception(
                'orderId must be intval: [' . print_r(
                    $orderId,
                    1
                )
                . '] given.'
            );
        }

        $req                  = array();
        $req['authorization'] = $this->bindAuthorization();
        $req['orderId']       = $orderId;

        $getWaybillDocumentData                                         = array();
        $getWaybillDocumentData['getWaybillDocument']['WaybillRequest'] = $req;

        $resp = $this->soapCall("getWaybillDocument", $getWaybillDocumentData);

        return $resp;
    }

    public function getCollectiveTurnInCopyDocument($idsArray = false)
    {
        $req                  = array();
        $req['authorization'] = $this->bindAuthorization();
        $req['orderIds']      = array();

        if ($idsArray) {
            if (is_array($idsArray)) {
                $req['orderIds'] = array('long' => $idsArray);
            } else {
                $req['orderIds'] = array('long' => $idsArray);
            }
        }
        $getCollectiveTurnInCopyDocumentData = array();
        $getCollectiveTurnInCopyDocumentData['getCollectiveTurnInCopyDocument']['CollectiveTurnInCopyRequest']
                                             = $req;

        $resp = $this->soapCall(
            "getCollectiveTurnInCopyDocument",
            $getCollectiveTurnInCopyDocumentData
        );

        return $resp;
    }

    public function soapCall($operation, $SoapBody)
    {
        if (! in_array($operation, array(
            "placeOrder",
            "validateAuthData",
            "getCountries",
            "getCollectiveWaybillDocument",
            "getWaybillDocument",
            "getCollectiveTurnInCopyDocument"
        ))
        ) {
            throw new Exception('Unsupported operation: [' . $operation . ']');
        }

        try {
            $resp = $this->client->__soapCall($operation, $SoapBody);
            //save soap request and response to file

            file_put_contents(
                $this->outputFileName,
                "[" . date('c') . "]\n" . "SoapCall: [$operation]\n",
                FILE_APPEND
            );
            file_put_contents(
                $this->outputFileName,
                "Request: \n" . $this->client->__getLastRequest() . "\n",
                FILE_APPEND
            );
            file_put_contents(
                $this->outputFileName,
                "Response: \n" . $this->client->__getLastResponse() . "\n\n",
                FILE_APPEND
            );
        } catch (Exception $ex) {
            if ($this->isVerboseMode) {
                print_r($ex);
            }

            file_put_contents(
                $this->outputFileName,
                "[" . date('c') . "]\n" . "SoapCall: [$operation]\n",
                FILE_APPEND
            );
            file_put_contents(
                $this->outputFileName,
                "Request: \n" . $this->client->__getLastRequest() . "\n",
                FILE_APPEND
            );
            file_put_contents(
                $this->outputFileName,
                "Response: \n" . $this->client->__getLastResponse() . "\n\n",
                FILE_APPEND
            );

            $this->soapError = $resp;

            return false;
        }

        if ($this->isVerboseMode) {
            echo("\n\n");
            print_r($this->client->__getLastRequest());
            echo("\n\n");
            print_r($this->client->__getLastResponse());
            echo("\n\n");
        }

        return $resp;
    }

    public function bindAuthorization()
    {
        $auth             = array();
        $auth['apiKey']   = $this->apiKey;
        $auth['login']    = $this->login;
        $auth['password'] = $this->password;

        return $auth;
    }

    public function setVerboseMode()
    {
        $this->isVerboseMode = true;
    }

    public function setTestMode()
    {
        $this->isTest = true;
        $this->init();
    }

    public function setProductionMode()
    {
        $this->isTest = false;
        $this->init();
    }
}
