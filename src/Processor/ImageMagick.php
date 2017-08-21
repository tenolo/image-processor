<?php

namespace ImageProcessor\Processor;

use Tenolo\CoreBundle\Util\CryptUtil;

/**
 * Class ImageMagick
 *
 * @package Tenolo\ImageBundle\Workshop\Processor
 * @author Nikita Loges
 * @company tenolo GbR
 * @date 22.05.14
 */
class ImageMagick extends AbstractProcessor {

    /**
     *
     */
    protected $source;

    /**
     *
     */
    protected $image;

    protected $tempfile;

    protected $info;

    /**
     * Constructor
     *
     * @param  string $source
     */
    public function __construct($source) {
        $this->source = $source;
        $this->image = $source;

        $this->renewInfo();

        $this->tempfile = __DIR__ . '/' . CryptUtil::getRandomHash() . '.' . $this->getExtension();
    }

    /**
     *
     */
    public function __destruct() {
        if(is_file($this->tempfile)) {
            @unlink($this->tempfile);
        }
    }

    /**
     * @{@inheritdoc}
     */
    public function resize($_width, $_height, $_flip = 0, $_gravity = 'TL') {
        #list($width, $height, $flip, $gravity) = parent::resize($_width, $_height, $_flip, $_gravity);

		switch($_gravity) {
			case 'TL':
				$gravity = 'NorthWest';
				break;
			case 'TM':
				$gravity = 'North';
				break;
			case 'TR':
				$gravity = 'NorthEast';
				break;
			case 'ML':
				$gravity = 'West';
				break;
			case 'MM':
				$gravity = 'Center';
				break;
			case 'MR':
				$gravity = 'East';
				break;
			case 'BL':
				$gravity = 'SouthWest';
				break;
			case 'BM':
				$gravity = 'South';
				break;
			case 'BR':
				$gravity = 'SouthEast';
				break;
			default:
				$gravity = 'NorthWest';
				break;
		}

        exec('convert -thumbnail ' . $_width . 'x' . $_height . '^ -gravity ' . $gravity . ' -extent ' . $_width . 'x' . $_height . ' "' . $this->image . '" "' . $this->tempfile . '"');
        $this->switchFileFields();
    }

    /**
     * @{@inheritdoc}
     */
    public function crop($_width, $_height, $_x, $_y, $_gravity = 'TL') {
        list($width, $height, $x, $y, $gravity) = parent::crop($_width, $_height, $_x, $_y, $_gravity);

        if(($width != $this->getWidth() || $x == 0) || ($height != $this->getHeight() || $y == 0)) {
            exec('convert "' . $this->image . '" -crop ' . $width . 'x' . $height . '+' . $x . '+' . $y . ' "' . $this->tempfile . '"');
            $this->switchFileFields();
        }
    }

    /**
     * @{@inheritdoc}
     */
    public function rotate($deg) {
        exec('convert "' . $this->image . '" -rotate ' . $deg . ' "' . $this->tempfile . '"');
        $this->switchFileFields();
    }

    /**
     * @{@inheritdoc}
     */
    public function flip() {
        exec('convert "' . $this->image . '" -flop "' . $this->tempfile . '"');
        $this->switchFileFields();
    }

    /**
     * @{@inheritdoc}
     */
    public function flop() {
        exec('convert "' . $this->image . '" -flip "' . $this->tempfile . '"');
        $this->switchFileFields();
    }

    /**
     * @{@inheritdoc}
     */
    public function getExtension() {
        return $this->info['extension'];
    }

    /**
     * @{@inheritdoc}
     */
    public function getWidth() {
        return $this->info['width'];
    }

    /**
     * @{@inheritdoc}
     */
    public function getHeight() {
        return $this->info['height'];
    }

    /**
     * @return array
     */
    public function getInfo() {
        return $this->info;
    }

    /**
     * @{@inheritdoc}
     */
    public function getImageSize() {
        $size = explode(',', exec('identify -format "%w,%h" "' . $this->image . '"'));

        return array(
            "width" => $size[0],
            "height" => $size[1]
        );
    }

    /**
     * @{@inheritdoc}
     */
    public function save($dest = null, $quality = 90) {
        if(empty($dest))
            $dest = $this->source;

        if(!is_int($quality) || $quality < 0 || $quality > 100)
            $quality = 90;

        exec('convert "' . $this->image . '" -quality ' . $quality . ' -strip "' . $dest . '"');
    }

    /**
     *
     */
    protected function switchFileFields() {
        $this->image = $this->tempfile;

        $this->renewInfo();
    }

    /**
     *
     */
    protected function renewInfo() {
        $info = exec('identify -format "%w,%h,%b,%e,%t" "' . $this->image . '"');
        $info = explode(',', $info);

        $this->info = array(
            'width' => $info[0],
            'height' => $info[1],
            'size' => $info[2],
            'extension' => $info[3],
            'filename' => $info[4],
        );

    }
} 