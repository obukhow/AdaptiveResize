<?php

/**
 * Oggetto Web extension for Magento
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
 * GD2 Image adapter
 *
 * @category   Oggetto
 * @package    Oggetto_AdaptiveResize
 * @subpackage Model
 * @author     Denis Obukhov <denis.obukhov@oggettoweb.com>
 */
class Oggetto_AdaptiveResize_Model_Image_Adapter_Gd2 extends Varien_Image_Adapter_Gd2
{

    /**
     * Change the image size down to a best match then crop from the center
     *
     * @param int $frameWidth  image width
     * @param int $frameHeight image height
     *
     * @return void
     */
    public function adaptiveResize($frameWidth = null, $frameHeight = null)
    {
        if (empty($frameWidth) && empty($frameHeight)) {
            throw new Exception('Invalid image dimensions.');
        }
        $widthDistance = $this->_imageSrcWidth - $frameWidth;
        $heightDistance = $this->_imageSrcHeight - $frameHeight;
        if (($frameWidth / $frameHeight) > ($this->_imageSrcWidth / $this->_imageSrcHeight)) {
            $this->resize($frameWidth, null);
        } else {
            $this->resize(null, $frameHeight);
        }
        $cropX = 0;
        $cropY = 0;
        if ($this->_imageSrcWidth > $frameWidth) {
            $cropX = intval(($this->_imageSrcWidth - $frameWidth) / 2);
        } elseif ($this->_imageSrcHeight > $frameHeight) {
            $cropY = intval(($this->_imageSrcHeight - $frameHeight) / 2);
        }
        $isAlpha = false;
        $isTrueColor = false;
        $this->_getTransparency($this->_imageHandler, $this->_fileType, $isAlpha, $isTrueColor);
        if ($isTrueColor) {
            $newImage = imagecreatetruecolor($frameWidth, $frameHeight);
        } else {
            $newImage = imagecreate($frameWidth, $frameHeight);
        }
        $this->_fillBackgroundColor($newImage);
        imagecopyresampled($newImage, $this->_imageHandler, 0, 0, $cropX, $cropY, $frameWidth, $frameHeight,
                           $frameWidth, $frameHeight);
        $this->_imageHandler = $newImage;
        $this->refreshImageDimensions();
    }

}
