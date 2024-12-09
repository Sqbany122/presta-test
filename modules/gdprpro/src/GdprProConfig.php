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
 * Class GdprProConfig A configuration class holding the modules config
 */
class GdprProConfig
{
    public static $className = 'GdprProConfig';

    const MODULES_TO_UNLOAD = "gdpr-pro_modules_to_unload";

    const TAB_TEXT_WELCOME = "gdpr-pro_tab_text_welcome";
    const TAB_NAME_WELCOME = "gdpr-pro_tab_name_welcome";


    const TAB_NAME_LINK    = "gdpr-pro_tab_name_link";
    const TAB_CONTENT_LINK = "gdpr-pro_tab_content_link";
    const TAB_CONTENT_PAGE_ID = "gdpr-pro_tab_content_page_id";

    const FOOTER_LINK_TEXT         = "gdpr-pro-footer-link-text";
    const FOOTER_LINK_BG_COLOR     = "gdpr-pro-footer-link-bg-color";
    const FOOTER_LINK_TEXT_COLOR   = "gdpr-pro-footer-link-text-color";
    const FOOTER_LINK_BORDER_COLOR = "gdpr-pro-footer-link-border-color";

    const TAB_TEXT_NECESSARY    = "gdpr-pro_tab_text_necessary";
    const TAB_TEXT_PREFERENCES  = "gdpr-pro_tab_text_preferences";
    const TAB_TEXT_STATISTICS   = "gdpr-pro_tab_text_statistics";
    const TAB_TEXT_MARKETING    = "gdpr-pro_tab_text_marketing";
    const TAB_TEXT_UNCLASSIFIED = "gdpr-pro_tab_text_unclassified";

    const CONSENT_CHKBOX_SIGNUP_TEXT   = "gdpr-pro_consent_signup_text";
    const CONSENT_CHKBOX_SIGNUP_ENABLE = "gdpr-pro_consent_signup_enable";

    const CONSENT_CHKBOX_MYACCOUNT_TEXT   = "gdpr-pro_consent_myaccount_text";
    const CONSENT_CHKBOX_MYACCOUNT_ENABLE = "gdpr-pro_consent_myaccount_enable";

    const CONSENT_CHKBOX_CONTACT_TEXT   = "gdpr-pro_consent_contact_text";
    const CONSENT_CHKBOX_CONTACT_ENABLE = "gdpr-pro_consent_contact_enable";

    const CONSENT_CHKBOX_NEWSLETTER_TEXT   = "gdpr-pro_consent_newsletter_text";
    const CONSENT_CHKBOX_NEWSLETTER_ENABLE = "gdpr-pro_consent_newsletter_enable";

    const SAVE_BTN_BG_COLOR   = "gdpr-pro_consent_save_btn_bg_color";
    const SAVE_BTN_TEXT_COLOR = "gdpr-pro_consent_save_btn_text_color";

    const ACCEPT_ALL_BTN_BG_COLOR   = "gdpr-pro_consent_accept_all_bg_color";
    const ACCEPT_ALL_BTN_TEXT_COLOR = "gdpr-pro_consent_accept_all_text_color";

    const REJECT_ALL_BTN_BG_COLOR   = "gdpr-pro_consent_reject_all_bg_color";
    const REJECT_ALL_BTN_TEXT_COLOR = "gdpr-pro_consent_reject_all_text_color";
    const REJECT_ALL_BTN_SHOW = "gdpr-pro_consent_reject_all_show";

    const POPUP_POSITION = "gdpr-pro_popup-position";

    const POPUP_BG_COLOR   = "gdpr-pro_popup-bg-color";
    const POPUP_TEXT_COLOR = "gdpr-pro_popup-text-color";

    const POPUP_STYLE      = "gdpr-pro_popup-style";
    const POPUP_SHOW_TITLE = "gdpr-pro_show-title";

    const UNDER_16_ENABLE = "gdpr-pro_under_16_enable";

    const UNDER_18_ENABLE     = "gdpr-pro_under_18_enable";
    const UNDER_18_ALERT_TEXT = "gdpr-pro_under_18_alert_text";

    const RELOAD_AFTER_BTN_SAVE       = 'gdpr-pro-reload-after-btn-save';
    const RELOAD_AFTER_BTN_ACCEPT_ALL = 'gdpr-pro-reload-after-btn-accept-all';

    const CHECK_ALL_MODULES_BY_DEFAULT = "gdpr-pro_check-all-modules-by-default";
    const ALLOW_ALL_MODULES_BY_DEFAULT = "gdpr-pro_allow-all-modules-by-default";

    const ENABLE_COOKIE_ACCEPT_CONSENT_LOG = "gdpr-pro_enable_cookie_accept_consent_log";

