<?php

/**
 * Adaptive image resize extension for Magento
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
 * the Bolevar AdaptiveResize module to newer versions in the future.
 * If you wish to customize the Bolevar AdaptiveResize module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Bolevar
 * @package    Bolevar_AdaptiveResize
 * @copyright  Copyright (C) 2012 Roomine Bolevar ltd (http://bolevar.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * AdaptiveResize Helper
 *
 * @category   Bolevar
 * @package    Bolevar_AdaptiveResize
 * @subpackage Helper
 * @author     Denis Obukhov <roomine@bolevar.com>
 */
class Bolevar_AdaptiveResize_Helper_Image extends Mage_Catalog_Helper_Image
{
    /**
     * Crop position
     *
     * @var string
     */
    protected $_cropPosition;

    /**
     * Adaptive resize flag
     *
     * @var bool
     */
    protected $_scheduleAdaptiveResize = false;

    /**
     * Reset all previous data
     *
     * @return Bolevar_AdaptiveResize_Helper_Image
     */
    protected function _reset()
    {
        $this->_scheduleAdaptiveResize = false;
        $this->_cropPosition = 0;
        parent::_reset();
    }

    /**
     * Adaptive resize method
     *
     * @param int      $width  image width
     * @param int|null $height image height
     *
     * @return \Bolevar_AdaptiveResize_Helper_Image
     */
    public function adaptiveResize($width, $height = null)
    {
        $this->_getModel()
            ->setWidth($width)
            ->setHeight((!is_null($height)) ? $height : $width)
            ->setKeepAspectRatio(true)
            ->setKeepFrame(false)
            ->setConstrainOnly(false);
            ;
        $this->_scheduleAdaptiveResize = true;
        return $this;
    }

    /**
     * Set crop position
     *
     * @param string $position top, bottom or center
     * 
     * @return \Bolevar_AdaptiveResize_Helper_Image
     */
    public function setCropPosition($position)
    {
        $this->_cropPosition = $position;
        return $this;
    }

    /**
     * Return generated image URL
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

                if ($this->_cropPosition) {
                    $this->_getModel()->setCropPosition($this->_cropPosition);
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
            Mage::logException($e);
            $url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
        }
        return $url;
    }

    /**
     * Init Image processor model
     *
     * Rewritten to change model
     *
     * @param Mage_Catalog_Model_Product $product       product
     * @param string                     $attributeName attribute name
     * @param string                     $imageFile     image file name
     *
     * @return \Bolevar_AdaptiveResize_Helper_Image
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
