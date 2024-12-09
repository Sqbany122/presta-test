<?php
/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */

/**
 * Class AdminGdprCustomScriptsController
 */
class AdminGdprCustomScriptsController extends ModuleAdminController
{
    public $actions_available   = array();
    protected $position_identifier = 'id_gdpr_custom_script';

    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;
        $this->table = GdprCustomScript::$definition['table'];
        $this->className = GdprCustomScript::class;

        $this->actions = array('edit');
        $this->identifier = GdprCustomScript::$definition['primary'];

        $this->_orderBy = GdprCustomScript::$definition['primary'];
        $this->_orderWay = 'ASC';
        $this->_select = "a.*";

        $this->fields_list = array(
            'id_gdpr_custom_script' => array(
                'align' => 'center',
                'title' => 'ID',
                'class' => 'fixed-width-xs',
                'type'  => 'text',
            ),
            'internal_name'         => array(
                'align' => 'center',
                'title' => 'Name',
                'type'  => 'text',
            ),
            'category'              => array(
                'align'      => 'center',
                'title'      => 'Category',
                'type'       => 'select',
                'list'       => array_combine(
                    GdprPro::$cookieCategories,
                    (GdprPro::$cookieCategories)
                ),
                'filter_key' => 'category',
            ),
            'active'                => array(
                'title'   => $this->l('Status'),
                'type'    => 'bool',
                'active'  => 'status',
                'align'   => 'center',
                'ajax'    => true,
                'orderby' => false,
            ),
            'position'              => array(
                'title'    => $this->l('Position'),
                'class'    => 'fixed-width-xs',
                'position' => 'position',
            ),
        );
    }

    public function renderForm()
    {
        $this->context->controller->addJS(
            $this->module->getLocalPath() .
            'views/js/codemirror/codemirror.js'
        );
        $this->context->controller->addJS(
            $this->module->getLocalPath() .
            'views/js/codemirror/mode-css.js'
        );
        $this->context->controller->addJS(
            $this->module->getLocalPath() .
            'views/js/codemirror/mode-javascript.js'
        );
        $this->context->controller->addJS(
            $this->module->getLocalPath() .
            'views/js/custom-scripts-editor.js'
        );
        $this->context->controller->addCSS(
            $this->module->getLocalPath() .
            'views/css/codemirror/codemirror.css'
        );
        $this->context->controller->addCSS(
            $this->module->getLocalPath() .
            'views/css/codemirror/theme-material.css'
        );

        $this->fields_form = array(
            'tinymce' => true,
            'legend'  => array(
                'title' => $this->l('Custom script'),
                'icon'  => 'icon-tags',
            ),
            'input'   => array(
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Internal name'),
                    'name'     => 'internal_name',
                    'lang'     => false,
                    'required' => true,
                ),
                array(
                    'type'    => 'switch',
                    'name'    => 'active',
                    'label'   => $this->l('Active'),
                    'is_bool' => true,
                    'values'  => array(
                        array(
                            'id'    => 'active_on',
                            'value' => '1',
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => '0',
                        ),
                    ),
                ),
                array(
                    'type'    => 'switch',
                    'name'    => 'keep_inline',
                    'label'   => $this->l('Keep inline js scripts'),
                    'is_bool' => true,
                    'values'  => array(
                        array(
                            'id'    => 'keep_inline_on',
                            'value' => '1',
                        ),
                        array(
                            'id'    => 'keep_inline_off',
                            'value' => '0',
                        ),
                    ),
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Expiry'),
                    'name'     => 'expiry',
                    'lang'     => true,
                    'required' => true,
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Provider'),
                    'name'     => 'provider',
                    'lang'     => true,
                    'required' => true,
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Frontend name'),
                    'name'     => 'frontend_name',
                    'lang'     => true,
                    'required' => true,
                ),
                array(
                    'type'     => 'textarea',
                    'label'    => $this->l('Description'),
                    'name'     => 'description',
                    'lang'     => true,
                    'required' => true,
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('External CSS'),
                    'name'     => 'external_css',
                    'lang'     => false,
                    'required' => false,
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('External JS'),
                    'name'     => 'external_js',
                    'lang'     => false,
                    'required' => false,
                ),
                array(
                    'type'     => 'textarea',
                    'label'    => $this->l('Inline CSS'),
                    'name'     => 'inline_css',
                    'lang'     => false,
                    'required' => false,
                ),
                array(
                    'type'     => 'textarea',
                    'label'    => $this->l('Inline JS'),
                    'name'     => 'inline_js',
                    'lang'     => false,
                    'required' => false,
                ),
                array(
                    'type'    => 'select',
                    'name'    => 'category',
                    'label'   => $this->l('Script category'),
                    'options' => array(
                        'query' => array(
                            array(
                                'id'   => GdprPro::COOKIE_CATEGORY_NECESSARY,
                                'name' => $this->l('Necessary'),
                            ),
                            array(
                                'id'   => GdprPro::COOKIE_CATEGORY_PREFERENCES,
                                'name' => $this->l('Preferences'),
                            ),
                            array(
                                'id'   => GdprPro::COOKIE_CATEGORY_STATISTICS,
                                'name' => $this->l('Statistics'),
                            ),
                            array(
                                'id'   => GdprPro::COOKIE_CATEGORY_MARKETING,
                                'name' => $this->l('Marketing'),
                            ),
                            array(
                                'id'   => GdprPro::COOKIE_CATEGORY_UNCLASSIFIED,
                                'name' => $this->l('Unclassified'),
                            ),
                        ),
                        'id'    => 'id',
                        'name'  => 'name',
                    ),
                ),
            ),
            'submit'  => array(
                'title' => $this->l('Save'),

            ),

        );
        return parent::renderForm();
    }


    /**
     * Ajax process action for update positions
     */
    public function ajaxProcessUpdatePositions()
    {
        try {
            $way = (int)(Tools::getValue('way'));
            $id_block = (int)(Tools::getValue('id'));
            $positions = Tools::getValue($this->table);
        } catch (Exception $exception) {
            $this->ajaxDie(array('hasError' => true, 'errors' => array($exception->getMessage())));
        }

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);
            if (isset($pos[2]) && (int)$pos[2] === $id_block) {
                try {
                    $model = new GdprCustomScript((int)$pos[2]);
                    if (is_numeric($model->id_gdpr_custom_script)) {
                        $model->updatePosition($way, $position);
                        $this->ajaxDie(json_encode(array('hasError' => false)));
                    } else {
                        $this->ajaxDie(json_encode(array('hasError' => true, 'errors' => array("Can't load block"))));
                    }
                } catch (Exception $e) {
                    $this->ajaxDie(
                        json_encode(array('hasError' => true, 'errors' => array("Exception {$e->getMessage()}")))
                    );
                }
            }
        }
        $this->ajaxDie(json_encode(array('hasError' => true, 'errors' => array("Unknown"))), false, false);
    }

    /**
     * Process the status change of an object
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function ajaxProcessStatusGdprCustomScript()
    {
        if (!$id_category = (int)Tools::getValue(GdprCustomScript::$definition['primary'])) {
            $content =
                json_encode(
                    array(
                        'success' => false,
                        'error'   => true,
                        'text'    => $this->l('Failed to update the status'),
                    )
                );
            die($content);
        } else {
            $category = new GdprCustomScript((int)$id_category);
            if (Validate::isLoadedObject($category)) {
                $category->active = $category->active == 1 ? 0 : 1;

                if ($category->save()) {
                    $content = json_encode(
                        array(
                            'success' => true,
                            'text'    => $this->l('The status has been updated successfully'),
                        )
                    );
                } else {
                    $content = json_encode(
                        array(
                            'success' => false,
                            'error'   => true,
                            'text'    => $this->l('Failed to update the status'),
                        )
                    );
                }
                die($content);
            }
        }
    }
}
