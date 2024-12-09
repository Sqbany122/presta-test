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
     * @throws \PrestaShopException
     * @throws \PrestaShopModuleException
     */
    public static function makeConsentAccept($data = null)
    {
        return self::make(\GdprActivityLog::ACTIVITY_TYPE_COOKIE_ACCEPT, "Cookie accepted", $data);
    }

    /**
     * @param null $customer
     * @return \GdprActivityLog
     * @throws \PrestaShopModuleException
     */
    public static function makeSignupFormConsent($customer = null)
    {
        return self::make(\GdprActivityLog::ACTIVITY_TYPE_REGISTRATION, "Signup form consent accepted", "", $customer);
    }

    /**
     * @return \GdprActivityLog
     * @throws \PrestaShopException
     * @throws \PrestaShopModuleException
     */
    public static function makeContactFormConsent()
    {
        return self::make(\GdprActivityLog::ACTIVITY_TYPE_REGISTRATION, "Contact form consent accepted", "");
    }

    /**
     * @param null $customer
     * @return \GdprActivityLog
     * @throws \PrestaShopModuleException
     */
    public static function makeNewsletterConsent($customer = null)
    {
        return self::make(
            \GdprActivityLog::ACTIVITY_TYPE_NEWSLETTER_ACCEPT,
            'Newsletter consent accepted',
            "",
            $customer
        );
    }

    /**
     * @return \GdprActivityLog
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
     * @param null $customer
     * @return \GdprActivityLog
     * @throws \PrestaShopModuleException
     */
    protected static function make($type, $subject, $data, $customer = null)
    {
        if (!$customer) {
            $customer = \Context::getContext()->customer;
        }

        $activity = new \GdprActivityLog();
        $activity->id_customer = $customer->id;
        $activity->id_guest = $customer->id_guest;
        $activity->activity_type = $type;
        $activity->activity_subject = $subject;
        $activity->activity_data = $data;
        if (!$activity->save()) {
            throw new \PrestaShopModuleException("Can't save GDPR activity log");
        }

        return $activity;
    }
}
