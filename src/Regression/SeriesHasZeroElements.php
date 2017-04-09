<?php

namespace MachineLearning\Regression;


use Throwable;

class SeriesHasZeroElements extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}