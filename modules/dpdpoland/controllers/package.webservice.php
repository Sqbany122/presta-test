<?php
/**
* 2014 DPD Polska Sp. z o.o.
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* prestashop@dpd.com.pl so we can send you a copy immediately.
*
*  @author    JSC INVERTUS www.invertus.lt <help@invertus.lt>
*  @copyright 2014 DPD Polska Sp. z o.o.
*  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*  International Registered Trademark & Property of DPD Polska Sp. z o.o.
*/

if (!defined('_PS_VERSION_'))
	exit;

/**
 * Class DpdPolandPackageWS Responsible for management via WebServices
 */
class DpdPolandPackageWS extends DpdPolandWS
{
    /**
     * Current file name
     */
	const FILENAME = 'Package';

    /**
     * @var array Parcels data used for WebServices
     */
	private $parcels = array();

    /**
     * @var array Services data used for WebServices
     */
	private $services = array();

    /**
     * @var array Sender data used for WebServices
     */
	private $sender = array();

    /**
     * @var array Receiver data used for WebServices
     */
	private $receiver = array();

    /**
     * Sets parcels data
     *
     * @param array $parcel Parcel data
     * @param string $additional_info Additional shipment info
     */
	public function addParcel($parcel, $additional_info)
	{
		$parcel = array(
			'content' => $parcel['content'],
			'customerData1' => $additional_info,
			'customerData2' => null,
			'customerData3' => null,
			'reference' => Tools::strtoupper(Tools::passwdGen(9, 'NO_NUMERIC')).'_'.(int)$parcel['number'],
			'sizeX' => (float)$parcel['length'],
			'sizeY' => (float)$parcel['width'],
			'sizeZ' => (float)$parcel['height'],
			'weight' => (float)$parcel['weight']
		);

		$this->parcels[] = $parcel;
	}

    /**
     * Collects error messages from WebServices
     *
     * @param array $response Response from WebServices
     * @param string $error_key Error code
     * @param array $errors Collected errors
     * @return array Error messages
     */
	private function getErrorsByKey($response, $error_key, $errors = array())
	{
		if (!empty($response))
			foreach ($response as $key => $value)
				if (is_object($value) || is_array($value))
					$errors = $this->getErrorsByKey($value, $error_key, $errors);
				elseif ($key == $error_key)
					$errors[] = $value;

		return $errors;
	}

    /**
     * Creates package
     *
     * @param DpdPolandPackage $package_obj Package object
     * @return bool Package created successfully
     */
	public function create(DpdPolandPackage $package_obj)
	{
		if ($result = $this->createRemotely($package_obj))
		{
			if (isset($result['Status']) && $result['Status'] == 'OK')
			{
				$package = $result['Packages']['Package'];
				$package_obj->id_package_ws = (int)$package['PackageId'];
				$package_obj->sessionId = (int)$result['SessionId'];

				if (!$package_obj->save())
					self::$errors[] = $this->l('Package was successfully created but we were unable to save its data locally');

				return $package;
			}
			else
			{
				if (isset($result['Packages']['InvalidFields']))
					$errors = $result['Packages']['InvalidFields'];
				elseif (isset($result['Packages']['Package']['ValidationDetails']))
					$errors = $result['Packages']['Package']['ValidationDetails'];
				elseif (isset($result['faultcode']) && isset($result['faultstring']))
					$errors = $result['faultcode'].' : '.$result['faultstring'];
				else
				{
					$errors = array();

					if ($error_ids = $this->getErrorsByKey($result, 'ErrorId'))
					{
						$language = new DpdPolandLanguage();

						foreach ($error_ids as $id_error)
							$errors[] = $language->getTranslation($id_error);
					}
					elseif ($error_messages = $this->getErrorsByKey($result, 'Info'))
					{
						foreach ($error_messages as $message)
							$errors[] = $message;
					}

					$errors = reset($errors);

					if (!$errors)
						$errors = $this->module_instance->displayName.' : '.$this->l('Unknown error');
				}

				if ($errors)
				{
                    $errors = (array)$errors;
					$errors = (array_values($errors) === $errors) ? $errors : array($errors); // array must be multidimentional

					foreach ($errors as $error)
					{
						if (isset($error['ValidationInfo']['Info']))
							self::$errors[] = $error['ValidationInfo']['Info'];
						elseif (isset($error['info']))
							self::$errors[] = $error['info'];
						elseif (isset($error['ValidationInfo']) && is_array($error['ValidationInfo'])) {
						    $errors_formatted = reset($error['ValidationInfo']);

						    if (isset($errors_formatted['ErrorId'])) {
                                $language = new DpdPolandLanguage();
                                $error_message = $language->getTranslation($errors_formatted['ErrorId']);

                                if (!$error_message) {
                                    $error_message = isset($errors_formatted['Info']) ? $errors_formatted['Info'] :
                                        $this->l('Unknown error occured');
                                }

                                self::$errors[] = $error_message;
                            } elseif (isset($errors_formatted['Info'])) {
                                self::$errors[] = $errors_formatted['Info'];
                            }
                        } else {
						    self::$errors[] = $error;
                        }
					}
				}
				else
					self::$errors[] = $errors;

				return false;
			}
		}

		return false;
	}

