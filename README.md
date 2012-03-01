mage resize with crop functionality for ideal proportions fit with every design. Instead of resizing of adding white frame this module will crop main part of image.

*Attention! For now it will work only with GD2 image library.*

To use adaptive resize just use standart product image helper as always, just change resize() method to adaptiveResize().
Example
```php
$this->helper('catalog/image')->init($this->getProduct(), 'image')->adaptiveResize(400, 215)
$this->helper('catalog/image')->init($this->getProduct(), 'image', $_image->getFile())->constrainOnly(TRUE)->keepAspectRatio(FALSE)->keepFrame(FALSE)->adaptiveResize(400, 300)
//height is the same is width
$this->helper('catalog/image')->init($this->getProduct())->constrainOnly(TRUE)->adaptiveResize(400)
```
