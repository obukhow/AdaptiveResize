# Magento Resize With Crop Functionality for Ideal Proportions Fit With Every Design.

Instead of resizing and adding white frame this module will crop main part of image.

* Doesn't rewrite any part of core Magento code
* Compatable with Magento CE >= 1.3 and Magento EE > 1.10
* Easily configurable for your needs

## Resize examples comparing to Magento default resize

### Portrait image with landscape oriented product images design

![Portrait image crop](http://i.imgur.com/rky8S.png)

### Landscape oriented image with portrait product images design

![Landscape image crop](http://i.imgur.com/Q5PMW.png)

## Crop position settings

You can specify crop position for your need by calling `setCropPosition()` method. Allowed values are: 'top', 'bottom' and 'center'. If no value is specified 'center' will be used by default.

![Crop position](http://i.imgur.com/CNv8k.png)

## How to Use

To use adaptive resize just use adaptiveResize helper instead of product image helper, and change `resize()` method to `adaptiveResize()`.
Example

```php
$this->helper('adaptiveResize/image')->init($this->getProduct(), 'image')->adaptiveResize(400, 215);
//height is the same is width
$this->helper('adaptiveResize/image')->init($this->getProduct())->adaptiveResize(400);
// you can specify crop position as 'top', 'bottom' and 'center'. 'center' is used by default
$this->helper('adaptiveResize/image')->init($this->getProduct())
    ->setCropPosition('top')
    ->adaptiveResize(400);
```

## Special Thanks

Resize logic created by [Leon Smith](http://github.com/leonsmith)
http://2ammedia.co.uk/web-design/magento-adaptive-resize-resize-to-best-fit