    /**
     * Creates package remotely
     *
     * @param DpdPolandPackage $package_obj Package object
     * @param string $payerType Payer type
     * @return bool Package created successfully
     */
	private function createRemotely(DpdPolandPackage $package_obj, $payerType = 'SENDER')
	{
		if (!$this->prepareReceiverAddress($package_obj))
			return false;

		$payer_number = Tools::getValue('dpdpoland_PayerNumber');

		$this->prepareSenderAddress($payer_number, $package_obj->id_sender_address);
		$this->prepareServicesData($package_obj);

		$params = array(
			'openUMLFeV2' => array(
				'packages' => array(
					'parcels' => $this->parcels,
					'payerType' => $payerType,
					'receiver' => $this->receiver,
					'ref1' => $package_obj->ref1,
					'ref2' => $package_obj->ref2,
					'ref3' => _DPDPOLAND_REFERENCE3_,
					'reference' => null,
					'sender' => $this->sender,
					'services' => $this->services,
					'thirdPartyFID' => null
				)
			),
			'pkgNumsGenerationPolicyV1' => 'STOP_ON_FIRST_ERROR',
			'langCode' => 'PL'
		);

		return $this->generatePackagesNumbersV3($params);
	}

    /**
     * Formats receiver address and prepares it to be used via WebServices
     *
     * @param DpdPolandPackage $package_obj Package object
     * @return bool Receiver address prepared without errors
     */
	private function prepareReceiverAddress(DpdPolandPackage $package_obj)
	{
		$address = new Address((int)$package_obj->id_address_delivery);

		if (Validate::isLoadedObject($address))
		{
			$customer = new Customer((int)$address->id_customer);

			if (Validate::isLoadedObject($customer))
			{
				$this->receiver = array(
					'address' => $address->address1.' '.$address->address2,
					'city' => $address->city,
					'company' => $address->company,
					'countryCode' => Country::getIsoById((int)$address->id_country),
					'email' => $customer->email,
					'fid' => null,
					'name' => $address->firstname.' '.$address->lastname,
					'phone' => $address->phone ? $address->phone : $address->phone_mobile,
					'postalCode' => DpdPoland::convertPostcode($address->postcode)
				);
			}
			else
			{
				self::$errors[] = $this->l('Customer does not exists');
				return false;
			}
		}
		else
		{
			self::$errors[] = $this->l('Receiver address does not exists');
			return false;
		}

		return true;
	}

    /**
     * Formats sender address and prepares it to be used via WebServices
     *
     * @param string|int $client_number Client number
     * @param null|int $id_sender_address Address ID
     */
	private function prepareSenderAddress($client_number = 'null', $id_sender_address = null)
	{
	    $sender_address = new DpdPolandSenderAddress((int)$id_sender_address);

		$this->sender = array(
			'address' => $sender_address->address,
			'city' => $sender_address->city,
			'company' => $sender_address->company,
			'countryCode' => DpdPoland::POLAND_ISO_CODE,
			'email' => $sender_address->email,
			'fid' => $client_number,
			'name' => $sender_address->name,
			'phone' => $sender_address->phone,
			'postalCode' => DpdPoland::convertPostcode($sender_address->postcode)
		);
	}

