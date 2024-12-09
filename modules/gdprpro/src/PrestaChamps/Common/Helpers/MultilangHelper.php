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

namespace PrestaChamps\Common\Helpers;

/**
 * Class MultilangHelper
 *
 * @package PrestaChamps\Common\Helpers
 */
class MultilangHelper
{
    public static function stringToMultilangArray($source)
    {
        $return = array();
        foreach (\Language::getLanguages() as $language) {
            $return[(int)$language['id_lang']] = $source;
        }

        return $return;
    }
}
