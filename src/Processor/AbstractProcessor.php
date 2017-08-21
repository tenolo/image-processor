<?php

namespace ImageProcessor\Processor;

/**
 * Class AbstractProcessor
 * @package ImageProcessor\Processor
 * @author Nikita Loges
 * @company tenolo GbR
 * @date 23.05.14
 */
abstract class AbstractProcessor implements ProcessorInterface {

    /**
     * @{@inheritdoc}
     */
    public function resize($width, $height, $flip = 0, $gravity = 'TL') {

        // if one size is missing, calcualte it
        if (empty($height)) {
            $height = $this->calculateHeightProportional($width);
        } else if (empty($width)) {
            $width = $this->calculateWidthProportional($height);
        }

        // ratio of original and thumb image
        $ratioCurrent = $this->getHeight() / $this->getWidth();
        $ratioNew     = $height / $width;

        // ratio inverse of original and thumb image
        $ratioInverseCurrent = 1 / $ratioCurrent;
        $ratioInverseNew     = 1 / $ratioNew;

        // image has to crop
        if ($ratioCurrent != $ratioNew) {
            if ($width > $height) {
                $cropHeight = $this->getWidth() * $ratioNew;
                $cropWidth  = $this->getWidth();

                if ($cropHeight > $this->getHeight()) {
                    $correction = 1 / ($cropHeight / $this->getHeight());
                    $cropWidth *= $correction;
                    $cropHeight *= $correction;
                }
            } else {
                $cropWidth  = $this->getHeight() * $ratioInverseNew;
                $cropHeight = $this->getHeight();

                if ($cropWidth > $this->getWidth()) {
                    $correction = 1 / ($cropWidth / $this->getWidth());
                    $cropWidth *= $correction;
                    $cropHeight *= $correction;
                }
            }

            $this->crop($cropWidth, $cropHeight, 0, 0, $gravity);
        }

        return array(
            $width,
            $height,
            $flip,
            $gravity
        );
    }

    /**
     * @{@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function crop($width, $height, $x, $y, $gravity = 'TL') {
        if ($width < 1)
            throw new \InvalidArgumentException('You can\'t use negative $width for "'.__METHOD__.'" method.');

        if ($height < 1)
            throw new \InvalidArgumentException('You can\'t use negative $height for "'.__METHOD__.'" method.');

        if ($x < 0)
            throw new \InvalidArgumentException('You can\'t use negative $x for "'.__METHOD__.'" method.');

        if ($y < 0)
            throw new \InvalidArgumentException('You can\'t use negative $y for "'.__METHOD__.'" method.');
        
        if(empty($gravity))
            $gravity = 'TL';

        switch($gravity) {
            case 'TL':
            case 'TM':
            case 'TR':
            case 'ML':
            case 'MM':
            case 'MR':
            case 'BL':
            case 'BM':
            case 'BR':
                // nothing to do
                break;
            default:
                throw new \InvalidArgumentException('Invalid gravity for "'.__METHOD__.'" method.');
                break;
        }



        $x = ($x < 0) ? $x * -1 : $x;
        $y = ($y < 0) ? $y * -1 : $y;
        $currentWidth = $this->getWidth();
        $currentHeight = $this->getHeight();

        if (($width != $currentWidth || $x == 0) || ($height != $currentHeight || $y == 0)) {
            switch($gravity) {
                case 'TL':
                    // nothing to do
                    break;
                case 'TM':
                    $x += ($currentWidth - $width) / 2;
                    break;
                case 'TR':
                    $x = ($currentWidth - $width) - $x;
                    break;
                case 'ML':
                    $y += ($currentHeight - $height) / 2;
                    break;
                case 'MM':
                    $x += ($currentWidth - $width) / 2;
                    $y += ($currentHeight - $height) / 2;
                    break;
                case 'MR':
                    $x = ($currentWidth - $width) - $x;
                    $y += ($currentHeight - $height) / 2;
                    break;
                case 'BL':
                    $y = ($currentHeight - $height) - $y;
                    break;
                case 'BM':
                    $x += ($currentWidth - $width) / 2;
                    $y = ($currentHeight - $height) - $y;
                    break;
                case 'BR':
                    $x = ($currentWidth - $width) - $x;
                    $y = ($currentHeight - $height) - $y;
                    break;
            }
        }

        return array(
            $width,
            $height,
            $x,
            $y,
            $gravity
        );
    }

    /**
     * @param $width
     *
     * @return float
     */
    public function calculateHeightProportional($width) {
        return (int)floor($this->getHeight() * ($width / $this->getWidth()));
    }

    /**
     * @param $height
     *
     * @return float
     */
    public function calculateWidthProportional($height) {
        return (int)floor($this->getWidth() * ($height / $this->getHeight()));
    }
} 