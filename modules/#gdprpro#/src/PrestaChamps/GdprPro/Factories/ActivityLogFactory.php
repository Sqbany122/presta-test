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

namespace PrestaChamps\GdprPro\Models;

class ActivityLogFactory
{
    /**
     * @return \GdprActivityLog
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws \PrestaShopModuleException
     */
    public static function makeConsentAccept($data = null)
    {
        return self::make(\GdprActivityLog::ACTIVITY_TYPE_COOKIE_ACCEPT, "Cookie accepted", $data);
    }

    /**
     * @return \GdprActivityLog
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws \PrestaShopModuleException
     */
    public static function makeSignupFormConsent()
    {
        return self::make(\GdprActivityLog::ACTIVITY_TYPE_REGISTRATION, "Signup form consent accepted", "");
    }

    /**
     * @return \GdprActivityLog
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws \PrestaShopModuleException
     */
    public static function makeContactFormConsent()
    {
        return self::make(\GdprActivityLog::ACTIVITY_TYPE_REGISTRATION, "Contact form consent accepted", "");
    }

    /**
     * @return \GdprActivityLog
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws \PrestaShopModuleException
     */
    public static function makeMyAccountFormConsent()
    {
        return self::make(\GdprActivityLog::ACTIVITY_TYPE_PROFILE_UPDATE, "Profile update form consent accepted", "");
    }

    /**
     * @param $type
     * @param $subject
     * @param $data
     *
     * @return \GdprActivityLog
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws \PrestaShopModuleException
     */
    protected static function make($type, $subject, $data)
    {
        $activity = new \GdprActivityLog();
        $activity->id_customer = \Context::getContext()->customer->id;
        $activity->id_guest = \Context::getContext()->customer->id_guest;
        $activity->activity_type = $type;
        $activity->activity_subject = $subject;
        $activity->activity_data = $data;
        if (!$activity->save()) {
            throw new \PrestaShopModuleException("Can't save GDPR activity log");
        }

        return $activity;
    }
}
