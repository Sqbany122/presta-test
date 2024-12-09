<?php

class exportProduct
{
  private $_context;
  private $_idShop;
  private $_idLang;
  private $_shopGroupId;
  private $_format;
  private $_model;
  private $_PHPExcel;
  private $_alphabet;
  private $_head;
  private $_separate;
  private $_more_settings;
  private $_name_file;
  private $_imageType;
  private $_productsCount;
  private $_limit;
  private $_limitN = 1000;
  private $_productIdCount = false;
  private $_connID;
  private $_distinctAttributes = array();
  private $_maxImages = 0;
  private $_distinctFeatures = array();
  private $_categoryTreesCount = 0;
  private $_parentCategories = array();
  private $_xml_head = array();

  public function __construct($idShop, $idLang, $format, $separate, $more_settings, $name_file)
  {
    include_once(dirname(__FILE__) . '/../../config/config.inc.php');
    include_once(dirname(__FILE__) . '/../../init.php');


    if (version_compare(_PS_VERSION_, '1.6.0.0') >= 0 && version_compare(_PS_VERSION_, '1.7.0.0') < 0) {
      include_once(_PS_MODULE_DIR_ . 'exportproducts/libraries/PHPExcel_1.7.9/Classes/PHPExcel.php');
      include_once(_PS_MODULE_DIR_ . 'exportproducts/libraries/PHPExcel_1.7.9/Classes/PHPExcel/IOFactory.php');
    }


    include_once('datamodel.php');
    $this->_context = Context::getContext();
    $this->_idShop = $idShop;
    $this->_idLang = $idLang;
    $this->_format = $format;
    $this->_separate = $separate;
    $this->_more_settings = $more_settings;
    $this->_name_file = $name_file;
    $imageTypes = ImageType::getImagesTypes('products');
    if (isset(Context::getContext()->shop->id_shop_group)) {
      $this->_shopGroupId = Context::getContext()->shop->id_shop_group;
    } elseif (isset(Context::getContext()->shop->id_group_shop)) {
      $this->_shopGroupId = Context::getContext()->shop->id_group_shop;
    }
    foreach ($imageTypes as $type) {
      if ($type['height'] > 150) {
        $this->_imageType = $type['name'];
        break;
      }
    }
    $this->_model = new productsExportModel();
    $this->_PHPExcel = new PHPExcel();
    $this->_alphabet = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
      'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
      'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
      'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
      'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ',
      'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ',
      'FA', 'FB', 'FC', 'FD', 'FE', 'FF', 'FG', 'FH', 'FI', 'FJ', 'FK', 'FL', 'FM', 'FN', 'FO', 'FP', 'FQ', 'FR', 'FS', 'FT', 'FU', 'FV', 'FW', 'FX', 'FY', 'FZ',
      'GA', 'GB', 'GC', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GJ', 'GK', 'GL', 'GM', 'GN', 'GO', 'GP', 'GQ', 'GR', 'GS', 'GT', 'GU', 'GV', 'GW', 'GX', 'GY', 'GZ',
      'HA', 'HB', 'HC', 'HD', 'HE', 'HF', 'HG', 'HH', 'HI', 'HJ', 'HK', 'HL', 'HM', 'HN', 'HO', 'HP', 'HQ', 'HR', 'HS', 'HT', 'HU', 'HV', 'HW', 'HX', 'HY', 'HZ',
      'IA', 'IB', 'IC', 'ID', 'IE', 'IF', 'IG', 'IH', 'II', 'IJ', 'IK', 'IL', 'IM', 'IN', 'IO', 'IP', 'IQ', 'IR', 'IS', 'IT', 'IU', 'IV', 'IW', 'IX', 'IY', 'IZ',
      'JA', 'JB', 'JC', 'JD', 'JE', 'JF', 'JG', 'JH', 'JI', 'JJ', 'JK', 'JL', 'JM', 'JN', 'JO', 'JP', 'JQ', 'JR', 'JS', 'JT', 'JU', 'JV', 'JW', 'JX', 'JY', 'JZ',
      'KA', 'KB', 'KC', 'KD', 'KE', 'KF', 'KG', 'KH', 'KI', 'KJ', 'KK', 'KL', 'KM', 'KN', 'KO', 'KP', 'KQ', 'KR', 'KS', 'KT', 'KU', 'KV', 'KW', 'KX', 'KY', 'KZ',
      'LA', 'LB', 'LC', 'LD', 'LE', 'LF', 'LG', 'LH', 'LI', 'LJ', 'LK', 'LL', 'LM', 'LN', 'LO', 'LP', 'LQ', 'LR', 'LS', 'LT', 'LU', 'LV', 'LW', 'LX', 'LY', 'LZ',
      'MA', 'MB', 'MC', 'MD', 'ME', 'MF', 'MG', 'MH', 'MI', 'MJ', 'MK', 'ML', 'MM', 'MN', 'MO', 'MP', 'MQ', 'MR', 'MS', 'MT', 'MU', 'MV', 'MW', 'MX', 'MY', 'MZ',
      'NA', 'NB', 'NC', 'ND', 'NE', 'NF', 'NG', 'NH', 'NI', 'NJ', 'NK', 'NL', 'NM', 'NN', 'NO', 'NP', 'NQ', 'NR', 'NS', 'NT', 'NU', 'NV', 'NW', 'NX', 'NY', 'NZ',
      'OA', 'OB', 'OC', 'OD', 'OE', 'OF', 'OG', 'OH', 'OI', 'OJ', 'OK', 'OL', 'OM', 'ON', 'OO', 'OP', 'OQ', 'OR', 'OS', 'OT', 'OU', 'OV', 'OW', 'OX', 'OY', 'OZ',
      'PA', 'PB', 'PC', 'PD', 'PE', 'PF', 'PG', 'PH', 'PI', 'PJ', 'PK', 'PL', 'PM', 'PN', 'PO', 'PP', 'PQ', 'PR', 'PS', 'PT', 'PU', 'PV', 'PW', 'PX', 'PY', 'PZ',
      'QA', 'QB', 'QC', 'QD', 'QE', 'QF', 'QG', 'QH', 'QI', 'QJ', 'QK', 'QL', 'QM', 'QN', 'QO', 'QP', 'QQ', 'QR', 'QS', 'QT', 'QU', 'QV', 'QW', 'QX', 'QY', 'QZ',
      'RA', 'RB', 'RC', 'RD', 'RE', 'RF', 'RG', 'RH', 'RI', 'RJ', 'RK', 'RL', 'RM', 'RN', 'RO', 'RP', 'RQ', 'RR', 'RS', 'RT', 'RU', 'RV', 'RW', 'RX', 'RY', 'RZ',
      'SA', 'SB', 'SC', 'SD', 'SE', 'SF', 'SG', 'SH', 'SI', 'SJ', 'SK', 'SL', 'SM', 'SN', 'SO', 'SP', 'SQ', 'SR', 'SS', 'ST', 'SU', 'SV', 'SW', 'SX', 'SY', 'SZ',
    );
  }

  public function exportProducts($limit = 0)
  {
    if ($this->_more_settings['feed_target'] == 'ftp') {
      $conn_id = ftp_connect($this->_more_settings['ftp_server']);
      $this->_connID = $conn_id;
      if (!$conn_id) {
        throw new Exception(Module::getInstanceByName('exportproducts')->l('Can not connect to your FTP Server!', 'export'));
      }

      $login_result = @ftp_login($conn_id, $this->_more_settings['ftp_user'], $this->_more_settings['ftp_password']);

      if (!$login_result) {
        throw new Exception(Module::getInstanceByName('exportproducts')->l('Can not Login to your FTP Server, please check access!', 'export'));
      }
    }
    $this->_limit = $limit;
    if (!$limit) {
      Configuration::updateValue('EXPORT_PRODUCTS_TIME', Date('Y.m.d_G-i-s'), false, $this->_shopGroupId, $this->_idShop);
      $this->_productsCount = $this->_model->getExportIds($this->_idShop, $this->_idLang, $this->_separate, $this->_more_settings, $limit, $this->_limitN, true);
      Configuration::updateValue('EXPORT_PRODUCTS_COUNT', $this->_productsCount, false, $this->_shopGroupId, $this->_idShop);
      Configuration::updateValue('EXPORT_PRODUCTS_CURRENT_COUNT', 0, false, $this->_shopGroupId, $this->_idShop);

      if (!$this->_productsCount) {
        throw new Exception(Module::getInstanceByName('exportproducts')->l('No of matching products', 'export'));
      }
    } else {
      $more = $this->_more_settings;
      $delimiter = $more['delimiter_val'];
      $seperatop = $more['seperatop_val'];

      if ($delimiter == 'space') {
        $delimiter = ' ';
      }
      if ($delimiter == 'tab') {
        $delimiter = "\t";
      }
      if ($seperatop == 3) {
        $sep = '';
      } elseif ($seperatop == 2) {
        $sep = "'";
      } else {
        $sep = '"';
      }
      if ($this->_format == 'xlsx' || $this->_format == 'xls') {
        $this->_PHPExcel = PHPExcel_IOFactory::load('files/export_products_' . Configuration::get('EXPORT_PRODUCTS_TIME', '', $this->_shopGroupId, $this->_idShop) . ((int)$limit - 1) . '.' . $this->_format);
      }
      if ($this->_format == 'csv') {
        $reader = PHPExcel_IOFactory::createReader("CSV");
        $reader->setDelimiter($delimiter);
        $reader->setEnclosure($sep);
        $this->_PHPExcel = $reader->load('files/export_products_' . Configuration::get('EXPORT_PRODUCTS_TIME', '', $this->_shopGroupId, $this->_idShop) . ((int)$limit - 1) . '.' . $this->_format);
      }
      $this->_productsCount = Configuration::get('EXPORT_PRODUCTS_COUNT', '', $this->_shopGroupId, $this->_idShop);
    }
    $productIds = $this->_model->getExportIds($this->_idShop, $this->_idLang, $this->_separate, $this->_more_settings, $this->_limit, $this->_limitN);
    $selected_fields = Tools::unserialize(Configuration::get('GOMAKOIL_FIELDS_CHECKED', '', $this->_shopGroupId, $this->_idShop));
    if (isset($selected_fields['combinations_value'])) {
      $this->_distinctAttributes = $this->_model->getExportIds($this->_idShop, $this->_idLang, $this->_separate, $this->_more_settings, $this->_limit, $this->_limitN, false, true);
    }
    if (isset($selected_fields['images_value'])) {
      $this->_maxImages = $this->_model->getExportIds($this->_idShop, $this->_idLang, $this->_separate, $this->_more_settings, $this->_limit, $this->_limitN, false, false, true);
    }

    if (isset($selected_fields['separated_categories'])) {
      $allProductIds = $this->_model->getExportIds($this->_idShop, $this->_idLang, $this->_separate, $this->_more_settings, $this->_limit, $this->_limitN, false, false, false, true);

      foreach ($allProductIds as $productId) {
        $this->_parentCategories = array();
        $categories = $this->_getWsCategories($productId['id_product']);

        $currentCount = 0;
        foreach ($categories as $category) {
          if ($this->_getCategoryTree($category['id'])) {
            $currentCount++;
          }
        }
        if ($this->_categoryTreesCount < $currentCount) {
          $this->_categoryTreesCount = $currentCount;
        }
      }
    }

    if (isset($selected_fields['features'])) {
      $this->_distinctFeatures = $this->_model->getExportIds($this->_idShop, $this->_idLang, $this->_separate, $this->_more_settings, $this->_limit, $this->_limitN, false, false, false, false, true);
    }

    return $this->_getProductsData($productIds);
  }

  private function _getCategoryTree($categoryId, $level = array())
  {
    $catInfo = new Category($categoryId, $this->_idLang);
    if (in_array($categoryId, $this->_parentCategories) && !$level) {
      return false;
    }
    if ($level) {
      $this->_parentCategories[] = $categoryId;
    }

    if ($catInfo->id_parent) {
      $level[] = $catInfo->name;
      return $this->_getCategoryTree($catInfo->id_parent, $level);
    }

    return array_reverse($level);
  }

  private function _getWsCategories($productId)
  {
    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
      'SELECT cp.`id_category` AS id
			FROM `' . _DB_PREFIX_ . 'category_product` cp
			LEFT JOIN `' . _DB_PREFIX_ . 'category` c ON (c.id_category = cp.id_category)
			' . Shop::addSqlAssociation('category', 'c') . '
			WHERE cp.`id_product` = ' . (int)$productId . '
      ORDER BY c.level_depth DESC'
    );
    return $result;
  }

  private function _getProductsData($productIds)
  {
    $date = Configuration::get('EXPORT_PRODUCTS_TIME', '', $this->_shopGroupId, $this->_idShop);
    $name_file = 'export_products_' . $date . '.' . $this->_format;
    if ($this->_name_file) {
      $name_file = $this->_name_file . '.' . $this->_format;
    }

    $more = $this->_more_settings;
    if ($more['display_headers']) {
      $line = 2;
    } else {
      $line = 1;
    }

    if ($this->_limit) {

      foreach ($this->_PHPExcel->getWorksheetIterator() as $worksheet) {
        $highestRow = $worksheet->getHighestRow();
        break;
      }
      $line = $highestRow + 1;
    }
    $this->_createHead();

    if ($this->_format == 'xml') {
      if (!$this->_limit) {
        $write_fd = fopen('files/' . $name_file, 'w');
        if (@$write_fd !== false)
          fwrite($write_fd, '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n<" . Module::getInstanceByName('exportproducts')->l('products', 'export') . ">\r\n");
      } else {
        $write_fd = fopen('files/' . $name_file, 'a');
      }
    } else {
      $write_fd = false;
    }

    foreach ($productIds as $prodId) {
      $productId = $prodId['id_product'];

      if ($this->_separate) {
        $productAttributeId = $prodId['id_product_attribute'];
        $this->_setProductInFile($this->_getProductById($productId, $productAttributeId), $line, $write_fd);
        $line++;
      } else {
        $this->_setProductInFile($this->_getProductById($productId, false), $line, $write_fd);
        $line++;
      }

      $currentExported = Configuration::get('EXPORT_PRODUCTS_CURRENT_COUNT', '', $this->_shopGroupId, Context::getContext()->shop->id);
      Configuration::updateValue('EXPORT_PRODUCTS_CURRENT_COUNT', ((int)$currentExported + 1), false, $this->_shopGroupId, Context::getContext()->shop->id);

    }

    if ((int)$this->_productsCount <= ((int)$this->_limit * (int)$this->_limitN) + (int)$this->_limitN) {
      if ($this->_format == 'xml') {
        if (@$write_fd !== false) {
          fwrite($write_fd, '</' . Module::getInstanceByName('exportproducts')->l('products', 'export') . '>' . "\r\n");
          fclose($write_fd);

          if ($this->_more_settings['feed_target'] == 'ftp') {
            $path = '';
            if ($this->_more_settings['ftp_folder_path']) {
              $path = $this->_more_settings['ftp_folder_path'] . '/';
              $path = str_replace('//', '/', $path);
            }

            $ftpUpload = ftp_put($this->_connID, $path . $name_file, 'files/' . $name_file, FTP_ASCII);
            if (!$ftpUpload) {
              throw new Exception(Module::getInstanceByName('exportproducts')->l('Can not upload export file to your FTP Server, please check ftp folder path and folder permissions!', 'export'));
            }
          }
        }
      } else {
        $this->_setStyle($line);
      }
    }

    $fileName = $this->_saveFile($name_file);
    return $fileName;
  }

  private function _setStyle($line)
  {
    $i = $line;
    $j = count($this->_head);
    $more = $this->_more_settings;

    if ($more['display_headers']) {
      $style_hprice = array(
        'alignment' => array(
          'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER,
        ),
        'fill' => array(
          'type' => PHPExcel_STYLE_FILL::FILL_SOLID,
          'color' => array(
            'rgb' => 'CFCFCF'
          )
        ),
        'font' => array(
          'bold' => true,
          'italic' => true,
          'name' => 'Times New Roman',
          'size' => 13
        ),
      );
      $this->_PHPExcel->getActiveSheet()->getStyle('A1:' . $this->_alphabet[$j - 1] . '1')->applyFromArray($style_hprice);
    } else {
      $style_hprice = array(
        'alignment' => array(
          'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_LEFT,
          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'fill' => array(
          'type' => PHPExcel_STYLE_FILL::FILL_SOLID,
          'color' => array(
            'rgb' => 'F2F2F5'
          )
        ),
      );
      $this->_PHPExcel->getActiveSheet()->getStyle('A1:' . $this->_alphabet[$j - 1] . '1')->applyFromArray($style_hprice);
    }

    $style_wrap = array(
      //рамки
      'borders' => array(
        //внешняя рамка
        'outline' => array(
          'style' => PHPExcel_Style_Border::BORDER_THICK
        ),
        //внутренняя
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN,
          'color' => array(
            'rgb' => '696969'
          )
        )
      )
    );
    $this->_PHPExcel->getActiveSheet()->getStyle('A1:' . $this->_alphabet[$j - 1] . ($i - 1))->applyFromArray($style_wrap);

    $style_price = array(
      'alignment' => array(
        'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_LEFT,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
      )
    );
    $this->_PHPExcel->getActiveSheet()->getStyle('A2:' . $this->_alphabet[$j - 1] . ($i - 1))->applyFromArray($style_price);

    $style_background1 = array(
      //заполнение цветом
      'fill' => array(
        'type' => PHPExcel_STYLE_FILL::FILL_SOLID,
        'color' => array(
          'rgb' => 'F2F2F5'
        )
      ),
    );
    $this->_PHPExcel->getActiveSheet()->getStyle('A2:' . $this->_alphabet[$j - 1] . ($i - 1))->applyFromArray($style_background1);
  }

  private function _getImageObject($mime, $image)
  {
    switch (Tools::strtolower($mime['mime'])) {
      case 'image/png':
        $img_r = imagecreatefrompng($image);
        break;
      case 'image/jpeg':
        $img_r = imagecreatefromjpeg($image);
        break;
      case 'image/gif':
        $img_r = imagecreatefromgif($image);
        break;
      default:
        $img_r = imagecreatefrompng($image);;
    }

    return $img_r;
  }

  private function _setProductInFile($product, $line, $write_fd)
  {
    $i = 0;
    if ($this->_format == 'xml') {
      fwrite($write_fd, "\t" . '<' . Module::getInstanceByName('exportproducts')->l('product', 'export') . '>' . "\r\n");
    }
    foreach ($this->_head as $field => $name) {
      if ($this->_format == 'xlsx' || $this->_format == 'csv') {
        if ($field == 'image_cover') {
          if (($mime = @getimagesize($product[$field]))) {
            $gdImage = $this->_getImageObject($mime, $product[$field]);
            $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
            $objDrawing->setImageResource($gdImage);
            $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
            $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
            $objDrawing->setHeight(150);
            $objDrawing->setOffsetX(6);
            $objDrawing->setOffsetY(6);
            $objDrawing->setCoordinates($this->_alphabet[$i] . $line);
            $objDrawing->setWorksheet($this->_PHPExcel->getActiveSheet());
            $this->_PHPExcel->getActiveSheet()->getRowDimension($line)->setRowHeight(121);
            $this->_PHPExcel->getActiveSheet()->getColumnDimension($this->_alphabet[$i])->setWidth(23);
          }
        } else {
          $this->_PHPExcel->setActiveSheetIndex(0)->setCellValueExplicit($this->_alphabet[$i] . $line, isset($product[$field]) ? $product[$field] : '', PHPExcel_Cell_DataType::TYPE_STRING);
        }
      } else {
        $fieldName = $this->_getXmlHead($field);

        if (strpos($field, 'Attribute_') !== false) {
          $fieldName = $name;
        }
//        $field = str_replace(':','', $field);
//        $field = str_replace(' ','_', $field);
//        $field = str_replace("'",'', $field);
//        $field = str_replace('"','', $field);

        $fieldName = str_replace(':', '', $fieldName);
        $fieldName = str_replace(' ', '_', $fieldName);
        $fieldName = str_replace("'", '', $fieldName);
        $fieldName = str_replace('"', '', $fieldName);


        if (strpos($field, 'images_value_') !== false) {
          if (isset($product[$field]) && $product[$field]) {
            fwrite($write_fd, "\t\t" . '<' . str_replace('images_value_', 'images_', $fieldName) . '>');
          }
        } else {
          fwrite($write_fd, "\t\t" . '<' . $fieldName . '>');
        }

        if (isset($product[$field]) && $product[$field]) {
          fwrite($write_fd, '<![CDATA[');
          fwrite($write_fd, isset($product[$field]) ? @$product[$field] : '');
          fwrite($write_fd, ']]>');
        }

        if (strpos($field, 'images_value_') !== false) {
          if (isset($product[$field]) && $product[$field]) {
            fwrite($write_fd, '</' . str_replace('images_value_', 'images_', $fieldName) . '>' . "\r\n");
          }
        } else {
          fwrite($write_fd, '</' . $fieldName . '>' . "\r\n");
        }


      }
      $i++;
    }
    if ($this->_format == 'xml') {
      fwrite($write_fd, "\t" . '</' . Module::getInstanceByName('exportproducts')->l('product', 'export') . '>' . "\r\n");
    }
  }

  private function _saveFile($name_file)
  {
    $more = $this->_more_settings;

    $delimiter = $more['delimiter_val'];
    $seperatop = $more['seperatop_val'];

    if (isset($more['settings']) && $more['settings'] && isset($more['automatic']) && $more['automatic']) {
      $not_exported = $more['not_exported'];
      $id_setting = $more['settings'];
    } else {
      $not_exported = false;
      $id_setting = false;
    }
    if ($delimiter == 'space') {
      $delimiter = ' ';
    }
    if ($delimiter == 'tab') {
      $delimiter = "\t";
    }

    if ($seperatop == 3) {
      $sep = ' ';
    } elseif ($seperatop == 2) {
      $sep = "'";
    } else {
      $sep = '"';
    }
    if ($this->_format == 'xlsx') {
      $objWriter = PHPExcel_IOFactory::createWriter($this->_PHPExcel, 'Excel2007');

      if ((int)$this->_productsCount <= ((int)$this->_limit * (int)$this->_limitN) + (int)$this->_limitN) {
        $objWriter->save('files/' . $name_file);
        if ($this->_more_settings['feed_target'] == 'ftp') {
          $path = '';
          if ($this->_more_settings['ftp_folder_path']) {
            $path = $this->_more_settings['ftp_folder_path'] . '/';
            $path = str_replace('//', '/', $path);
          }

          $ftpUpload = ftp_put($this->_connID, $path . $name_file, 'files/' . $name_file, FTP_ASCII);
          if (!$ftpUpload) {
            throw new Exception(Module::getInstanceByName('exportproducts')->l('Can not upload export file to your FTP Server, please check ftp folder path and folder permissions!', 'export'));
          }
        }
        for ($l = 0; $l < (int)$this->_limit; $l++) {
          if (file_exists('files/export_products_' . Configuration::get('EXPORT_PRODUCTS_TIME', '', $this->_shopGroupId, $this->_idShop) . ((int)$l) . '.' . $this->_format)) {
            unlink('files/export_products_' . Configuration::get('EXPORT_PRODUCTS_TIME', '', $this->_shopGroupId, $this->_idShop) . ((int)$l) . '.' . $this->_format);
          }
        }
      } else {
        $objWriter->save('files/export_products_' . Configuration::get('EXPORT_PRODUCTS_TIME', '', $this->_shopGroupId, $this->_idShop) . $this->_limit . '.' . $this->_format);
      }
    } elseif ($this->_format == 'csv') {
      $objWriter = PHPExcel_IOFactory::createWriter($this->_PHPExcel, 'CSV');
      $objWriter->setDelimiter($delimiter);
      $objWriter->setEnclosure($sep);
      $objWriter->setUseBOM(true);
      if ((int)$this->_productsCount <= ((int)$this->_limit * (int)$this->_limitN) + (int)$this->_limitN) {
        $objWriter->save('files/' . $name_file);
        if ($this->_more_settings['feed_target'] == 'ftp') {
          $path = '';
          if ($this->_more_settings['ftp_folder_path']) {
            $path = $this->_more_settings['ftp_folder_path'] . '/';
            $path = str_replace('//', '/', $path);
          }

          $ftpUpload = ftp_put($this->_connID, $path . $name_file, 'files/' . $name_file, FTP_ASCII);
          if (!$ftpUpload) {
            throw new Exception(Module::getInstanceByName('exportproducts')->l('Can not upload export file to your FTP Server, please check ftp folder path and folder permissions!', 'export'));
          }
        }
        for ($l = 0; $l < (int)$this->_limit; $l++) {
          if (file_exists('files/export_products_' . Configuration::get('EXPORT_PRODUCTS_TIME', '', $this->_shopGroupId, $this->_idShop) . ((int)$l) . '.' . $this->_format)) {
            unlink('files/export_products_' . Configuration::get('EXPORT_PRODUCTS_TIME', '', $this->_shopGroupId, $this->_idShop) . ((int)$l) . '.' . $this->_format);
          }
        }
      } else {
        $objWriter->save('files/export_products_' . Configuration::get('EXPORT_PRODUCTS_TIME', '', $this->_shopGroupId, $this->_idShop) . $this->_limit . '.' . $this->_format);
      }
    }

    if ((int)$this->_productsCount > ((int)$this->_limit * (int)$this->_limitN) + (int)$this->_limitN) {
      return (int)$this->_limit + 1;
    }

    if (isset($more['automatic']) && $more['automatic'] && $not_exported) {
      $productIds = $this->_model->getExportIds($this->_idShop, $this->_idLang, $this->_separate, $this->_more_settings, 0, 100000);
      $this->setInDbExportedProducts($id_setting, $productIds);
    }

    return _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/exportproducts/files/' . $name_file;
  }

  public function setInDbExportedProducts($id_setting, $productIds)
  {

    $ids = array();

    if ($id_setting && $productIds) {
      foreach ($productIds as $id_product) {
        Db::getInstance()->insert('exported_products', array('id_product' => (int)$id_product['id_product'], 'id_setting' => (int)$id_setting));
      }
    }

  }

  private function _createHead()
  {
    $this->_head = $this->_getHeadFields();
    $this->_PHPExcel->getProperties()->setCreator("PHP")
      ->setLastModifiedBy("Admin")
      ->setTitle("Office 2007 XLSX")
      ->setSubject("Office 2007 XLSX")
      ->setDescription(" Office 2007 XLSX, PHPExcel.")
      ->setKeywords("office 2007 openxml php")
      ->setCategory("File");
    $this->_PHPExcel->getActiveSheet()->setTitle('Export');

    $i = 0;
    foreach ($this->_head as $field => $name) {
      $more = $this->_more_settings;
      if ($more['display_headers']) {
        $this->_PHPExcel->setActiveSheetIndex(0)
          ->setCellValue($this->_alphabet[$i] . '1', $name);
      }

      if ($field == "product_link") {
        $this->_PHPExcel->getActiveSheet()->getColumnDimension($this->_alphabet[$i])->setWidth(80);
      } elseif ($field == "images") {
        $this->_PHPExcel->getActiveSheet()->getColumnDimension($this->_alphabet[$i])->setWidth(80);
      } elseif ($field == "name") {
        $this->_PHPExcel->getActiveSheet()->getColumnDimension($this->_alphabet[$i])->setWidth(80);
      } elseif ($field == "description") {
        $this->_PHPExcel->getActiveSheet()->getColumnDimension($this->_alphabet[$i])->setWidth(80);
      } elseif ($field == "description_short") {
        $this->_PHPExcel->getActiveSheet()->getColumnDimension($this->_alphabet[$i])->setWidth(80);
      } else {
        $this->_PHPExcel->getActiveSheet()->getColumnDimension($this->_alphabet[$i])->setWidth(30);
      }
      $i++;
    }
    $this->_PHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
  }

  private function _getHeadFields()
  {
    $selected_fields = Tools::unserialize(Configuration::get('GOMAKOIL_FIELDS_CHECKED', '', $this->_shopGroupId, $this->_idShop));
    $selected_fields = $this->splitSpecificPriceFields($selected_fields);

    if (isset($selected_fields['features'])) {
      unset($selected_fields['features']);
      foreach ($this->_distinctFeatures as $feature) {
        $featureInfo = Feature::getFeature($this->_idLang, $feature['id_feature']);
        $selected_fields["FEATURE_" . $featureInfo['name']] = "FEATURE_" . $featureInfo['name'];
      }
    }

    if (isset($selected_fields['combinations_value'])) {
      unset($selected_fields['combinations_value']);
      if ($this->_distinctAttributes) {
        foreach ($this->_distinctAttributes as $attribute) {
          if ($attribute['id_attribute_group']) {
            $attrName = new AttributeGroup($attribute['id_attribute_group'], $this->_idLang, $this->_idShop);
            $attrName = $attrName->name;
            $selected_fields["Attribute_" . $attribute['id_attribute_group']] = "Attribute_" . str_replace(' ', '_', $attrName);
          }
        }
      }
    }

    if (isset($selected_fields['separated_categories'])) {
      unset($selected_fields['separated_categories']);
      if ($this->_categoryTreesCount) {
        for ($i = 1; $i <= $this->_categoryTreesCount; $i++) {
          $selected_fields['category_tree_' . $i] = 'Category Tree' . $i;
        }
      }
    }

    if (isset($selected_fields['images_value'])) {
      unset($selected_fields['images_value']);
      if ($this->_maxImages) {

        for ($x = 0; $x++ < $this->_maxImages;) {
          $selected_fields['images_value_' . $x] = "Product Image " . $x;
        }

      }
    }

    return $selected_fields;
  }

  private function _getProductById($productId, $productAttributeId)
  {
    $selected_fields = Tools::unserialize(Configuration::get('GOMAKOIL_FIELDS_CHECKED', '', $this->_shopGroupId, $this->_idShop));
    $product = new Product($productId, false, $this->_idLang, $this->_idShop);

    if ($this->_separate) {
      $productInfo = $this->getProductInfoSeparate($this->splitSpecificPriceFields($selected_fields), $productId, $productAttributeId, $product);
    } else {
      $productInfo = $this->getProductInfo($this->splitSpecificPriceFields($selected_fields), $productId, false, $product);
    }
    return $productInfo;
  }


  public function getProductInfoSeparate($selected_fields, $productId, $id_product_attribute, $product)
  {
    $productInfo = array();
    $combination = new Combination($id_product_attribute);
    $more_settings = $this->_more_settings;
    $round_value = $more_settings['round_value'];
    $decoration_price = $more_settings['decoration_price'];
    $separator_decimal_points = $more_settings['separator_decimal_points'];
    $address = null;
    if (is_object(Context::getContext()->cart) && Context::getContext()->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} != null) {
      $address = Context::getContext()->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
    }

    $product->tax_rate = $product->getTaxesRate(new Address($address));
    $product->base_price = $product->price;
    $product->unit_price = ($product->unit_price_ratio != 0 ? $product->price / $product->unit_price_ratio : 0);

    foreach ($selected_fields as $field => $value) {
      if ($field == "id_product") {
        $productInfo[$field] = $productId;
      } elseif ($field == "id_product_attribute") {
        $productInfo[$field] = $id_product_attribute;
      } elseif ($field == "name_with_combination") {
        $productInfo[$field] = Product::getProductName($product->id, $combination->id);
      } elseif (strpos($field, 'Attribute_') !== false) {
        $needAttribute = explode('_', $field);
        $needAttribute = $needAttribute[1];
        foreach ($this->_getAttributesName($id_product_attribute, $this->_idLang) as $attrValues) {
          if ($needAttribute != $attrValues['id_attribute_group']) {
            continue;
          }
          if (!isset($productInfo['Attribute_' . $attrValues['id_attribute_group']])) {
            $productInfo['Attribute_' . $attrValues['id_attribute_group']] = '';
          }
          $productInfo['Attribute_' . $attrValues['id_attribute_group']] = $attrValues['name'];
        }
      } elseif ($field == "combinations_value") {
        foreach ($this->_getAttributesName($id_product_attribute, $this->_idLang) as $attrValues) {
          if (!isset($productInfo['Attribute_' . $attrValues['id_attribute_group']])) {
            $productInfo['Attribute_' . $attrValues['id_attribute_group']] = '';
          }
          $productInfo['Attribute_' . $attrValues['id_attribute_group']] = $attrValues['name'];
        }
      } elseif ($field == "category_default_name") {
        $productInfo[$field] = "";
        $catName = CategoryCore::getCategoryInformations(array($product->id_category_default), $this->_idLang);
        if (isset($catName[$product->id_category_default])) {
          $productInfo[$field] = $catName[$product->id_category_default]['name'];
        }
      } elseif ($field == "separated_categories") {
        $categories = $this->_getWsCategories($product->id);
        $this->_parentCategories = array();

        $currentCount = 0;
        foreach ($categories as $category) {
          $catTree = '';
          if (($tree = $this->_getCategoryTree($category['id']))) {
            $currentCount++;
            foreach ($tree as $cat) {
              $catTree .= $cat . '->';
            }
            $catTree = rtrim($catTree, '->');
            $productInfo['category_tree_' . $currentCount] = $catTree;
          }
        }
      } elseif ($field == "images_value") {
        $link = new Link(null, 'http://');
        $images = $product->getCombinationImages(Context::getContext()->language->id);
        if (isset($images[$id_product_attribute]) && $images[$id_product_attribute]) {
          foreach ($images[$id_product_attribute] as $key => $image) {
            $productInfo['images_value_' . ($key + 1)] = "";
            if ($link->getImageLink($product->link_rewrite, $image['id_image'])) {
              $productInfo['images_value_' . ($key + 1)] = $link->getImageLink($product->link_rewrite, $image['id_image']);
            }
          }
        } else {
          foreach ($product->getWsImages() as $key => $image) {
            $productInfo['images_value_' . ($key + 1)] = "";
            if ($link->getImageLink($product->link_rewrite, $image['id'])) {
              $productInfo['images_value_' . ($key + 1)] = $link->getImageLink($product->link_rewrite, $image['id']);
            }
          }
        }
      } elseif ($field == "categories_ids") {
        $productInfo[$field] = "";
        foreach ($product->getWsCategories() as $category) {
          $productInfo[$field] .= $category['id'] . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "categories_names") {
        $productInfo[$field] = "";
        foreach ($product->getWsCategories() as $category) {
          $cat_obj = new Category($category['id'], $this->_idLang, $this->_idShop);
          $productInfo[$field] .= $cat_obj->name . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == 'suppliers_ids') {
        $product_supplier = $this->_model->getProductSuppliersID($productId);
        if ($product_supplier) {
          $productInfo[$field] = $product_supplier[0]['suppliers_ids'];
        }
      } elseif ($field == 'suppliers_name') {
        $product_supplier = $this->_model->getProductSuppliersID($productId);
        if ($product_supplier) {
          $productInfo[$field] = $product_supplier[0]['suppliers_name'];
        }
      } elseif ($field == 'quantity') {
        $productInfo[$field] = $product->getQuantity($productId, $id_product_attribute);
      } elseif ($field == 'total_quantity') {
        $productInfo[$field] = $product->getQuantity($productId, 0);
      } elseif ($field == 'out_of_stock') {
        $productInfo[$field] = StockAvailable::outOfStock($productId);
      } elseif ($field == 'depends_on_stock') {
        $productInfo[$field] = StockAvailable::dependsOnStock($productId);
      } elseif ($field == 'manufacturer_name') {
        $productInfo[$field] = Manufacturer::getNameById((int)$product->id_manufacturer);
      } elseif ($field == 'supplier_name') {
        $productInfo[$field] = Supplier::getNameById((int)$product->id_supplier);
      } elseif ($field == 'new') {
        $productInfo[$field] = $product->isNew();
      } elseif ($field == 'supplier_reference') {
        $sReference = ProductSupplier::getProductSupplierReference($productId, $id_product_attribute, $product->id_supplier);
        if (!$sReference) {
          $sReference = '';
        }
        $productInfo[$field] = $sReference;
      } elseif ($field == 'supplier_price') {
        $sPrice = ProductSupplier::getProductSupplierPrice($productId, $id_product_attribute, $product->id_supplier);
        if (!$sPrice) {
          $sPrice = '';
        } else {
          $sPrice = Tools::ps_round($sPrice, $round_value);
          $sPrice = number_format($sPrice, $round_value, $separator_decimal_points, '');
          $sPrice = str_replace('[PRICE]', $sPrice, $decoration_price);
        }
        $productInfo[$field] = $sPrice;
      } elseif ($field == 'supplier_price_currency') {
        $sPriceCurrency = ProductSupplier::getProductSupplierPrice($productId, $id_product_attribute, $product->id_supplier, true);
        if (isset($sPriceCurrency['id_currency'])) {
          $tmpCurrency = new Currency($sPriceCurrency['id_currency']);
          $sPriceCurrency['id_currency'] = $tmpCurrency->iso_code;
          $productInfo[$field] = $sPriceCurrency['id_currency'];
        } else {
          $productInfo[$field] = '';
        }
      } elseif ($field == "base_price" || $field == "ecotax" || $field == "additional_shipping_cost" || $field == "unit_price") {
        $tmpPrice = Tools::ps_round($product->$field, $round_value);
        $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
        $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
        $productInfo[$field] = $tmpPrice;
      } elseif ($field == "base_price_with_tax") {
        $taxPrice = $product->base_price;
        if ($product->tax_rate) {
          $taxPrice = $taxPrice + ($taxPrice * ($product->tax_rate / 100));
        }

        $tmpPrice = Tools::ps_round($taxPrice, $round_value);
        $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
        $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);

        $productInfo[$field] = $tmpPrice;
      } elseif ($field == "wholesale_price") {
        $tmpPrice = Tools::ps_round($product->$field, $round_value);
        $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
        $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
        $productInfo[$field] = $tmpPrice;
      } elseif ($field == "price") {

        $taxPrice = $product->getPrice(false, $id_product_attribute, $round_value);
        $tmpPrice = Tools::ps_round($taxPrice, $round_value);
        $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
        $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
        $productInfo[$field] = $tmpPrice;

      } elseif ($field == "final_price_with_tax") {
        $taxPrice = $product->getPrice(true, $id_product_attribute, $round_value);
        $tmpPrice = Tools::ps_round($taxPrice, $round_value);
        $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
        $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
        $productInfo[$field] = $tmpPrice;
      } elseif ($field == "combinations_name") {
        if ($combination->id) {
          $productInfo[$field] = str_replace($product->name . " : ", '', Product::getProductName($product->id, $combination->id));
        } else {
          $productInfo[$field] = '';
        }

      } elseif ($field == "combinations_price") {
        $tmpPrice = Tools::ps_round($combination->price, $round_value);
        $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
        $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
        $productInfo[$field] = $tmpPrice;
      } elseif ($field == "combinations_price_with_tax") {
        $taxPrice = $combination->price;
        $tmpPrice = ($taxPrice + ($taxPrice * ($product->tax_rate / 100)));
        $tmpPrice = Tools::ps_round($tmpPrice, $round_value);
        $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
        $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
        $productInfo[$field] = $tmpPrice;
      } elseif ($field == "combinations_wholesale_price") {
        $tmpPrice = Tools::ps_round($combination->wholesale_price, $round_value);
        $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
        $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
        $productInfo[$field] = $tmpPrice;
      } elseif ($field == "combinations_unit_price_impact") {
        $tmpPrice = Tools::ps_round($combination->unit_price_impact, $round_value);
        $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
        $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
        $productInfo[$field] = $tmpPrice;
      } elseif ($field == "minimal_quantity") {
        if ($id_product_attribute) {
          $productInfo[$field] = $combination->minimal_quantity;
        } else {
          $productInfo[$field] = $product->minimal_quantity;
        }
      } elseif (preg_match('/id_specific_price_\d+/', $field)) {
        $productInfo[$field] = $this->getSpecificPriceAttribute('id_specific_price', $productId, $field, $id_product_attribute);
      } elseif (preg_match('/specific_price_\d+/', $field)) {
        $tmpPrice = $this->getSpecificPriceAttribute('price', $productId, $field, $id_product_attribute);

        if ($tmpPrice > 0) {
          $tmpPrice = Tools::ps_round($tmpPrice, $round_value);
          $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
          $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
          $productInfo[$field] = $tmpPrice;
        }
      } elseif (preg_match('/^specific_price_from_quantity_\d+$/', $field)) {
        $productInfo[$field] = $this->getSpecificPriceAttribute('from_quantity', $productId, $field, $id_product_attribute);
      } elseif (preg_match('/^specific_price_reduction_\d+$/', $field)) {
        $tmpPrice = $this->getSpecificPriceAttribute('reduction', $productId, $field, $id_product_attribute);

        if ($tmpPrice > 0) {
          $tmpPrice = Tools::ps_round($tmpPrice, $round_value);
          $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
          $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
          $productInfo[$field] = $tmpPrice;
        }
      } elseif (preg_match('/^specific_price_reduction_type_\d+$/', $field)) {
        $productInfo[$field] = $this->getSpecificPriceAttribute('reduction_type', $productId, $field, $id_product_attribute);
      } elseif (preg_match('/^specific_price_from_\d+$/', $field)) {
        $productInfo[$field] = $this->getSpecificPriceAttribute('from', $productId, $field, $id_product_attribute);
      } elseif (preg_match('/^specific_price_to_\d+$/', $field)) {
        $productInfo[$field] = $this->getSpecificPriceAttribute('to', $productId, $field, $id_product_attribute);
      } elseif (preg_match('/^specific_price_id_group_\d+$/', $field)) {
        $productInfo[$field] = $this->getSpecificPriceAttribute('id_group', $productId, $field, $id_product_attribute);
      } elseif ($field == "combinations_reference") {
        $productInfo[$field] = $combination->reference;
      } elseif ($field == "combinations_location") {
        $productInfo[$field] = $combination->location;
      } elseif ($field == "combinations_weight") {
        $tmpPrice = Tools::ps_round($combination->weight, $round_value);
        $productInfo[$field] = number_format($tmpPrice, $round_value, '.', '');
      } elseif ($field == "combinations_ecotax") {
        $tmpPrice = Tools::ps_round($combination->ecotax, $round_value);
        $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
        $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
        $productInfo[$field] = $tmpPrice;
      } elseif ($field == "combinations_ean13") {
        $productInfo[$field] = $combination->ean13;
      } elseif ($field == "combinations_upc") {
        $productInfo[$field] = $combination->upc;
      } elseif ($field == "tags") {
        $productInfo[$field] = $product->getTags($this->_idLang);
      } elseif ($field == "id_attachments") {
        $productInfo[$field] = "";
        foreach ($product->getAttachments($this->_idLang) as $attachments) {
          $productInfo[$field] .= $attachments['id_attachment'] . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "attachments_name") {
        $productInfo[$field] = "";
        foreach ($product->getAttachments($this->_idLang) as $attachments) {
          $productInfo[$field] .= $attachments['name'] . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "attachments_description") {
        $productInfo[$field] = "";
        foreach ($product->getAttachments($this->_idLang) as $attachments) {
          $productInfo[$field] .= $attachments['description'] . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "attachments_file") {
        $productInfo[$field] = "";
        $link = new Link(null, 'http://');
        foreach ($product->getAttachments($this->_idLang) as $attachments) {
          $productInfo[$field] .= $link->getPageLink('attachment', true, NULL, "id_attachment=" . $attachments['id_attachment']) . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "id_carriers") {
        $productInfo[$field] = "";
        foreach ($product->getCarriers() as $carriers) {
          $productInfo[$field] .= $carriers['id_carrier'] . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "id_product_accessories") {
        $productInfo[$field] = "";
        foreach ($product->getWsAccessories() as $accessories) {
          $productInfo[$field] .= $accessories['id'] . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "image_caption") {
        $productInfo[$field] = "";
        foreach ($product->getWsImages() as $image) {
          $img = new Image($image['id'], $this->_idLang);
          $productInfo[$field] .= $img->legend . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "images") {
        $productInfo[$field] = "";
        $link = new Link(null, 'http://');
        $combImages = $this->getCombinationImageById($combination->id, $this->_idLang, false);
        if (!$combImages) {
          $combImages = $product->getWsImages();
        }
        foreach ($combImages as $image) {
          $productInfo[$field] .= $link->getImageLink($product->link_rewrite, $image['id']) . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "image_cover") {
        $cover = $product->getCover($product->id);
        $images = $this->getCombinationImageById($id_product_attribute, $this->_idLang);
        if (!$cover && !$images) {
          $productInfo[$field] = false;
        } else {
          $link = new Link(null, 'http://');
          if ($images['id_image']) {
            $url_cover = $link->getImageLink($product->link_rewrite, $images['id_image'], $this->_imageType);
          } else {
            $url_cover = $link->getImageLink($product->link_rewrite, $cover['id_image'], $this->_imageType);
          }
          $productInfo[$field] = $url_cover;
        }
      }
      elseif ($field == "cover_image_url") {
        $cover = $product->getCover($product->id);
        $images = $this->getCombinationImageById($id_product_attribute, $this->_idLang);
        if (!$cover && !$images) {
          $productInfo[$field] = false;
        } else {
          $link = new Link(null, 'http://');
          if ($images['id_image']) {
            $url_cover = $link->getImageLink($product->link_rewrite, $images['id_image'], $this->_imageType);
          } else {
            $url_cover = $link->getImageLink($product->link_rewrite, $cover['id_image'], $this->_imageType);
          }
          $productInfo[$field] = $url_cover;
        }
      }
      elseif (strpos($field, 'FEATURE_') !== false) {
        $needFeature = explode('_', $field);
        $needFeature = $needFeature[1];
        if (Module::getInstanceByName('pm_multiplefeatures')) {
          $features = Module::getInstanceByName('pm_multiplefeatures')->getFrontFeatures($productId);
        } else {
          $features = $product->getFrontFeatures($this->_idLang);
        }

        foreach ($features as $feature) {
          if ($needFeature == $feature['name']) {
            $productInfo["FEATURE_" . $feature['name']] = $feature['value'];
          }
        }
      } elseif ($field == "features") {
        $productInfo[$field] = "";
        if (Module::getInstanceByName('pm_multiplefeatures')) {
          $features = Module::getInstanceByName('pm_multiplefeatures')->getFrontFeatures($productId);
        } else {
          $features = $product->getFrontFeatures($this->_idLang);
        }

        foreach ($features as $feature) {
          $productInfo["FEATURE_" . $feature['name']] = $feature['value'];
        }
      } elseif ($field == "product_link") {
        $productInfo[$field] = "";
        $link = new Link();
        $productInfo[$field] = $link->getProductLink($productId, null, null, null, $this->_idLang, $this->_idShop, $combination->id);
      } elseif ($field == "description" || $field == "description_short") {
        $mora_settings = $this->_more_settings;
        if ($mora_settings['strip_tags']) {
          $productInfo[$field] = strip_tags($product->$field);
        } else {
          $productInfo[$field] = $product->$field;
        }
      } elseif ($field == "width" || $field == "height" || $field == "depth" || $field == "weight") {
        $tmpPrice = Tools::ps_round($product->$field, $round_value);
        $productInfo[$field] = number_format($tmpPrice, $round_value, '.', '');
      } else {
        $productInfo[$field] = $product->$field;
      }
    }
    return $productInfo;
  }

  private function _getAttributesName($combinationId, $id_lang)
  {
    return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT al.*, a.id_attribute_group
			FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac
			JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON (pac.id_attribute = al.id_attribute AND al.id_lang=' . (int)$id_lang . ')
			LEFT JOIN ' . _DB_PREFIX_ . 'attribute a ON (a.id_attribute = al.id_attribute)
			WHERE pac.id_product_attribute=' . (int)$combinationId . '
    ');
  }

  public function getProductInfo($selected_fields, $productId, $id_product_attribute, $product)
  {
    $combinations = array();
    $productInfo = array();

    $more_settings = $this->_more_settings;
    $round_value = $more_settings['round_value'];
    $decoration_price = $more_settings['decoration_price'];
    $separator_decimal_points = $more_settings['separator_decimal_points'];

    $address = null;
    if (is_object(Context::getContext()->cart) && Context::getContext()->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} != null) {
      $address = Context::getContext()->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
    }

    $product->tax_rate = $product->getTaxesRate(new Address($address));
    $product->base_price = $product->price;
    $product->unit_price = ($product->unit_price_ratio != 0 ? $product->price / $product->unit_price_ratio : 0);

    foreach ($product->getWsCombinations() as $attribute) {
      $combination = new Combination($attribute['id']);
      $combinations[$attribute['id']] = $combination;
    }

    foreach ($selected_fields as $field => $value) {
      if ($field == "id_product") {
        $productInfo[$field] = $productId;
      } elseif ($field == "id_product_attribute") {
        $productInfo[$field] = "";
        foreach ($combinations as $key => $attribute) {
          $productInfo[$field] .= $key . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "category_default_name") {
        $productInfo[$field] = "";
        $catName = CategoryCore::getCategoryInformations(array($product->id_category_default), $this->_idLang);
        if (isset($catName[$product->id_category_default])) {
          $productInfo[$field] = $catName[$product->id_category_default]['name'];
        }
      } elseif (strpos($field, 'Attribute_') !== false) {
        $productInfo[$field] = "";
        $existsAttr = array();
        $needAttribute = explode('_', $field);
        $needAttribute = $needAttribute[1];
        foreach ($combinations as $key => $attribute) {
          foreach ($this->_getAttributesName($key, $this->_idLang) as $attrValues) {
            if ($needAttribute != $attrValues['id_attribute_group']) {
              continue;
            }
            if (!isset($productInfo['Attribute_' . $attrValues['id_attribute_group']])) {
              $productInfo['Attribute_' . $attrValues['id_attribute_group']] = '';
            }
            if (!isset($existsAttr[$attrValues['id_attribute_group'] . $attrValues['name']])) {
              $productInfo['Attribute_' . $attrValues['id_attribute_group']] .= "," . $attrValues['name'];
              $productInfo['Attribute_' . $attrValues['id_attribute_group']] = ltrim($productInfo['Attribute_' . $attrValues['id_attribute_group']], ",");
              $existsAttr[$attrValues['id_attribute_group'] . $attrValues['name']] = true;
            }
          }

        }
      } elseif ($field == "combinations_value") {
        $productInfo[$field] = "";
        $existsAttr = array();
        foreach ($combinations as $key => $attribute) {
          foreach ($this->_getAttributesName($key, $this->_idLang) as $attrValues) {
            if (!isset($productInfo['Attribute_' . $attrValues['id_attribute_group']])) {
              $productInfo['Attribute_' . $attrValues['id_attribute_group']] = '';
            }
            if (!isset($existsAttr[$attrValues['id_attribute_group'] . $attrValues['name']])) {
              $productInfo['Attribute_' . $attrValues['id_attribute_group']] .= "," . $attrValues['name'];
              $productInfo['Attribute_' . $attrValues['id_attribute_group']] = ltrim($productInfo['Attribute_' . $attrValues['id_attribute_group']], ",");
              $existsAttr[$attrValues['id_attribute_group'] . $attrValues['name']] = true;
            }
          }

        }
      } elseif ($field == "separated_categories") {
        $categories = $this->_getWsCategories($product->id);
        $this->_parentCategories = array();

        $currentCount = 0;
        foreach ($categories as $category) {
          $catTree = '';
          if (($tree = $this->_getCategoryTree($category['id']))) {
            $currentCount++;
            foreach ($tree as $cat) {
              $catTree .= $cat . '->';
            }
            $catTree = rtrim($catTree, '->');
            $productInfo['category_tree_' . $currentCount] = $catTree;
          }
        }
      } elseif ($field == "images_value") {
        $link = new Link(null, 'http://');
        foreach ($product->getWsImages() as $key => $image) {
          $productInfo['images_value_' . ($key + 1)] = "";
          if ($link->getImageLink($product->link_rewrite, $image['id'])) {
            $productInfo['images_value_' . ($key + 1)] = $link->getImageLink($product->link_rewrite, $image['id']);
          }
        }
      } elseif ($field == "categories_ids") {
        $productInfo[$field] = "";
        foreach ($product->getWsCategories() as $category) {
          $productInfo[$field] .= $category['id'] . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "categories_names") {
        $productInfo[$field] = "";
        foreach ($product->getWsCategories() as $category) {
          $cat_obj = new Category($category['id'], $this->_idLang, $this->_idShop);
          $productInfo[$field] .= $cat_obj->name . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == 'suppliers_ids') {
        $product_supplier = $this->_model->getProductSuppliersID($productId);
        if ($product_supplier) {
          $productInfo[$field] = $product_supplier[0]['suppliers_ids'];
        }
      } elseif ($field == 'suppliers_name') {
        $product_supplier = $this->_model->getProductSuppliersID($productId);
        if ($product_supplier) {
          $productInfo[$field] = $product_supplier[0]['suppliers_name'];
        }
      } elseif ($field == 'quantity') {
        if ($combinations) {
          $productInfo[$field] = "";
          foreach ($combinations as $key => $combination) {
            $productInfo[$field] .= $product->getQuantity($productId, $key) . ",";
          }
          $productInfo[$field] = rtrim($productInfo[$field], ",");
        } else {
          $productInfo[$field] = $product->getQuantity($productId, 0);
        }
      } elseif ($field == 'total_quantity') {
        $productInfo[$field] = $product->getQuantity($productId, 0);
      } elseif ($field == 'out_of_stock') {
        $productInfo[$field] = StockAvailable::outOfStock($productId);
      } elseif ($field == 'depends_on_stock') {
        $productInfo[$field] = StockAvailable::dependsOnStock($productId);
      } elseif ($field == 'manufacturer_name') {
        $productInfo[$field] = Manufacturer::getNameById((int)$product->id_manufacturer);
      } elseif ($field == 'supplier_name') {
        $productInfo[$field] = Supplier::getNameById((int)$product->id_supplier);
      } elseif ($field == 'new') {
        $productInfo[$field] = $product->isNew();
      } elseif ($field == 'supplier_reference') {
        $sReference = '';
        if ($combinations) {
          foreach ($combinations as $combination) {
            $sReference .= ProductSupplier::getProductSupplierReference($productId, $combination->id, $product->id_supplier) . ",";
          }
          $sReference = rtrim($sReference, ",");
        } else {
          $sReference = ProductSupplier::getProductSupplierReference($productId, 0, $product->id_supplier);
          if (!$sReference) {
            $sReference = '';
          }
        }
        $productInfo[$field] = $sReference;
      } elseif ($field == 'supplier_price') {
        $sPrice = '';
        if ($combinations) {
          foreach ($combinations as $combination) {
            $tmpPrice = ProductSupplier::getProductSupplierPrice($productId, $combination->id, $product->id_supplier);
            $tmpPrice = Tools::ps_round($tmpPrice, $round_value);
            $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
            $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
            $sPrice .= $tmpPrice . ",";
          }

          $sPrice = rtrim($sPrice, ",");
        } else {
          $sPrice = ProductSupplier::getProductSupplierPrice($productId, 0, $product->id_supplier);
          if (!$sPrice) {
            $sPrice = '';
          } else {
            $sPrice = Tools::ps_round($sPrice, $round_value);
            $sPrice = number_format($sPrice, $round_value, $separator_decimal_points, '');
            $sPrice = str_replace('[PRICE]', $sPrice, $decoration_price);
          }
        }

        $productInfo[$field] = $sPrice;
      } elseif ($field == 'supplier_price_currency') {
        if ($combinations) {
          $productInfo[$field] = "";
          foreach ($combinations as $combination) {
            $sPriceCurrency = ProductSupplier::getProductSupplierPrice($productId, $combination->id, $product->id_supplier, true);
            if (isset($sPriceCurrency['id_currency']) && $sPriceCurrency['id_currency']) {
              $tmpCurrency = new Currency($sPriceCurrency['id_currency']);
              $sPriceCurrency['id_currency'] = $tmpCurrency->iso_code;
              $productInfo[$field] .= $sPriceCurrency['id_currency'] . ",";
            }
          }

          $productInfo[$field] = rtrim($productInfo[$field], ",");
        } else {
          $sPriceCurrency = ProductSupplier::getProductSupplierPrice($productId, 0, $product->id_supplier, true);
          if (isset($sPriceCurrency['id_currency'])) {
            $tmpCurrency = new Currency($sPriceCurrency['id_currency']);
            $sPriceCurrency['id_currency'] = $tmpCurrency->iso_code;
            $productInfo[$field] = $sPriceCurrency['id_currency'];
          } else {
            $productInfo[$field] = '';
          }
        }
      } elseif ($field == "base_price" || $field == "ecotax" || $field == "additional_shipping_cost" || $field == "unit_price") {
        $tmpPrice = Tools::ps_round($product->$field, $round_value);
        $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
        $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
        $productInfo[$field] = $tmpPrice;
      } elseif ($field == "base_price_with_tax") {
        $taxPrice = $product->base_price;
        if ($product->tax_rate) {
          $taxPrice = $taxPrice + ($taxPrice * ($product->tax_rate / 100));
        }
        $tmpPrice = Tools::ps_round($taxPrice, $round_value);
        $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
        $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
        $productInfo[$field] = $tmpPrice;
      } elseif ($field == "wholesale_price") {
        $tmpPrice = Tools::ps_round($product->$field, $round_value);
        $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
        $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
        $productInfo[$field] = $tmpPrice;
      } elseif ($field == "price") {
        if ($combinations) {
          $productInfo[$field] = "";
          foreach ($combinations as $combination) {
            $price = $product->getPrice(false, $combination->id, $round_value) . ",";
            $tmpPrice = Tools::ps_round($price, $round_value);
            $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
            $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
            $productInfo[$field] .= $tmpPrice . ",";
          }
          $productInfo[$field] = rtrim($productInfo[$field], ",");
        } else {
          $taxPrice = $product->getPrice(false, 0, $round_value);
          $tmpPrice = Tools::ps_round($taxPrice, $round_value);
          $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
          $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
          $productInfo[$field] = $tmpPrice;
        }
      } elseif ($field == "final_price_with_tax") {
        if ($combinations) {
          $productInfo[$field] = "";
          foreach ($combinations as $combination) {
            $price = $product->getPrice(true, $combination->id, $round_value) . ",";
            $tmpPrice = Tools::ps_round($price, $round_value);
            $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
            $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
            $productInfo[$field] .= $tmpPrice . ",";

          }
          $productInfo[$field] = rtrim($productInfo[$field], ",");
        } else {
          $taxPrice = $product->getPrice(true, 0, $round_value);
          $tmpPrice = Tools::ps_round($taxPrice, $round_value);
          $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
          $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
          $productInfo[$field] = $tmpPrice;
        }
      } elseif ($field == "combinations_name") {
        $productInfo[$field] = "";
        foreach ($combinations as $combination) {
          $productInfo[$field] .= str_replace($product->name . " : ", '', Product::getProductName($product->id, $combination->id)) . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "name_with_combination") {
        foreach ($combinations as $combination) {
          $productInfo[$field] .= Product::getProductName($product->id, $combination->id) . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "combinations_price") {
        $productInfo[$field] = "";
        foreach ($combinations as $combination) {
          $tmpPrice = Tools::ps_round($combination->price, $round_value);
          $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
          $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
          $productInfo[$field] .= $tmpPrice . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "combinations_price_with_tax") {
        $productInfo[$field] = "";
        foreach ($combinations as $combination) {
          $taxPrice = $combination->price;
          $price = ($taxPrice + ($taxPrice * ($product->tax_rate / 100)));
          $tmpPrice = Tools::ps_round($price, $round_value);
          $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
          $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
          $productInfo[$field] .= $tmpPrice . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif (preg_match('/id_specific_price_\d+/', $field)) {
        $productInfo[$field] = $this->getSpecificPriceAttribute('id_specific_price', $productId, $field);
      } elseif (preg_match('/specific_price_\d+/', $field)) {
        $tmpPrice = $this->getSpecificPriceAttribute('price', $productId, $field);

        if ($tmpPrice > 0) {
          $tmpPrice = Tools::ps_round($tmpPrice, $round_value);
          $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
          $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
          $productInfo[$field] = $tmpPrice;
        }
      } elseif (preg_match('/^specific_price_from_quantity_\d+$/', $field)) {
        $productInfo[$field] = $this->getSpecificPriceAttribute('from_quantity', $productId, $field);
      } elseif (preg_match('/^specific_price_reduction_\d+$/', $field)) {
        $tmpPrice = $this->getSpecificPriceAttribute('reduction', $productId, $field);

        if ($tmpPrice > 0) {
          $tmpPrice = Tools::ps_round($tmpPrice, $round_value);
          $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
          $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
          $productInfo[$field] = $tmpPrice;
        }
      } elseif (preg_match('/^specific_price_reduction_type_\d+$/', $field)) {
        $productInfo[$field] = $this->getSpecificPriceAttribute('reduction_type', $productId, $field);
      } elseif (preg_match('/^specific_price_from_\d+$/', $field)) {
        $productInfo[$field] = $this->getSpecificPriceAttribute('from', $productId, $field);
      } elseif (preg_match('/^specific_price_to_\d+$/', $field)) {
        $productInfo[$field] = $this->getSpecificPriceAttribute('to', $productId, $field);
      } elseif (preg_match('/^specific_price_id_group_\d+$/', $field)) {
        $productInfo[$field] = $this->getSpecificPriceAttribute('id_group', $productId, $field);
      } elseif ($field == "combinations_wholesale_price") {
        $productInfo[$field] = "";
        foreach ($combinations as $combination) {
          $tmpPrice = Tools::ps_round($combination->wholesale_price, $round_value);
          $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
          $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
          $productInfo[$field] .= $tmpPrice . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "combinations_unit_price_impact") {
        $productInfo[$field] = "";
        foreach ($combinations as $combination) {
          $tmpPrice = Tools::ps_round($combination->unit_price_impact, $round_value);
          $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
          $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
          $productInfo[$field] .= $tmpPrice . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "minimal_quantity") {
        if ($combinations) {
          $productInfo[$field] = "";
          foreach ($combinations as $combination) {
            $productInfo[$field] .= $combination->minimal_quantity . ",";
          }
          $productInfo[$field] = rtrim($productInfo[$field], ",");
        } else {
          $productInfo[$field] = $product->minimal_quantity;
        }
      } elseif ($field == "combinations_reference") {
        $productInfo[$field] = "";
        foreach ($combinations as $combination) {
          $productInfo[$field] .= $combination->reference . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "combinations_location") {
        $productInfo[$field] = "";
        foreach ($combinations as $combination) {
          $productInfo[$field] .= $combination->location . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "combinations_weight") {
        $productInfo[$field] = "";
        foreach ($combinations as $combination) {
          $tmpPrice = Tools::ps_round($combination->weight, $round_value);
          $productInfo[$field] .= number_format($tmpPrice, $round_value, '.', '') . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "combinations_ecotax") {
        $productInfo[$field] = "";
        foreach ($combinations as $combination) {
          $tmpPrice = Tools::ps_round($combination->ecotax, $round_value);
          $tmpPrice = number_format($tmpPrice, $round_value, $separator_decimal_points, '');
          $tmpPrice = str_replace('[PRICE]', $tmpPrice, $decoration_price);
          $productInfo[$field] .= $tmpPrice . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "combinations_ean13") {
        $productInfo[$field] = "";
        foreach ($combinations as $combination) {
          $productInfo[$field] .= $combination->ean13 . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "combinations_upc") {
        $productInfo[$field] = "";
        foreach ($combinations as $combination) {
          $productInfo[$field] .= $combination->upc . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "tags") {
        $productInfo[$field] = $product->getTags($this->_idLang);
      } elseif ($field == "id_attachments") {
        $productInfo[$field] = "";
        foreach ($product->getAttachments($this->_idLang) as $attachments) {
          $productInfo[$field] .= $attachments['id_attachment'] . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "attachments_name") {
        $productInfo[$field] = "";
        foreach ($product->getAttachments($this->_idLang) as $attachments) {
          $productInfo[$field] .= $attachments['name'] . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "attachments_description") {
        $productInfo[$field] = "";
        foreach ($product->getAttachments($this->_idLang) as $attachments) {
          $productInfo[$field] .= $attachments['description'] . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "attachments_file") {
        $productInfo[$field] = "";
        $link = new Link(null, 'http://');
        foreach ($product->getAttachments($this->_idLang) as $attachments) {
          $productInfo[$field] .= $link->getPageLink('attachment', true, NULL, "id_attachment=" . $attachments['id_attachment']) . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "id_carriers") {
        $productInfo[$field] = "";
        foreach ($product->getCarriers() as $carriers) {
          $productInfo[$field] .= $carriers['id_carrier'] . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "id_product_accessories") {
        $productInfo[$field] = "";
        foreach ($product->getWsAccessories() as $accessories) {
          $productInfo[$field] .= $accessories['id'] . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "image_caption") {
        $productInfo[$field] = "";
        foreach ($product->getWsImages() as $image) {
          $img = new Image($image['id'], $this->_idLang);
          $productInfo[$field] .= $img->legend . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "images") {
        $productInfo[$field] = "";
        $link = new Link(null, 'http://');
        foreach ($product->getWsImages() as $image) {
          $productInfo[$field] .= $link->getImageLink($product->link_rewrite, $image['id']) . ",";
        }
        $productInfo[$field] = rtrim($productInfo[$field], ",");
      } elseif ($field == "image_cover") {
        $cover = $product->getCover($product->id);
        if (!$cover) {
          $productInfo[$field] = false;
        } else {
          $link = new Link(null, 'http://');
          $url_cover = $link->getImageLink($product->link_rewrite, $cover['id_image'], $this->_imageType);
          $productInfo[$field] = $url_cover;
        }
      }
      elseif ($field == "cover_image_url") {
        $cover = $product->getCover($product->id);
        if (!$cover) {
          $productInfo[$field] = false;
        } else {
          $link = new Link(null, 'http://');
          $url_cover = $link->getImageLink($product->link_rewrite, $cover['id_image'], $this->_imageType);
          $productInfo[$field] = $url_cover;
        }
      }
      elseif (strpos($field, 'FEATURE_') !== false) {
        $needFeature = explode('_', $field);
        $needFeature = $needFeature[1];
        if (Module::getInstanceByName('pm_multiplefeatures')) {
          $features = Module::getInstanceByName('pm_multiplefeatures')->getFrontFeatures($productId);
        } else {
          $features = $product->getFrontFeatures($this->_idLang);
        }

        foreach ($features as $feature) {
          if ($needFeature == $feature['name']) {
            $productInfo["FEATURE_" . $feature['name']] = $feature['value'];
          }
        }
      } elseif ($field == "features") {
        $productInfo[$field] = "";
        if (Module::getInstanceByName('pm_multiplefeatures')) {
          $features = Module::getInstanceByName('pm_multiplefeatures')->getFrontFeatures($productId);
        } else {
          $features = $product->getFrontFeatures($this->_idLang);
        }

        foreach ($features as $feature) {
          $productInfo["FEATURE_" . $feature['name']] = $feature['value'];
        }
      } elseif ($field == "product_link") {
        $productInfo[$field] = "";
        $link = new Link(null, 'http://');
        $productInfo[$field] = $link->getProductLink($productId);
      } elseif ($field == "description" || $field == "description_short") {
        $mora_settings = $this->_more_settings;
        if ($mora_settings['strip_tags']) {
          $productInfo[$field] = strip_tags($product->$field);
        } else {
          $productInfo[$field] = $product->$field;
        }
      } elseif ($field == "width" || $field == "height" || $field == "depth" || $field == "weight") {
        $tmpPrice = Tools::ps_round($product->$field, $round_value);
        $productInfo[$field] = number_format($tmpPrice, $round_value, '.', '');
      } else {
        $productInfo[$field] = $product->$field;
      }
    }
    return $productInfo;
  }

  public static function getCombinationImageById($id_product_attribute, $id_lang, $cover = true)
  {
    if (!Combination::isFeatureActive() || !$id_product_attribute) {
      return false;
    }

    $result = Db::getInstance()->executeS('
			SELECT pai.`id_image`,pai.`id_image` as id, pai.`id_product_attribute`, il.`legend`
			FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai
			LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (il.`id_image` = pai.`id_image`)
			LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_image` = pai.`id_image`)
			WHERE pai.`id_product_attribute` = ' . (int)$id_product_attribute . ' AND il.`id_lang` = ' . (int)$id_lang . ' ORDER by i.`position`'
    );

    if (!$result) {
      return false;
    }

    if ($cover) {
      return $result[0];
    } else {
      return $result;
    }
  }

  private function _getXmlHead($fieldName)
  {
    if (!$this->_xml_head) {
      $allFields = array_merge(Module::getInstanceByName('exportproducts')->_exportTabInformation, Module::getInstanceByName('exportproducts')->_exportTabPrices, Module::getInstanceByName('exportproducts')->_exportTabSeo, Module::getInstanceByName('exportproducts')->_exportTabAssociations, Module::getInstanceByName('exportproducts')->_exportTabShipping, Module::getInstanceByName('exportproducts')->_exportTabCombinations, Module::getInstanceByName('exportproducts')->_exportTabQuantities, Module::getInstanceByName('exportproducts')->_exportTabImages, Module::getInstanceByName('exportproducts')->_exportTabFeatures, Module::getInstanceByName('exportproducts')->_exportTabCustomization, Module::getInstanceByName('exportproducts')->_exportTabAttachments, Module::getInstanceByName('exportproducts')->_exportTabSuppliers);
      foreach ($allFields as $field) {
        $this->_xml_head[$field['val']] = isset($field['xml_head']) ? $field['xml_head'] : '';
      }
    }

    if (isset($this->_xml_head[$fieldName]) && $this->_xml_head[$fieldName]) {
      return $this->_xml_head[$fieldName];
    } else {
      return $fieldName;
    }
  }

  private function splitSpecificPriceFields($selected_fields)
  {
    $specific_price_fields = array(
      'id_specific_price' => '',
      'specific_price' => '',
      'specific_price_reduction' => '',
      'specific_price_reduction_type' => '',
      'specific_price_from' => '',
      'specific_price_to' => '',
      'specific_price_from_quantity' => '',
      'specific_price_id_group' => '',
    );

    $specific_price_selected_fields = array_intersect_key($selected_fields, $specific_price_fields);
    $specific_price_num_of_cols = $this->_model->getExportIds(
      $this->_idShop,
      $this->_idLang,
      $this->_separate,
      $this->_more_settings,
      $this->_limit,
      1,
      $count = false,
      $separateAttribute = false,
      $count_images = false,
      $separatedCategories = false,
      $features = false,
      true
    );

    if (!empty($specific_price_selected_fields)) {
      foreach ($specific_price_selected_fields as $key => $value) {
        unset($selected_fields[$key]);
        for ($i = 1; $i <= $specific_price_num_of_cols; $i++) {
          $selected_fields[$key . '_' . $i] = $value . '_' . $i;
        }
      }
    }

    return $selected_fields;
  }

  private function getSpecificPriceAttribute($specific_price_attr_name, $product_id, $field, $product_attribute_id = null)
  {
    $specific_price_field_number = end(explode('_', $field));
    $specific_prices = SpecificPrice::getByProductId($product_id);

    if ($this->_separate) {
      if ($specific_prices[$specific_price_field_number - 1]['id_product_attribute'] != 0 && $specific_prices[$specific_price_field_number - 1]['id_product_attribute'] != $product_attribute_id) {
        return '';
      }
    }

    if (isset($specific_prices[$specific_price_field_number - 1][$specific_price_attr_name])) {
      return $specific_prices[$specific_price_field_number - 1][$specific_price_attr_name];
    }

  }
}
