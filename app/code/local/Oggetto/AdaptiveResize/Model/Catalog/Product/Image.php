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
 * Rewrites Product Image Module to Add Resize
 *
 * @category   Oggetto
 * @package    Oggetto_AdaptiveResize
 * @subpackage Model
 * @author     Denis Obukhov <denis.obukhov@oggettoweb.com>
 */
class Oggetto_AdaptiveResize_Model_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{
    /**
     * Adaptive Resize
     *
     * @return Oggetto_AdaptiveResize_Model_Catalog_Product_Image
     */
    public function adaptiveResize()
    {
        if (is_null($this->getWidth()) && is_null($this->getHeight())) {
            return $this;
        }
        $this->getImageProcessor()->adaptiveResize($this->_width, $this->_height);
        return $this;
    }

    /**
     * Get Image Processor
     *
     * @return Varien_Image
     */
    public function getImageProcessor()
    {
        if( !$this->_processor ) {
            $this->_processor = Mage::getModel('adaptiveResize/image', $this->getBaseFile());
        }
        $this->_processor->keepAspectRatio($this->_keepAspectRatio);
        $this->_processor->keepFrame($this->_keepFrame);
        $this->_processor->keepTransparency($this->_keepTransparency);
        $this->_processor->constrainOnly($this->_constrainOnly);
        $this->_processor->backgroundColor($this->_backgroundColor);
        $this->_processor->quality($this->_quality);
        return $this->_processor;
    }

}
