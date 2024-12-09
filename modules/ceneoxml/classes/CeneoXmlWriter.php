<?php

class CeneoXmlWriter extends XmlWriter
{
    private $context;
    
    public function __construct($context)
    {
        $this->context = $context;
    }
    
    public function setAttribute($name, $value)
    {
        $this->startAttribute($name);
            $this->text($value);
        $this->endAttribute();	
    }
    
    public function AddCategoryToXml(CategoryPathInfo $category_info)
    {
        $this->startElement('cat');
            $this->writeCData(
                mb_substr($category_info->getCategory_path(), 
                        -CeneoConfig::XmlCategoryPathLength, 
                        $category_info->getCategoryPath_length(), 
                        CeneoConfig::Encoding
                    )
            );
        $this->endElement();
    }
    
    public function AddNameToXml($name)
    {
        $this->startElement('name');
            $this->writeCData(
                mb_substr($name, 0, CeneoConfig::XmlProductNameLength, CeneoConfig::Encoding)
            );
        $this->endElement();
    }
    
    public function AddDescriptionToXml($desc)
    {
        $this->startElement('desc');
            $this->writeCData(
                    mb_substr(htmlspecialchars_decode($desc), 0, CeneoConfig::XmlProductDesc, CeneoConfig::Encoding)
            );
        $this->endElement();
    }
    
    public function AddImagesToXml($images, $link_rewrite)
    {
        if(!empty($images))
        {
            $imgs_tag_added = false;

            foreach ($images as $key => $value) 
            {
                $image_link = $this->context->link->getImageLink($link_rewrite, $value['id_image'], 'thickbox_default');

                if(strlen($image_link) <= CeneoConfig::XmlImageLength)
                {
                    if(!$imgs_tag_added)
                    {
                        $this->startElement('imgs');
                        $imgs_tag_added = true;
                    }

                    $img_tag_name = $key == 0 ? 'main' : 'i';

                    $this->startElement($img_tag_name);
                            $this->setAttribute('url', $image_link);
                    $this->endElement();
                }
            }

            if($imgs_tag_added)
            {
                $this->endElement();
            }
        }
    }
    
    public function AddCeneoAttribute($name, $value)
    {
        if(!empty($value))
        {
            $this->startElement('a');
                $this->setAttribute('name',$name);
                $this->writeCData($value);					
            $this->endElement();
        }
    }
}