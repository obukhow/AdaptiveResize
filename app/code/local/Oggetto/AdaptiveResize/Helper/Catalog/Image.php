<?php

/**
 * Oggetto extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Oggetto AdaptiveResize module to newer versions in the future.
 * If you wish to customize the Oggetto AdaptiveResize module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_AdaptiveResize
 * @copyright  Copyright (C) 2012 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rewrites helper to Add Adaptive Resize Method
 *
 * @category   Oggetto
 * @package    Oggetto_AdaptiveResize
 * @subpackage Helper
 * @author     Denis Obukhov <denis.obukhov@oggettoweb.com>
 */
class Oggetto_AdaptiveResize_Helper_Catalog_Image extends Mage_Catalog_Helper_Image
{
    /**
     * Adpative resize flag
     *
     * @var bool
     */
    protected $_scheduleAdaptiveResize = false;

    /**
     * Reset all previos data
     *
     * @return Oggetto_AdaptiveResize_Helper_Catalog_Image
     */
    protected function _reset()
    {
        $this->_scheduleAdaptiveResize = false;
        parent::_reset();
    }

    /**
     * Adaptive resize method
     *
     * @param int      $width  image width
     * @param int|null $height image height
     *
     * @return \Oggetto_AdaptiveResize_Helper_Catalog_Image
     */
    public function adaptiveResize($width, $height = null)
    {
        $this->_getModel()->setWidth($width)->setHeight($height);
        $this->_scheduleAdaptiveResize = true;
        return $this;
    }

    /**
     * Return generated image url
     *
     * @return string
     */
    public function __toString()
    {
        try {
            if ($this->getImageFile()) {
                $this->_getModel()->setBaseFile($this->getImageFile());
            } else {
                $this->_getModel()->setBaseFile($this->getProduct()
                        ->getData($this->_getModel()->getDestinationSubdir()));
            }

            if ($this->_getModel()->isCached()) {
                return $this->_getModel()->getUrl();
            } else {
                if ($this->_scheduleRotate) {
                    $this->_getModel()->rotate($this->getAngle());
                }

                if ($this->_scheduleResize) {
                    $this->_getModel()->resize();
                }

                if ($this->_scheduleAdaptiveResize) {
                    $this->_getModel()->adaptiveResize();
                }

                if ($this->getWatermark()) {
                    $this->_getModel()->setWatermark($this->getWatermark());
                }

                $url = $this->_getModel()->saveFile()->getUrl();
            }
        } catch (Exception $e) {
            $url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
        }
        return $url;
    }

    /**
     * Init Image processor model
     *
     * Rewrited to change model
     *
     * @param Mage_Catalog_Model_Product $product       product
     * @param string                     $attributeName attribute name
     * @param string                     $imageFile     image file name
     *
     * @return \Oggetto_AdaptiveResize_Helper_Catalog_Image
     */
    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)
    {
        $this->_reset();
        $this->_setModel(Mage::getModel('adaptiveResize/catalog_product_image'));
        $this->_getModel()->setDestinationSubdir($attributeName);
        $this->setProduct($product);
        $subdir = $this->_getModel()->getDestinationSubdir();
        $this->setWatermark(Mage::getStoreConfig("design/watermark/{$subdir}_image"));
        $this->setWatermarkImageOpacity(Mage::getStoreConfig("design/watermark/{$subdir}_imageOpacity"));
        $this->setWatermarkPosition(Mage::getStoreConfig("design/watermark/{$subdir}_position"));
        $this->setWatermarkSize(Mage::getStoreConfig("design/watermark/{$subdir}_size"));

        if ($imageFile) {
            $this->setImageFile($imageFile);
        } else {
            // add for work original size
            $this->_getModel()->setBaseFile($this->getProduct()->getData($subdir));
        }
        return $this;
    }

}