    /**
     * Formats data and prepares it to be used via WebServices
     *
     * @param DpdPolandPackage $package_obj Package object
     */
	private function prepareServicesData(DpdPolandPackage $package_obj)
	{
		if ($package_obj->cod_amount !== null)
		{
			$this->services['cod'] = array(
				'amount' => $package_obj->cod_amount,
				'currency' => _DPDPOLAND_CURRENCY_ISO_
			);
		}

		if ($package_obj->declaredValue_amount !== null)
		{
			$this->services['declaredValue'] = array(
				'amount' => $package_obj->declaredValue_amount,
				'currency' => _DPDPOLAND_CURRENCY_ISO_
			);
		}

        if ($package_obj->cud)
        {
            $this->services['cud'] = 1;
        }

        if ($package_obj->rod)
        {
            $this->services['rod'] = 1;
        }

        // DPD PUDO SERVICE DATA PREPARATION
        $order = new Order($package_obj->id_order);
        // First get pudo carrier id
        $id_pudo_carrier = Configuration::get(DpdPolandConfiguration::CARRIER_PUDO_ID);

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $id_order_carrier = (int)DpdPolandCarrier::getReferenceByIdCarrier((int)$order->id_carrier);
        } else {
            $carrier = new Carrier($order->id_carrier);
            $id_order_carrier = $carrier->id_reference;
        }

        // Check if order has pudo service as carrier
		if ($id_order_carrier == $id_pudo_carrier && Tools::getValue('dpdpoland_SessionType') == 'pudo') {
		    // Get pudo code from pudo_cart mappings table
            $pudoCode = Db::getInstance()->getValue('
                  SELECT `pudo_code`
                  FROM `'._DB_PREFIX_.'dpdpoland_pudo_cart`
                  WHERE `id_cart` = '.(int)$order->id_cart.'
            ');

            if ($pudoCode) {
                $this->services['dpdPickup'] = array(
                    'pudo' => $pudoCode,
                );
            }
        }
	}

    /**
     * Collects and returns sender address
     *
     * @param int|string $client_number Client number
     * @param null|int $id_sender_address Sender address ID
     * @return array Sender address
     */
	public function getSenderAddress($client_number, $id_sender_address = null)
	{
		if (!$this->sender)
			$this->prepareSenderAddress($client_number, $id_sender_address);

		return $this->sender;
	}

    /**
     * Generates multiple labels for selected packages
     *
     * @param array $waybills Packages waybills
     * @param string $outputDocPageFormat Document page format
     * @param string $session_type Session type
     * @return bool Multiple labels generated successfully
     */
	public function generateMultipleLabels($waybills, $outputDocPageFormat, $session_type)
	{
		if (!in_array($outputDocPageFormat, array(DpdPolandConfiguration::PRINTOUT_FORMAT_A4, DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL)))
			$outputDocPageFormat = DpdPolandConfiguration::PRINTOUT_FORMAT_A4;

		$this->prepareSenderAddress();
		
		$session = array(
			'packages' => array(
				'parcels' => array(

				)
			),
			'sessionType' => $session_type
		);
		
		foreach ($waybills as $waybill) {
			$session['packages']['parcels'][] = array('waybill' => $waybill);
		}

		$params = array(
			'dpdServicesParamsV1' => array(
				'policy' => 'IGNORE_ERRORS',
				'session' => $session
			),
			'outputDocFormatV1' => 'PDF',
			'outputDocPageFormatV1' => $outputDocPageFormat,
			'pickupAddress' => $this->sender
		);

		if (!$result = $this->generateSpedLabelsV1($params)) {
			return false;
		}

		if (isset($result['session']) && $result['session']['statusInfo']['status'] == 'OK')
		{
			return $result['documentData'];
		}
		else
		{
			if (isset($result['session']['statusInfo']['status'])) {
				self::$errors[] = $result['session']['statusInfo']['status'];

				return false;
			}

			$error = isset($result['session']['packages']['statusInfo']['description']) ?
				$result['session']['packages']['statusInfo']['description'] :
				$result['session']['statusInfo']['description'];
			self::$errors[] = $error;

			return false;
		}
	}

