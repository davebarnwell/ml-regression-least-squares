<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 07/04/2017
 * Time: 14:28
 */

namespace MachineLearning\Regression;


use Throwable;

class SeriesCountMismatch extends \Exception
{

    /**
     * LinearRegressionParamsException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @internal param string $string
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}