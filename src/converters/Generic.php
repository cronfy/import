<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.02.18
 * Time: 20:19
 */

namespace cronfy\import\converters;

use cronfy\import\exceptions\PopulateException;

abstract class Generic
{
    public $autoTrim = true;

    abstract protected function populators();

    protected $_populators;
    protected function getPopulators()
    {
        if (!$this->_populators) {
            $this->_populators = $this->populators();
        }

        return $this->_populators;
    }

    public function convert($source, $target = null)
    {
        foreach ($this->getPopulators() as $name => $populator) {
            try {
                switch (true) {
                    case ($populator instanceof \Closure):
                        $value = $populator($source);
                        break;
                    case is_string($populator):
                        $value = $source[$populator];
                        break;
                    default:
                        $value = $populator;
                        break;
                }
            } catch (\Exception $e) {
                throw $e;
                $exception = new PopulateException($e->getMessage());
                $exception->originalException = $e;
                throw $exception;
            }

            if ($this->autoTrim && is_string($value)) {
                $value = trim($value);
            }

            $target[$name] = $value;
        }

        return $target;
    }
}
