===================================================================================
Magento Resize With Crop Functionality for Ideal Proportions Fit With Every Design.
===================================================================================

Instead of resizing and adding white frame this module will crop main part of image.

**Attention! For now it will work only with GD2 image library.**

Resize examples comparing to Magento default resize
---------------------------------------------------

**Portrait image with landscape oriented product images design**

.. image:: http://i.imgur.com/rky8S.png

**Landscape oriented image with portrait product images design**

.. image:: http://i.imgur.com/Q5PMW.png

How to Use
__________

To use adaptive resize just use standart product image helper as always, just change resize() method to adaptiveResize().
Example

::
	$this->helper('catalog/image')->init($this->getProduct(), 'image')->adaptiveResize(400, 215)
	$this->helper('catalog/image')->init($this->getProduct(), 'image', $_image->getFile())->constrainOnly(TRUE)->keepAspectRatio(FALSE)->keepFrame(FALSE)->adaptiveResize(400, 300)
	//height is the same is width
	$this->helper('catalog/image')->init($this->getProduct())->constrainOnly(TRUE)->adaptiveResize(400)

