<?php
namespace nazmulpcc;
use mirazmac\Unicode2Bijoy;
/**
 * Texter
 */
class Texter
{
    /**
     * Location of Bangla ANSI font
     * @var string
     */
    public $bangla = __DIR__ . "/fonts/kalpurushANSI.ttf";

    /**
     * Location of English font.
     * @var [type]
     */
    
    public $english = __DIR__ . "/fonts/arial.ttf";
    /**
     * Set the lineheight. Recommended way is to use the lineHeight method
     * @var string
     */
    
    public $lineHeight = 10;
    /**
     * Default fontsize
     * @var integer
     */
    
    public $fontSize = 14;
    /**
     * Temporary text bin.
     * @var string
     */
    public $align = 'left';
    private $_color = ['red'=>0, 'green'=>0, 'blue'=>0];
    private $_text = '';
    private $_lineWidth = 0;

    /**
     * Add text to be written
     * @param  string $text The text to be added
     * @return object       this object
     */
    public function text($text)
    {
        $this->_text = $this->_text . $text;
        return $this;
    }

    /**
     * Set the fonsize in pixel(px), point(pt) or gd default(empty)
     * @param  string|integer $size size in pixel, point or gd default. Ex: 15px, 15pt or 15. Deafault 15pt
     * @return [type]       [description]
     */
    public function fontSize($size = '15pt')
    {
        $this->fontSize = $size;
        $gd2 = $this->isGd2();
        if(strpos($size, 'px') !== false AND $gd2 == true){
            $this->_fontSize = $this->pixelToPoint($size);
        } elseif (strpos($size, 'pt') !== false AND $gd2 == false) {
            $this->_fontSize = $this->pointToPixel($size);
        }else {
            $this->_fontSize = (float) $size;
        }
        return $this;
    }

    /**
     * Set the text align
     * @param  string $align Set the align to left, right or center/centre. Default left.
     * @return [type]        [description]
     */
    public function align($align = 'left')
    {
        $this->align = $align;
        return $this;
    }

    /**
     * Set the co-ordinates from where Texter will start writing.
     * @param  integer $x Starting x co-ordinate
     * @param  integer $y Starting y co-ordinate
     * @return [type]    [description]
     */
    public function startFrom($x, $y)
    {
        $this->_x = (int) $x;
        $this->_y = (int) $y;
        $this->_origin = ['x' => $this->_x, 'y' => $this->_y];
        return $this;
    }

    /**
     * Set the image on which Texter will write. Must be a gd resource.
     * @param  resource &$image Gd resource, passed by refference.
     * @return [type]         [description]
     */
    public function on(&$image)
    {
        $this->_image = &$image;
        return $this;
    }

    /**
     * Set the image on which Texter will write. Must be a gd resource.
     * Alias for the method on.
     * @param  resource &$image Gd resource, passed by refference.
     * @return [type]         [description]
     */
    public function image(&$image)
    {
        return $this->on($image);
    }

    /**
     * Set the width of the boundary within which Texter will write.
     * @param  integer $w The width of the boundary.
     * @return [type]    [description]
     */
    public function width($w )
    {
        $this->_boxWidth = (int) $w;
        return $this;
    }

    /**
     * Set the color of the text. Accepts both Hex and RGB.
     * @param  integer|string  $red   If RGB, then the value of red. If Hex, then the Hex color code.
     * @param  integer $green RGB value for the color Green.
     * @param  integer $blue  RGB value for the color Blue.
     * @return [type]         [description]
     */
    public function color($red, $green = false, $blue = false)
    {
        if (strlen($red) == 6 AND $green === false AND $blue === false) {
            $hexcode = str_replace('#', '', $red); //hexcode is given, so convert it to RGB
            list($red, $green, $blue) = sscanf($hexcode, '%2x%2x%2x');
        }
        $this->_color = compact('red', 'green', 'blue');
        return $this;
    }

    /**
     * Set the lineheight either in pixel or as a percentage of the Text height.
     * @param  integer|string $h The lineheight in pixel or percentage of the text height.
     * @return [type]    [description]
     */
    public function lineHeight($h)
    {
        $this->lineHeight = $h;
        if(strpos($h, "%") !== false){
            $h = (float) $h;
            $gd2 = $this->isGd2();
            if ($gd2 == true) {
                $this->fontSize($this->fontSize);
                $fontPixel = $this->pointToPixel($this->_fontSize);
            }else {
                $fontPixel = $this->_fontSize;
            }
            $this->_lineHeight = round(($h / 100) * $fontPixel);
        }else {
            $this->_lineHeight = (int) $h;
        }
        return $this;
    }