    /**
     * Generates labels for package
     *
     * @param DpdPolandPackage $package Package object
     * @param string $outputDocFormat Document format
     * @param string $outputDocPageFormat Document page format
     * @param string $policy Policy type
     * @return bool Labels generated successfully
     */
	public function generateLabels(DpdPolandPackage $package, $outputDocFormat, $outputDocPageFormat, $policy)
	{
		if (!in_array($outputDocPageFormat, array(DpdPolandConfiguration::PRINTOUT_FORMAT_A4, DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL)))
			$outputDocPageFormat = DpdPolandConfiguration::PRINTOUT_FORMAT_A4;

		$this->prepareSenderAddress();

		$params = array(
			'dpdServicesParamsV1' => array(
				'policy' => $policy,
				'session' => array(
					'sessionId' => (int)$package->sessionId,
					'sessionType' => $package->getSessionType()
				)
			),
			'outputDocFormatV1' => $outputDocFormat,
			'outputDocPageFormatV1' => $outputDocPageFormat,
			'pickupAddress' => $this->sender
		);

		if (!$result = $this->generateSpedLabelsV1($params))
			return false;

		if (isset($result['session']) && $result['session']['statusInfo']['status'] == 'OK')
		{
			$package->labels_printed = 1;
			$package->update();
			return $result['documentData'];
		}
		else
		{
			if (isset($result['session']['statusInfo']['status'])) {
				self::$errors[] = $result['session']['statusInfo']['status'];
				
				return false;
			}

			$error = isset($result['session']['packages']['statusInfo']['description']) ?
				$result['session']['packages']['statusInfo']['description'] :
				$result['session']['statusInfo']['description'];
			self::$errors[] = $error;

			return false;
		}
	}

    /**
     * Generates multiple labels for selected packages
     *
     * @param array $package_ids Packages IDs
     * @param string $outputDocFormat Document format
     * @param string $outputDocPageFormat Document page format
     * @param string $policy Policy type
     * @return bool Labels generated successfully
     */
	public function generateLabelsForMultiplePackages($package_ids, $outputDocFormat, $outputDocPageFormat, $policy)
	{
		$sessionType = '';
		$packages = array();

		foreach ($package_ids as $id_package_ws)
		{
			$package = new DpdPolandPackage((int)$id_package_ws);

			if (!$sessionType || $sessionType == $package->getSessionType())
				$sessionType = $package->getSessionType();
			else
			{
				self::$errors[] = $this->l('Manifests of DOMESTIC shipments cannot be mixed with INTERNATIONAL shipments');
				return false;
			}

			$packages[] = array(
				'packageId' => (int)$id_package_ws
			);
		}

		$this->prepareSenderAddress();

		$params = array(
			'dpdServicesParamsV1' => array(
				'policy' => $policy,
				'session' => array(
					'packages' => $packages,
					'sessionType' => $sessionType
				)
			),
			'outputDocFormatV1' => $outputDocFormat,
			'outputDocPageFormatV1' => $outputDocPageFormat,
			'pickupAddress' => $this->sender
		);

		if (!$result = $this->generateSpedLabelsV1($params))
			return false;

		if (isset($result['session']['statusInfo']['status']) && $result['session']['statusInfo']['status'] == 'OK')
		{
			foreach ($packages as $id_package_ws)
			{
				$package = new DpdPolandPackage($id_package_ws);
				$package->labels_printed = 1;
				$package->update();
			}

			return $result['documentData'];
		}
		else
		{
			$packages = $result['session']['statusInfo'];
			$packages = (array_values($packages) === $packages) ? $packages : array($packages); // array must be multidimentional

			foreach ($packages as $package)
				if (isset($package['description']))
					self::$errors[] = $package['description'];
				elseif (isset($package['status']))
					self::$errors[] = $package['status'];

			return false;
		}
	}
}