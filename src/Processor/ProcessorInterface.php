<?php

namespace ImageProcessor\Processor;

/**
 * Class ProcessorInterface
 *
 * @package Tenolo\ImageBundle\Workshop\Processor
 * @author Nikita Loges
 * @company tenolo GbR
 * @date 22.05.14
 */
interface ProcessorInterface {

    /**
     * Constructor
     *
     * @param  string $source
     */
    public function __construct($source);

    /**
     * Resize image
     *
     * @param  int $width
     * @param  int $height
     * @param  int $flip
     * @param  int $gravity
     */
    public function resize($width, $height, $flip, $gravity);

    /**
     * Rotate image
     *
     * @param  int $deg
     *
     * @return void
     */
    public function rotate($deg);

    /**
     * Flip image horizontally
     */
    public function flip();

    /**
     * Flip image vertically
     */
    public function flop();

    /**
     * Get image dimensions
     *
     * @return string
     */
    public function getExtension();

    /**
     * Get image dimensions
     *
     * @return int
     */
    public function getWidth();

    /**
     * Get image dimensions
     *
     * @return int
     */
    public function getHeight();

    /**
     * Get image dimensions
     *
     * @return array
     */
    public function getImageSize();

    /**
     * Extract a region of the image
     *
     * @param  int $width
     * @param  int $height
     * @param  int $x
     * @param  int $y
     * @param  int $gravity
     */
    public function crop($width, $height, $x, $y, $gravity);

    /**
     * Save image to file
     *
     * @param  string|null $dest
     * @param  int $quality
     */
    public function save($dest = null, $quality = 90);
} 