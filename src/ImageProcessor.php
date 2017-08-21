<?php

namespace Tenolo\ImageProcessor;

use Tenolo\ImageProcessor\Processor\ProcessorInterface;

/**
 * Class ImageProcessor
 *
 * @package Tenolo\ImageProcessor
 * @author  Nikita Loges
 */
class ImageProcessor
{

    /**
     * @var ProcessorInterface
     */
    protected $processor;

    /**
     * @param        $source
     * @param string $processor
     *
     * @throws \Exception
     */
    public function __construct($source, $processor = 'ImageMagick')
    {
        $processorClassName = 'Tenolo\ImageProcessor\Processor\\' . $processor;

        if (!class_exists($processorClassName) && !class_exists($processor)) {
            throw new \Exception('class not found');
        }

        if (class_exists($processor)) {
            $processorClassName = $processor;
        }

        $ref = new \ReflectionClass($processorClassName);
        if (!$ref->implementsInterface(ProcessorInterface::class)) {
            throw new \Exception('processor do not implement ProcessorInterface');
        }

        $this->processor = new $processorClassName($source);
    }

    /**
     * @param $name
     * @param $aguments
     *
     * @return mixed
     */
    public function __call($name, $aguments)
    {
        return call_user_func_array([$this->processor, $name], $aguments);
    }
} 