    const CLOSE_MODAL_ONLY_WITH_BUTTONS = 'gdpr-pro-close-modal-only-with-buttons';

    const ENABLE_SCROLL_LOCK = 'gdpr-pro-enable-scroll-lock';

    const ENABLE_ACCEPT_BY_SCROLL = 'gdpr-pro-enable-accept-by-scroll';

    const HIDE_POPUP_FOR_BOTS = 'gdpr-pro-hide-popup-for-bots';

    const ENABLE_DO_NOT_TRACK_COMPLIANCE = 'gdpr-pro-enable-do-not-track-compliance';

    const POPUP_OVERLAY_BACKGROUND_COLOR = 'gdpr-pro_popup-overlay-bg-color';
    const POPUP_OVERLAY_BACKGROUND_OPACITY = 'gdpr-pro_popup-overlay-bg-opacity';

    public static $multiLang = array(
        self::TAB_CONTENT_LINK,
        self::TAB_NAME_LINK,
        self::FOOTER_LINK_TEXT,
        self::TAB_TEXT_NECESSARY,
        self::TAB_TEXT_PREFERENCES,
        self::TAB_TEXT_STATISTICS,
        self::TAB_TEXT_MARKETING,
        self::TAB_TEXT_UNCLASSIFIED,
        self::TAB_TEXT_WELCOME,
        self::TAB_NAME_WELCOME,
        self::CONSENT_CHKBOX_SIGNUP_TEXT,
        self::CONSENT_CHKBOX_MYACCOUNT_TEXT,
        self::CONSENT_CHKBOX_CONTACT_TEXT,
        self::CONSENT_CHKBOX_NEWSLETTER_TEXT,
        self::UNDER_18_ALERT_TEXT,
    );

    /**
     * @param bool $activeOnly
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getModulesToUnload($activeOnly = false)
    {
        require_once dirname(__FILE__) . '/../vendor/autoload.php';
        if ($activeOnly) {
            $modules = unserialize(Configuration::get(GdprProConfig::MODULES_TO_UNLOAD));
            if (is_array($modules)) {
                $modules = array_filter($modules, function ($item) {
                    if (isset($item['enabled']) && $item['enabled'] == 1) {
                        return true;
                    }
                    return false;
                });

                foreach ($modules as $name => $module) {
                    if (!Module::isInstalled($name) || !Module::isEnabled($name)) {
                        unset($modules[$name]);
                    }
                }

                return array_merge($modules, GdprCustomScript::getUnHookableScripts());
            } else {
                return array();
            }
        }
        return unserialize(Configuration::get(GdprProConfig::MODULES_TO_UNLOAD));
    }

    /**
     * Set the modules which will be unhooked
     *
     * @param array $modulesToUnload
     *
     * @return bool
     */
    public static function setModulesToUnload($modulesToUnload = array())
    {
        return Configuration::updateValue(
            GdprProConfig::MODULES_TO_UNLOAD,
            serialize($modulesToUnload)
        );
    }

    /**
     * Save a config value
     *
     * @param $key
     * @param $value
     *
     * @return bool
     */
    public static function saveValue($key, $value)
    {
        return Configuration::updateValue($key, $value);
    }

    /**
     * Get configuration keys and values
     *
     * @return array
     */
    public static function getConfigurationValues()
    {
        try {
            $class = new ReflectionClass(self::$className);
            $values = array();
            foreach ($class->getConstants() as $constant) {
                if (is_string($constant)) {
                    if (in_array($constant, self::$multiLang, true)) {
                        static::getMultilangConfigValues($constant, $values);
                    } else {
                        $values[$constant] = Configuration::get($constant);
                    }
                }
            }
            unset($values[self::MODULES_TO_UNLOAD]);
            return $values;
        } catch (Exception $exception) {
            return array();
        }
    }

    /**
     * Get a multilang config key (mainly used with the HelperForm class)
     *
     * @param $key
     * @param $values
     */
    private static function getMultilangConfigValues($key, &$values)
    {
        $languages = Language::getLanguages(false, false, false);
        $values[$key] = array();
        foreach ($languages as $language) {
            $values[$key][$language['id_lang']] = Configuration::get($key, $language['id_lang']);
        }
    }

    /**
     * Decide if a config key exists in the DB or not, doesn't really care about multilang
     *
     * @param null $configKey
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public static function configExists($configKey = null)
    {
        $query = new \DbQuery();
        $query->select('count(*)');
        $query->from('configuration');
        $query->where("name = '" . pSQL($configKey) . "'");

        return (int)Db::getInstance()->getValue($query) > 0;
    }
}