    /**
     * Write the text on the image.
     * @return [type] [description]
     */
    public function write()
    {
        $this->setGlobalSettings(); //color, align, lineHeight
        $words = explode(" ", $this->_text." ");
        for ($i = 0; $i < count($words); $i++) {
            if ($i+1 == count($words)) {
                $end = true;
                //$this->dump($words);
            }else {
                $end = false;
            }
            $this->createLinesAndWrite($words[$i], $end);
        }
        $this->_text = '';
        $this->_line = [];
        return $this;
    }

    ##################################################################################################
    #################### IF YOU TOUCH ANYTHING BELOW< YOUR COMPUTER MIGHT EXPLODE ####################
    ##################################################################################################

    /**
     * Set some variables which is needed by other methods to work correctly.
     */
    private function setGlobalSettings()
    {
        $this->color = imagecolorallocate($this->_image, $this->_color['red'], $this->_color['green'], $this->_color['blue']);
        if (!isset($this->_fontSize)) {
            $this->fontSize($this->fontSize);
        }
        if (!isset($this->_fontSize)) {
            $this->fontSize($this->fontSize);
        }
        if (!isset($this->_lineHeight)) {
            $this->lineHeight($this->lineHeight);
        }

    }

    /**
     * Set some text specific values
     * @param string $text The text for which settings will be updated.
     */
    private function setTextSettings($text = false)
    {
        if (!is_string($text)) {
            $text = $this->_text;
        }
        if (mb_detect_encoding($text) !== 'ASCII') {
            $this->_font = $this->bangla;
            $text = Unicode2Bijoy::convert($text);
        }else {
            $this->_font = $this->english;
        }
        $w = imagettfbbox($this->_fontSize, 0, $this->_font, $text);
        $this->_textWidth = $w[2] - $w[0];
        $this->_textHeight = abs($w[7] - $w[1]);
        return $text;
    }


    /**
     * Set the x co-ordinate according to align settings just before writing a line.
     */
    private function setX()
    {
        if ($this->align == 'right') {
            $this->_x = ($this->_origin['x'] + $this->_boxWidth) - $this->_lineWidth;
        }elseif ($this->align == 'center' OR $this->align == 'centre') {
            $this->_x = ( ($this->_boxWidth/2) - ($this->_lineWidth / 2) ) + $this->_origin['x'];
        }else {
            $this->_x = $this->_origin['x'];
        }
    }

    /**
     * Set y co-ordinate after a line is written.
     * This is the laziest function Texter has.
     */
    private function setY()
    {
        $this->_y = $this->_y + $this->_textHeight + $this->_lineHeight;
    }

    /**
     * Convert point to pixel values.
     * @param  float $pt The point value to be converted.
     * @return [type]     [description]
     */
    private function pointToPixel($pt)
    {
        $pt = (float) $pt;
        $px = (4/3) * $pt;
        return round($px);
    }

    /**
     * Convert pixel to point values.
     * @param  integer $px The pixel values to be converted.
     * @return [type]     [description]
     */
    private function pixelToPoint($px)
    {
        $px = (int) $px;
        $pt = $px * .75;
        return $pt;
    }

    /**
     * Check if the gd version is 2.
     * @return boolean [description]
     */
    private function isGd2()
    {
        $gd = gd_info();
        list($v) = explode('.', $gd['GD Version']);
        $v = preg_replace( '/[^0-9]/', '', $v );
        if ($v = 2) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * The true hero behind Texter. Create lines according to the boundary width and then issue a write command.
     * @param  string  $word The word to be added in a line till there's enough space for it.
     * @param  boolean $end  Indicate whether it's the last word.
     * @return [type]        [description]
     */
    private function createLinesAndWrite($word, $end = false)
    {
        $word = $word. ' ';
        $this->setTextSettings($word);
        $text_width = $this->_textWidth;
        if ($this->_lineWidth + $text_width > $this->_boxWidth OR $end) {
            $this->writeLine($this->_line);
            $this->_line = [$word];
            $this->_lineWidth = $text_width;
        }else {
            $this->_line[] = $word;
            $this->_lineWidth = $this->_lineWidth + $this->_textWidth;
        }
    }

    /**
     * Update text settings for each word of a line and write word by word.
     * @param  [type] $line [description]
     * @return [type]       [description]
     */
    private function writeLine($line)
    {
        $this->setX();
        foreach ($line as $word) {
            $word = $this->setTextSettings($word);
            imagettftext($this->_image, $this->_fontSize, 0, $this->_x, $this->_y, $this->color, $this->_font, $word);
            $this->_x = $this->_x + $this->_textWidth;
        }
        $this->setY();
    }

}
