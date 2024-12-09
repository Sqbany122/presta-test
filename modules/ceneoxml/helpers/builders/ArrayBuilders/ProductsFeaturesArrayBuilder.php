<?php
require_once(__CENEO_XML_MODULE_PATH__ . 'classes' . DIRECTORY_SEPARATOR . 'FeatureEnum.php');

class ProductsFeaturesArrayBuilder
{
    public static function Build($features)
    {
        $features_by_id = array();

        foreach ($features as $feature) 
        {
            $feature_id = (FeatureEnum::Weight == $feature['id_feature']) ? $feature['id_feature'] : $feature['name'];
            $features_by_id[$feature['id_product']][$feature_id] = $feature['value'];
        }

        return $features_by_id;
    }
}