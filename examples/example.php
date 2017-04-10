<?php
/**
 *
 * Test linear regression analysis
 * to get the slope and intercept
 *
 */


require_once '../vendor/autoload.php';

$x = [];
$y = [];


// Get data from CSV
$fd = fopen(__DIR__ . '/data/data.csv', 'r');

// column indexes in the CSV start from zero
$ENERGY_IDX     = 2;      // col 2 = first column with energy data
$DEGREE_DAY_IDX = 14; // col 14 = fifteen degree day data
$DAYS_OF_DATA   = 365;  // How many days of data maximum to read from the file

$c = 0;
while ($row = fgetcsv($fd, 4096, ',')) {
    $c++;
    if (1 == $c) {
        continue; // Skip header row
    }
    if ('' == $row[$DEGREE_DAY_IDX] || '' == $row[$ENERGY_IDX]) {
        // Skip empty data
        continue;
    }
    $x[] = $row[$DEGREE_DAY_IDX]; // Degree days
    $y[] = $row[$ENERGY_IDX]; // 1st E
    if ($DAYS_OF_DATA + 1 == $c) {
        break; // only first 365 days plus 1 header row;
    }
}
fclose($fd);

$linearRegression = new \MachineLearning\Regression\LeastSquares();

$linearRegression->train($x, $y); // targets, samples
$results = $linearRegression->getSlopeAndIntercept();

// var_dump($results); // Dump slope and intercept out
// You can validate against slope and intercept functions in Excel, google docs etc

$differences = $linearRegression->getDifferencesFromRegressionLine();

$cumulativeSum = $linearRegression->getCumulativeSumOfDifferencesFromRegressionLine();

$regressionLine = $linearRegression->getRegressionLinePoints();

echo $linearRegression->getRSquared().PHP_EOL; die;

echo implode(',', ['degreeDay-x','energy-y','rX','rY','yDiff','cumSumyDiff']).PHP_EOL;
foreach($x as $i => $v) {
    $row = [$v,$y[$i],$regressionLine[$i]->getX(),$regressionLine[$i]->getY(),$differences[$i],$cumulativeSum[$i]];
    echo implode(',', $row).PHP_EOL;
}
