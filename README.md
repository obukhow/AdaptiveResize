# Magento Resize With Crop Functionality for Ideal Proportions Fit With Every Design.

Instead of resizing and adding white frame this module will crop main part of image.

## Resize examples comparing to Magento default resize

### Portrait image with landscape oriented product images design

![Portrait image crop](http://i.imgur.com/rky8S.png)

### Landscape oriented image with portrait product images design

![Landscape image crop](http://i.imgur.com/Q5PMW.png)

## How to Use

To use adaptive resize just use adaptiveResize helper instead of product image helper, and change `resize()` method to `adaptiveResize()`.
Example

```php
$this->helper('adaptiveResize/image')->init($this->getProduct(), 'image')->adaptiveResize(400, 215);
//height is the same is width
$this->helper('adaptiveResize/image')->init($this->getProduct())->adaptiveResize(400);
```

## Special Thanks

Resize logic created by [Leon Smith](http://github.com/leonsmith)
http://2ammedia.co.uk/web-design/magento-adaptive-resize-resize-to-best-fit
