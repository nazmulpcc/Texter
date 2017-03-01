# Texter
Texter helps to write Bangla/English text on image with PHP GD. It also has featurs like align, maintain lineheight, color settings etc.
## Example
```php
	$texter = new nazmulpcc\Texter;
	$image = imagecreate(500, 300);
	imagecolorallocate($image, 255, 255, 255);
	$texter->startFrom(50, 90)->width(400)->on($image)->align('center')->fontSize(30)->color('333333');
	$texter->text('আমার সোনার বাংলা, আমি তোমায় ভালবাসি Lorem ipsum dolor sit amet.....')->write();
```
 **It will give you something like this:**
![Result](http://i.imgur.com/phfgEqy.jpg "Result")

## Installation
You can easily install **Texter** via composer or you can manually download the package and include them in your code.
### Composer
```shell
	composer require nazmulpcc/Texter
```

### Manual
- Download/Clone this repo.
- Download/Clone [mirazmac/Unicode2Bijoy](https://github.com/mirazmac/Unicode2Bijoy) .
- Include **Texter.php** and **Unicode2Bijoy.php** in your code and you are ready to go.

## Documentation
Each method is fairly well documented in the source code. A few important methods:  
- **startFrom(x, y):** Set up the co-ordinates from which Texter will start writing.  
- **width($w):** Width of the boundary inside which Texter will write text.  
- **align($position):** Set the horizontal alignment to left, right or center/centre. Default is left.  
- **on($image)** / **image($image):** Set the image on which Texter will write. Image is passed by reference.  
- **fontSize($size):** Set the font size. $size can be point or pixel, like ```$texter->fontSize('15pt')``` or ```$texter->fontSize('15px')```. If 'pt' or 'px' is absent, the gd default is used.  
- **color($hex):** / **color($red, $green, $blue):** Both hex and RGB are accepted as text color.
- **text($text):** Add text to be written.  
- **lineHeight($height):** The line height. $height can be in pixel or as a percentage of the text height.  
- **write():** Call this guy to do the job or if you want to start a new line.  
All public functions can be chained up. So you can always do things like:  
```php
    $texter->startFrom(10, 10)
                ->color(0, 0, 0)
                ->align('center')
                ->width(500)
                ->text('Hellow World')
                ->write();
```  
**Note:** Before you write a piece of text, you must at least set the starting points(**startFrom**), **width**, and **image**.  

## About Texter
I developed this out of pure frustration that PHP, one of the most widely used coding language doesn't support Bangla because GD can't handle complex fonts. There are room for a lot of improvements which I will try to accomplish gradually. Please contribute if you can.

## To-Do
- Add vertical alignment feature.
- Support for Text shadow.
- Add some debugging functions
etc.  

## License
This project is lincensed under [DBAD](http://www.dbad-license.org) license.