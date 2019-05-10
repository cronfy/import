<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.02.18
 * Time: 20:07
 */

namespace cronfy\import\exceptions;

class PopulateException extends \Exception
{
    public $originalException;
}
