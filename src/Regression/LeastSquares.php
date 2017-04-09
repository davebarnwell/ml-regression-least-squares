<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 07/04/2017
 * Time: 14:00
 */

namespace MachineLearning\Regression;

/**
 * Class LinearRegression
 *
 * Linear model that uses least squares method to approximate solution.
 *
 * @package MachineLearning
 */
class LeastSquares
{

    /**
     * @var float[]
     */
    private $xCoords = [];

    /**
     * @var float[]
     */
    private $yCoords = [];

    /**
     * Holds the y differences from the calcuated regression line
     *
     * @var float[]
     */
    private $yDifferences = [];


    /**
     * Holds the cumulative sum of yDifferences
     *
     * @var float[]
     */
    private $cumulativeSum = [];
    /**
     * @var float
     */
    private $slope;
    /**
     * @var float
     */
    private $intercept;

    /**
     * @var int
     */
    private $coordinateCount = 0;

    /**
     * LinearRegression constructor.
     */
    function __construct()
    {
    }

    /**
     * Append the data and compute the linear regression
     * multiple calls to train in a row keep adding data and calculate the new regression
     *
     *
     * @param array $xCoords the targets (e.g. degree days)
     * @param array $yCoords the samples (e.g. energy used for heating)
     */
    public function train(array $xCoords, array $yCoords)
    {
        $this->resetCalculatedValues();
        $this->appendData($xCoords, $yCoords);
        $this->compute();
    }

    /**
     * @param array $xCoords
     * @param array $yCoords
     */
    private function appendData(array $xCoords, array $yCoords)
    {
        $this->xCoords = array_merge($this->xCoords, $xCoords);
        $this->yCoords = array_merge($this->yCoords, $yCoords);
        $this->countCoordinates();
    }

    /**
     * clear the calculated values
     */
    private function resetCalculatedValues()
    {
        $this->slope         = null;
        $this->intercept     = null;
        $this->yDifferences  = [];
        $this->cumulativeSum = [];
    }

    /**
     * clear the series data
     */
    private function clearData()
    {
        $this->xCoords         = [];
        $this->yCoords         = [];
        $this->coordinateCount = 0;
    }

    /**
     * clear all data so new calls to train() start a fresh
     */
    public function reset()
    {
        $this->resetCalculatedValues();
        $this->clearData();
    }

    /**
     * The amount of increase in y (vertical) for an increase of 1 on the x axis (horizontal)
     *
     * @return float
     */
    public function getSlope(): float
    {
        return $this->returnOrThrowIfNull($this->slope);
    }

    /**
     * The value at which the regression line crosses the y axis (vertical)
     *
     * @return float
     */
    public function getIntercept(): float
    {
        return $this->returnOrThrowIfNull($this->intercept);
    }

    /**
     * @return array
     */
    public function getSlopeAndIntercept(): array
    {
        return array("slope" => $this->slope, "intercept" => $this->intercept);
    }

    /**
     * @return int
     * @throws SeriesCountMismatch
     */
    private function countCoordinates(): int
    {
        // calculate number points
        $this->coordinateCount = count($this->xCoords);
        $yCount                = count($this->yCoords);

        // ensure both arrays of points are the same size
        if ($this->coordinateCount != $yCount) {
            throw new SeriesCountMismatch("Number of elements in arrays do not match {$this->xCoords}:{$yCount}");
        }
        if ($this->coordinateCount === 0) {
            throw new SeriesHasZeroElements('Series has zero elements');
        }
        return $this->coordinateCount;
    }

    /**
     * @param float $value
     *
     * @return float
     * @throws ParameterNotComputedYet
     */
    private function returnOrThrowIfNull(float $value)
    {
        if (null === $value) {
            throw new ParameterNotComputedYet('Parameter not compute yet');
        }
        return $value;
    }

    /**
     * Linear model that uses least squares method to approximate solution.
     */
    private function compute()
    {

        // calculate sums
        $x_sum = array_sum($this->xCoords);
        $y_sum = array_sum($this->yCoords);

        $xx_sum = 0;
        $xy_sum = 0;

        for ($i = 0; $i < $this->coordinateCount; $i++) {
            $xy_sum += ($this->xCoords[$i] * $this->yCoords[$i]);
            $xx_sum += ($this->xCoords[$i] * $this->xCoords[$i]);
        }

        // calculate slope
        $this->slope = (($this->coordinateCount * $xy_sum) - ($x_sum * $y_sum)) / (($this->coordinateCount * $xx_sum) - ($x_sum * $x_sum));

        // calculate intercept
        $this->intercept = ($y_sum - ($this->slope * $x_sum)) / $this->coordinateCount;

    }

    /**
     * predict for a given y value (sample) the x value (target)
     *
     * @param float $y
     *
     * @return float
     */
    public function predictX(float $y): float
    {
        return ($y - $this->getIntercept()) / $this->getSlope();
    }

    /**
     * predict for a given x value (target) the y value (sample)
     *
     * @param float $x
     *
     * @return float
     */
    public function predictY(float $x): float
    {
        return $this->getIntercept() + ($x * $this->getSlope());
    }

    /**
     * Get the differences of the actual data from the regression line
     * This is the differences in y values
     *
     * @return \float[]
     */
    public function getDifferencesFromRegressionLine()
    {
        if (0 === count($this->yDifferences)) {
            for ($i = 0; $i < $this->coordinateCount; $i++) {
                $this->yDifferences[] = $this->yCoords[$i] - $this->predictY($this->xCoords[$i]);
            }
        }
        return $this->yDifferences;
    }

    /**
     * Get the cumulative some of the differences from the regression line
     *
     * @return float[]
     */
    public function getCumulativeSumOfDifferencesFromRegressionLine()
    {
        if (0 === count($this->cumulativeSum)) {
            $differences   = $this->getDifferencesFromRegressionLine();
            $this->cumulativeSum = [$differences[0]];
            for ($i = 1; $i < $this->coordinateCount; $i++) {
                $this->cumulativeSum[$i] = $differences[$i] + $this->cumulativeSum[$i - 1];
            }
        }
        return $this->cumulativeSum;
    }


    /**
     * return an array of Points corresponding to the regression line of the current data
     *
     * @return Point[]
     */
    public function getRegressionLinePoints() {
        $minX = min($this->xCoords);
        $maxX = max($this->xCoords);
        $xStepSize = (($maxX - $minX) / ($this->coordinateCount-1));
        $xy = [];
        for($i=0;$i<$this->coordinateCount;$i++) {
            $x = $minX + ($i*$xStepSize);
            $y = $this->predictY($x);
            $xy[] = new Point($x,$y); // add point
        }
        return $xy;
    }
}