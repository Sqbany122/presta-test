<?php

abstract class UrlProviderBase
{
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    abstract public function getProductLink($product_id, $category_id, $prod_attr_id);
}