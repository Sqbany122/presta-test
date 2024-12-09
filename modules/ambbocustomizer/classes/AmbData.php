<?php
/**
 *   ambBOcustomizer Module : customize the prestashop back-office
 *
 *    @author    Ambris Informatique
 *    @copyright Copyright (c) 2013-2015 Ambris Informatique SARL
 *    @license   Commercial license
 *    @module    BO Customizer (AmbBoCustomizer)
 *    @file      classes/AmbData.php
 *    @subject   Data Structure loaded from json files
 *
 *            Support by mail: support@ambris.com
 */

class AmbData
{

    protected $fields;
    protected $params;
    protected $name;
    protected $data_dir;
    protected $params_dir = 'params/';
    protected $default_params_dir = 'default_params/';
    protected $fields_dir = 'fields/';
    protected $custom_fields_dir = 'custom_fields/';
    public $controller_name;
    public static $amb_cached_tabnames = array();
    public $default_views;

    public function __construct($name)
    {
        $this->data_dir = _PS_MODULE_DIR_ . 'ambbocustomizer/data/';
        $this->name = $name;

        $tab = Tab::getTab(Context::getContext()->language->id, Tab::getIdFromClassName($name));
        $this->controller_name = $tab['class_name'];
        $this->view_name = $this->getViewName();
        $this->fields = $this->loadFields();

        if ($this->fields) {
            $this->params = $this->loadParams();
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getViewName()
    {
        $context = Context::getContext();

        if (!isset($context->cookie->bocustomizer)) {
            $context->cookie->bocustomizer = serialize(array());
        }

        $settings = unserialize($context->cookie->bocustomizer);

        if (Tools::getValue('amb_customizer_view')) {
            $view_id = md5(time());
        } elseif (Tools::getValue('amb_customizer_view_id')) {
            $view_id = Tools::getValue('amb_customizer_view_id');
        } elseif (isset($settings[$this->controller_name]) && $settings[$this->controller_name] != '') {
            $view_id = $settings[$this->controller_name];
        } else {
            $view_id = 'default';
        }

        if (Tools::getValue('delete_view') && $view_id == Tools::getValue('delete_view')) {
            $view_id = 'default';
        }

        $settings[$this->controller_name] = $view_id;
        $context->cookie->bocustomizer = serialize($settings);

        // die(print_r($cookie));
        return $view_id;
    }

    public function viewChanged()
    {
        //  $cookie = new Cookie('amb_customizer_view-' . $this->name);
        $cookie = &Context::getContext()->cookie;
        $val = Tools::getValue('amb_customizer_view_id', null);

        if ($val != null) {
            return true;
        } else {
            return false;
        }
    }

    public function getViewDisplayName()
    {
        if (Tools::getValue('amb_customizer_view')) {
            $name = Tools::getValue('amb_customizer_view');
        } else {
            $name = 'Default';
        }

        return $name;
    }

    public function getViewNames()
    {
        $view_names = array();
        foreach ($this->params->views as $name => $view) {
            $view_names[$name] = $view->display_name;
        }

        return $view_names;
    }

    public function getAllFields()
    {
        return $this->fields->fields;
    }

    public function getAllFieldsCount()
    {
        return count((array) $this->getAllFields());
    }

    public function getParams()
    {
        if (isset($this->params->views->{$this->view_name}->params)) {
            return $this->params->views->{$this->view_name}->params;
        } else {
            return false;
        }
    }

    public function getHeaders()
    {
        return $this->fields->headers;
    }

    public function getControllerName()
    {
        return $this->name;
    }

    public function getTabName()
    {
        if (count(self::$amb_cached_tabnames) == 0) {
            $unordered = Tab::getTabs(Context::getContext()->language->id);

            foreach ($unordered as $tab) {
                self::$amb_cached_tabnames[$tab['class_name']] = $tab['name'];
            }
        }

        return self::$amb_cached_tabnames[$this->getControllerName()];
    }

    public function getFieldParam($name)
    {
        if (property_exists($this->getParams(), $name)) {
            return $this->getParams()->{$name};
        } else {
            return array('field_active' => false);
        }
    }

    public function getConstrainedFields()
    {
        $fields = $this->getAllFields();

        $constrained_fields = array();
        $field_counter = 1;
        foreach ($this->getParams() as $field_name => $params) {
            if ($params->field_active && property_exists($fields, $field_name)) {
                if (property_exists($fields->{$field_name}, 'amb_callback')
                    && property_exists($fields->{$field_name}->amb_callback, 'args')
                    && property_exists($fields->{$field_name}->amb_callback->args, 'value')) {
                    $constrained_fields[str_repeat('_', $field_counter)] = $fields->{$field_name};
                    $field_counter++;
                } else {
                    $constrained_fields[$field_name] = $fields->{$field_name};
                }
            }
        }
        return $constrained_fields;
    }

    public function getConstrainedFieldsCount()
    {
        return count((array) $this->getConstrainedFields());
    }

    public function getOrderedFields()
    {
        $fields = (array) $this->getAllFields();
        $ordered_fields = array();
        foreach ($this->getParams() as $field_name => $params) {
            if (isset($fields[$field_name])) {
                $ordered_fields[$field_name] = array(
                    'active' => ($params->field_active ? 1 : 0),
                    'field' => $fields[$field_name],
                );
            }
        }
        return $ordered_fields;
    }

    public function generateDefaultParamsArray()
    {
        $defaults = array();
        $i = 0;
        foreach ($this->getAllFields() as $field_name => $field) {
            if (property_exists($field, 'is_core') && $field->is_core) {
                $defaults[$field_name] = array('field_active' => true);
            } else {
                $defaults[$field_name] = array('field_active' => false);
            }
            $i++;
        }

        return $defaults;
    }

    public function checkParams()
    {
        $nb_fields = $this->getAllFieldsCount();

        $fields = $this->getAllFields();

        $registered_fields = array();
        $unregistered_fields = array();
        foreach ($this->getParams() as $field_name => $params) {
            if (property_exists($fields, $field_name)) {
                $registered_fields[] = $field_name;
            } else {
                $unregistered_fields[] = $field_name;
            }
        }

        if (count($unregistered_fields) > 0) {
            $this->fixExcessiveFieldsInParams($unregistered_fields);
            $this->checkParams();
        }

        if (count($registered_fields) < $nb_fields) {
            $this->fixMissingFieldsInParams($fields, $registered_fields);
            $this->checkParams();
        }

        return true;
    }

    public function fixExcessiveFieldsInParams($unregistered_fields)
    {

        foreach ($unregistered_fields as $field_name) {
            unset($this->getParams()->{$field_name});
        }

        return $this->saveParams();
    }

    public function fixMissingFieldsInParams($fields, $registered_fields)
    {
        foreach ($fields as $field_name => $field) {
            if (!in_array($field_name, $registered_fields)) {
                $status = property_exists($field, 'is_core') && $field->is_core ? true : false;
                if (is_array($this->params->views->{$this->view_name}->params)) {
                    $this->params->views->{$this->view_name}->params[$field_name] = array('field_active' => $status);
                } else {
                    $this->params->views->{$this->view_name}->params->{$field_name} = array('field_active' => $status);
                }
            }
        }

        return $this->saveParams();
    }

    public function saveFileData($name, $folder, $data)
    {
        $filename = $this->data_dir . $folder . $name . '.json';

        $file = fopen($filename, 'w');
        fwrite($file, Tools::jsonEncode($data));
        fclose($file);

        return true;
    }

    public function getFileData($name, $folder)
    {
        $filename = $this->data_dir . $folder . $name . '.json';
        if (file_exists($filename)) {
            $json = Tools::file_get_contents($filename);
            $json = str_replace("\r", "", $json);
            $json = str_replace("\n", "", $json);
            $data = Tools::jsonDecode($json);

            return $data;
        } else {
            return false;
        }
    }

    public function isActive()
    {
        if (property_exists($this, 'params') && isset($this->params->active)) {
            return $this->params->active;
        } else {
            return false;
        }
    }

    public function updatePositions($fields)
    {
        $new_params = array();

        foreach ($fields as $field_name) {
            $new_params[$field_name] = $this->getFieldParam($field_name);
        }

        //error_log(print_r($new_params, true));
        return $this->saveParams($new_params);
    }

    public function updateSpecificParamsField($field, $value)
    {
        foreach ($this->getParams() as $field_name => $param) {
            if ($field_name == $field) {
                $this->getParams()->{$field_name} = $value;
                break;
            }
        }

        return $this->saveParams();
    }

    public function updateListActive($value)
    {
        $this->params->active = (bool) $value;
        return $this->saveParams();
    }

    public function updateDisplayName($view_name, $value)
    {
        $this->params->views->{$view_name}->display_name = $value;
        return $this->saveParams();
    }

    public function deleteView($view_name)
    {
        unset($this->params->views->{$view_name});
        return $this->saveParams();
    }

    public function loadFields()
    {
        $fields = $this->getFileData($this->name, $this->fields_dir);
        $custom_fields = $this->getFileData($this->name, $this->custom_fields_dir);

        if ($custom_fields && count($custom_fields) > 0) {
            foreach ($custom_fields as $name => $custom_field) {
                $fields->fields->$name = $custom_field;
            }
        }

        return $fields;
    }

    public function saveParams($params = null, $view_specific = true)
    {
        if ($params != null) {
            if ($view_specific) {
                $this->params->views->{$this->view_name}->params = $params;
            } else {
                $this->params = $params;
            }
        }

        $this->saveFileData($this->getControllerName(), $this->params_dir, $this->params);
        $this->params = $this->loadParams();
    }

    public function loadParams()
    {

        $loaded = $this->getFileData($this->name, $this->params_dir);
        $loaded_defaults = $this->getFileData($this->name, $this->default_params_dir);

        if ($loaded === false) {
            //Create basic file structure with 1 view
            $this->saveParams(
                array(
                    'active' => false,
                    'views' => array(
                        'default' => array('display_name' => 'Default', 'params' => array()),
                    ),
                ),
                false
            );
        } elseif (!property_exists($loaded, 'views')) {
            //Resolve v1.0.0 compatibility issues
            $this->setJsonData(
                $loaded,
                'views',
                array('default' => array('display_name' => 'Default', 'params' => $loaded->params))
            );
            unset($loaded->params);
            $this->saveParams($loaded, false);
        }

        $loaded = $this->getFileData($this->name, $this->params_dir);
        $added = false;

        if (is_object($loaded_defaults)) {
            foreach ($loaded_defaults->views as $view_name => $view) {
                $this->default_views[] = $view_name;
                if (!in_array($view_name, array_keys((array) $loaded->views))) {
                    $this->setJsonData($loaded->views, $view_name, $view);
                    $added = true;
                }
            }
            if ($added) {
                $this->saveParams($loaded, false);
            }
        }

        if (!property_exists($loaded->views, $this->view_name)) {
            //Create structure for new view
            $this->setJsonData(
                $loaded->views,
                $this->view_name,
                array('display_name' => $this->getViewDisplayName(), 'params' => array())
            );
            $this->saveParams($loaded, false);
        } elseif (Tools::getValue('delete_view') && property_exists($loaded->views, Tools::getValue('delete_view'))) {
            unset($loaded->views->{Tools::getValue('delete_view')});
            $this->saveParams($loaded, false);
            $this->view_name = $this->getViewName();
        }

        $loaded = $this->getFileData($this->name, $this->params_dir);
        return $loaded;
    }

    public function setJsonData(&$data, $key, $value)
    {
        if (is_array($data)) {
            $data[$key] = $value;
        } else {
            $data->$key = $value;
        }
    }

    public static function isCore($field)
    {
        if (property_exists($field, 'is_core') && $field->is_core) {
            return true;
        } else {
            return false;
        }
    }

    public static function generateTranslatables()
    {
        $filename = _PS_MODULE_DIR_ . 'ambbocustomizer/translatables.txt';

        $wfile = fopen($filename, 'w');

        $files = scandir(_PS_MODULE_DIR_ . 'ambbocustomizer/data/fields');
        $array_to_write = array();
        foreach ($files as $file) {
            if (preg_match('/(.+)\.json/', $file, $matches)) {
                $controller_name = $matches[1];

                $amb_data = new AmbData($controller_name);
                foreach ($amb_data->getAllFields() as $key => $field) {
                    if (!self::isCore($field)) {
                        if (!property_exists($field, 'translator')) {
                            if (!empty($field->title)) {
                                $array_to_write[$file][$key][] = '$this->l(\'' . $field->title . '\');';
                            }
                        }
                    }

                    if (!empty($field->description) && ($field->description != $field->title)) {
                        $array_to_write[$file][$key][] = '$this->l(\'' . $field->description . '\');';
                    }
                }
            }
        }

        $to_write = '';
        foreach ($array_to_write as $file => $key) {
            $to_write .= '//' . $file . "\n";
            foreach ($key as $k => $values) {
                foreach ($values as $value) {
                    $to_write .= $value . "\n";
                }
            }

            $to_write .= "\n";
        }

        fwrite($wfile, $to_write);

        fclose($wfile);

        return true;
    }
}
