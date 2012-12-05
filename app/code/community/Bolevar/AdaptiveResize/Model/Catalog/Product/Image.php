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
 * Rewrites Product Image Module to Add Resize
 *
 * @category   Bolevar
 * @package    Bolevar_AdaptiveResize
 * @subpackage Model
 * @author     Denis Obukhov <roomine@bolevar.com>
 */
class Bolevar_AdaptiveResize_Model_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{
    /**
     * Adaptive Resize
     *
     * @return Bolevar_AdaptiveResize_Model_Catalog_Product_Image
     */
    public function adaptiveResize()
    {
        if (is_null($this->getWidth()) && is_null($this->getHeight())) {
            return $this;
        }

        $processor = $this->getImageProcessor();

        $currentRatio = $processor->getOriginalWidth() / $processor->getOriginalHeight();
        $targetRatio = $this->getWidth() / $this->getHeight();

        if ($targetRatio > $currentRatio) {
            $processor->resize($this->getWidth(), null);
        } else {
            $processor->resize(null, $this->getHeight());
        }

        $diffWidth  = $processor->getOriginalWidth() - $this->getWidth();
        $diffHeight = $processor->getOriginalHeight() - $this->getHeight();

        $processor->crop(
            floor($diffHeight / 2),
            floor($diffWidth / 2),
            ceil($diffWidth / 2),
            ceil($diffHeight / 2)
        );

        return $this;

    }

}
