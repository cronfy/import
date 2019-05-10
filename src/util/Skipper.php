<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 29.04.16
 * Time: 16:38
 */

namespace cronfy\import\util;

class Skipper
{

    const STATE_CONTINUE = 'continue';
    const STATE_FINISHED = 'finished';

    public $step, $qty, $any;

    public function __construct($step, $qty)
    {
        $this->any = false;
        $this->step = $step ? $step : 1;
        $this->skip = ($step - 1) * $qty;
        $this->qty = $qty;
    }

    public function skip()
    {
        if ($this->skip) {
            $this->skip--;
            return true;
        }
    }

    public function go()
    {
        if ($this->qty !== null) {
            if ($this->qty) {
                $this->qty--;
                $this->any = true;
                return true;
            } else {
                return false;
            }
        } else {
            $this->any = true;
            return true;
        }
    }

    public function state()
    {
        if ($this->any) {
            return static::STATE_CONTINUE;
        } else {
            return static::STATE_FINISHED;
        }
    }
}
