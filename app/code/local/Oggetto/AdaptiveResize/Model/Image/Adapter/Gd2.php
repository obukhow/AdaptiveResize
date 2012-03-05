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

    /**
     * Get Transparency
     *
     * @param resource $imageResource image resourc
     * @param string   $fileType      fill type
     * @param bool     &$isAlpha      is alpha channel
     * @param bool     &$isTrueColor  is image true color
     *
     * @return boolean
     */
    private function _getTransparency($imageResource, $fileType, &$isAlpha = false, &$isTrueColor = false)
    {
        $isAlpha = false;
        $isTrueColor = false;
        // assume that transparency is supported by gif/png only
        if ((IMAGETYPE_GIF === $fileType) || (IMAGETYPE_PNG === $fileType)) {
            // check for specific transparent color
            $transparentIndex = imagecolortransparent($imageResource);
            if ($transparentIndex >= 0) {
                return $transparentIndex;
            } elseif (IMAGETYPE_PNG === $fileType) {
                // assume that truecolor PNG has transparency
                $isAlpha = $this->checkAlpha($this->_fileName);
                $isTrueColor = true;
                return $transparentIndex; // -1
            }
        }
        if (IMAGETYPE_JPEG === $fileType) {
            $isTrueColor = true;
        }
        return false;
    }

    /**
     * Fill background color
     *
     * @param resource &$imageResourceTo image resource
     *
     * @return string
     * @throws Exception
     */
    private function _fillBackgroundColor(&$imageResourceTo)
    {
        // try to keep transparency, if any
        if ($this->_keepTransparency) {
            $isAlpha = false;
            $transparentIndex = $this->_getTransparency($this->_imageHandler, $this->_fileType, $isAlpha);
            try {
                // fill truecolor png with alpha transparency
                if ($isAlpha) {

                    if (!imagealphablending($imageResourceTo, false)) {
                        throw new Exception('Failed to set alpha blending for PNG image.');
                    }
                    $transparentAlphaColor = imagecolorallocatealpha($imageResourceTo, 0, 0, 0, 127);
                    if (false === $transparentAlphaColor) {
                        throw new Exception('Failed to allocate alpha transparency for PNG image.');
                    }
                    if (!imagefill($imageResourceTo, 0, 0, $transparentAlphaColor)) {
                        throw new Exception('Failed to fill PNG image with alpha transparency.');
                    }
                    if (!imagesavealpha($imageResourceTo, true)) {
                        throw new Exception('Failed to save alpha transparency into PNG image.');
                    }

                    return $transparentAlphaColor;
                } elseif (false !== $transparentIndex) {
                    // fill image with indexed non-alpha transparency
                    list($r, $g, $b)  = array_values(imagecolorsforindex($this->_imageHandler, $transparentIndex));
                    $transparentColor = imagecolorallocate($imageResourceTo, $r, $g, $b);
                    if (false === $transparentColor) {
                        throw new Exception('Failed to allocate transparent color for image.');
                    }
                    if (!imagefill($imageResourceTo, 0, 0, $transparentColor)) {
                        throw new Exception('Failed to fill image with transparency.');
                    }
                    imagecolortransparent($imageResourceTo, $transparentColor);
                    return $transparentColor;
                }
            }
            catch (Exception $e) {
                // fallback to default background color
            }
        }
        list($r, $g, $b) = $this->_backgroundColor;
        $color = imagecolorallocate($imageResourceTo, $r, $g, $b);
        if (!imagefill($imageResourceTo, 0, 0, $color)) {
            throw new Exception("Failed to fill image background with color {$r} {$g} {$b}.");
        }

        return $color;
    }

    /**
     * Refresh image dimensions
     *
     * @return void
     */
    private function refreshImageDimensions()
    {
        $this->_imageSrcWidth = imagesx($this->_imageHandler);
        $this->_imageSrcHeight = imagesy($this->_imageHandler);
    }

    /**
     * Fixes saving PNG alpha channel
     *
     * @param resource $imageHandler image handler
     *
     * @return void
     */
    private function _saveAlpha($imageHandler)
    {
        $background = imagecolorallocate($imageHandler, 0, 0, 0);
        ImageColorTransparent($imageHandler, $background);
        imagealphablending($imageHandler, false);
        imagesavealpha($imageHandler, true);
    }
}
