<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 08/04/2017
 * Time: 19:23
 */

namespace MachineLearning\Regression;


use Throwable;

class ParameterNotComputedYet extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}