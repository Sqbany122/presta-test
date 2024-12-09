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
 * Class GdprCustomScriptRegisterService
 *
 * Registers the necessary custom scripts and styles to the controller
 */
class GdprCustomScriptRegisterService
{
    /**
     * @var $controller FrontController
     */
    protected $controller;

    /**
     * @var $customScripts GdprCustomScript[]
     */
    protected $customScripts;
    /**
     * @var $inlineCss string[]
     */
    protected $inlineCss = array();

    /**
     * @var $inlineJs string[]
     */
    protected $inlineJs = array();

    protected $allowAllByDefault = false;

    public function __construct(FrontController $controller, array $customScripts = array())
    {
        $this->controller = $controller;
        $this->customScripts = $customScripts;
        $this->allowAllByDefault = (bool)Configuration::get(GdprProConfig::ALLOW_ALL_MODULES_BY_DEFAULT) && !Context::getContext()->cookie->gdpr_windows_was_opened;
        $this->register();
    }

    /**
     * Actually register the scripts
     */
    protected function register()
    {
        foreach ($this->customScripts as $customScript) {
            try {
                if ($this->isScriptEnabled($customScript) ||
                    $customScript['category'] == GdprPro::COOKIE_CATEGORY_NECESSARY
                ) {
                    if ($customScript['inline_js']) {
                        $this->inlineJs[] = array(
                            'content' => $customScript['inline_js'],
                            'keep_inline' => $customScript['keep_inline'],
                        );
                    }
                    if ($customScript['inline_css']) {
                        $this->inlineCss[] = $customScript['inline_css'];
                    }

                    if (GdprPro::isPs17()) {
                        if ($customScript['external_js']) {
                            $this->controller->registerJavascript($customScript['module_id'], $customScript['external_js'], array('server' => 'remote', 'position' => 'head', 'priority' => 20));
                        }
                        if ($customScript['external_css']) {
                            $this->controller->registerStylesheet($customScript['module_id'], $customScript['external_css'], array('server' => 'remote', 'position' => 'head', 'priority' => 20));
                        }
                    } else {
                        if ($customScript['external_js']) {
                            $this->controller->addJS($customScript['external_js']);
                        }
                        if ($customScript['external_css']) {
                            $this->controller->addCSS($customScript['external_css']);
                        }
                    }
                }
            } catch (Exception $exception) {
                PrestaShopLogger::addLog(
                    $exception->getMessage(),
                    2,
                    $exception->getCode(),
                    'GdprCustomScript',
                    null,
                    true
                );
                continue;
            }
        }
    }

    /**
     * Decide if a script should be loaded or not
     *
     * @param $customScript
     *
     * @return bool
     */
    public function isScriptEnabled($customScript)
    {
        if ($this->allowAllByDefault) {
            return true;
        }
        $gdprCookieContent = GdprProCookie::getInstance()->content;

        if (isset($gdprCookieContent[$customScript['module_id']]) &&
            $gdprCookieContent[$customScript['module_id']] == 'true') {
            return true;
        }
        if ($customScript == GdprPro::COOKIE_CATEGORY_NECESSARY) {
            return true;
        }

        return false;
    }

    /**
     * Render together the inline js scripts
     *
     * @return string
     */
    public function getInlineJs()
    {
        $scripts = "";
        foreach ($this->inlineJs as $js) {
            try {
                $scripts .= \PrestaChamps\Common\Helpers\HtmlHelper::script(
                    $js['content'],
                    ($js['keep_inline']) ? array('data-keepinline' => 'true') : array()
                );
            } catch (Exception $e) {
                PrestaShopLogger::addLog(
                    $e->getMessage(),
                    2,
                    $e->getCode(),
                    'GdprCustomScript',
                    null,
                    true
                );
                continue;
            }
        }
        return $scripts;
    }

    /**
     * Render together the inline css styles
     *
     * @return string
     */
    public function getInlineCss()
    {
        $scripts = "";

        foreach ($this->inlineCss as $css) {
            try {
                $scripts .= \PrestaChamps\Common\Helpers\HtmlHelper::style($css);
            } catch (Exception $e) {
                PrestaShopLogger::addLog(
                    $e->getMessage(),
                    2,
                    $e->getCode(),
                    'GdprCustomScript',
                    null,
                    true
                );
                continue;
            }
        }

        return $scripts;
    }
}
