<?php

namespace ImageProcessor;

use ImageProcessor\Processor\ProcessorInterface;

/**
 * Class ImageWorkshop
 * @package Tenolo\ImageBundle\Workshop
 * @author Nikita Loges
 * @company tenolo GbR
 * @date 22.05.14
 */
class ImageProcessor {

    /**
     * @var string
     */
    protected $source;

    /**
     * @var ProcessorInterface
     */
    protected $processor;

    /**
     * @param $source
     * @param string $processor
     *
     * @throws \Exception
     */
    public function __construct($source, $processor = 'ImageMagick') {
        $this->source = $source;

        $processorClassName = 'ImageProcessor\Processor\\'.$processor;

        if(!class_exists($processorClassName) && !class_exists($processor))
            throw new \Exception('class not found');

        $ref = new \ReflectionClass($processorClassName);
        if(!$ref->implementsInterface('\ImageProcessor\Processor\ProcessorInterface'))
            throw new \Exception('processor do not implement ProcessorInterface');

        $this->processor = new $processorClassName($this->source);
    }

    /**
     * @param $name
     * @param $aguments
     *
     * @return mixed
     */
    public function __call($name, $aguments) {
        return call_user_func_array(array($this->processor, $name), $aguments);
    }
} 