<?php
/**
 * @author    Innovation Software Sp.z.o.o
 * @copyright 2018 Innovation Software Sp.z.o.o
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @category  apaczkashipment
 * @package   apaczkashipment
 * @version   1.1
 */

class ApaczkaOrderShipment
{

    public $dimension1 = '';
    public $dimension2 = '';
    public $dimension3 = '';
    public $weight = '';
    private $shipmentTypeCode = '';
    private $shipmentValue = '';
    private $options = '';
    private $position = 0;

    private static $dictShipmentTypeCode = array('LIST', 'PACZ', 'PALETA');

    public function __construct(
        $shipmentTypeCode = '',
        $dim1 = '',
        $dim2 = '',
        $dim3 = '',
        $weight = ''
    ) {
        if ($shipmentTypeCode == 'LIST') {
            $this->createShipment($shipmentTypeCode, 0, 0, 0, 0);
        } else {
            if ($dim1 != '' && $dim2 != '' && $dim3 != '' && $weight != ''
                && $shipmentTypeCode != ''
            ) {
                $this->createShipment(
                    $shipmentTypeCode,
                    $dim1,
                    $dim2,
                    $dim3,
                    $weight
                );
            }
        }
    }

    public function getShipmentTypeCode()
    {
        return $this->shipmentTypeCode;
    }

    public function setShipmentTypeCode($shipmentTypeCode)
    {
        if (! in_array($shipmentTypeCode, self::$dictShipmentTypeCode)) {
            throw new Exception('UNSUPPORTED service code: ['
                                . $shipmentTypeCode . '] must be one of: '
                                . print_r(self::$dictShipmentTypeCode, 1));
        }

        $this->shipmentTypeCode = $shipmentTypeCode;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function addOrderOption($option)
    {
        if ($this->options == "") {
            $this->options = array('string' => $option);
        } elseif (! is_array($this->options['string'])) {
            $tmp_option = $this->options['string'];

            if ($tmp_option != $option) {
                $this->options['string'] = array($tmp_option, $option);
            }
        } else {
            $this->options['string'][] = $option;
        }
    }

    public function getShipmentValue()
    {
        return $this->shipmentValue;
    }

    public function setShipmentValue($value)
    {
        if (! $value > 0) {
            throw new Exception('UNSUPPORTED ShipmentValue: [' . $value
                                . '] ShipmentValue must be greater then 0');
        }

        $this->shipmentValue = $value;
        $this->addOrderOption('UBEZP');
    }

    public function createShipment(
        $shipmentTypeCode,
        $dim1 = '',
        $dim2 = '',
        $dim3 = '',
        $weight = ''
    ) {

        $this->setShipmentTypeCode($shipmentTypeCode);

        $this->dimension1 = $dim1;
        $this->dimension2 = $dim2;
        $this->dimension3 = $dim3;

        $this->weight = $weight;
    }
}
