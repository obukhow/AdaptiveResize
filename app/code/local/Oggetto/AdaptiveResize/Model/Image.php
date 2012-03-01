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
 * Image processor model
 *
 * @category   Oggetto
 * @package    Oggetto_AdaptiveResize
 * @subpackage Model
 * @author     Denis Obukhov <denis.obukhov@oggettoweb.com>
 */
class Oggetto_AdaptiveResize_Model_Image extends Varien_Image
{

    /**
     * Adaptive resize an image
     *
     * @param int $width  image width
     * @param int $height image height
     *
     * @return string
     */
    public function adaptiveResize($width, $height = null)
    {
        return $this->_getAdapter()->adaptiveResize($width, $height);
    }

    /**
     * Retrieve image adapter object
     *
     * @param string $adapter adapter type
     * 
     * @return Varien_Image_Adapter_Abstract
     */
    protected function _getAdapter($adapter=null)
    {
        if (!isset($this->_adapter)) {
            if ($adapter == Varien_Image_Adapter::ADAPTER_GD2) {
                $this->_adapter = Mage::getModel('adaptiveResize/image_adapter_gd2');
            } else {
                 throw new Exception('Invalid adapter selected.');
            }
        }
        return $this->_adapter;
    }
}
