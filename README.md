# Least Squares Linear Regression class

A Linear regression class that uses the least squares method to approximate a straight line to a data set
with some example test data to run it against. The class is called \MachineLearning\Regression\LeastSquares.

The example uses composer to generate the class auto loader even though there are no dependencies, as in a larger project
you'd include with composer.

Usage:-

    $x = [...]; // target values
    $y = [...]; // observation values

    $linearRegression = new \MachineLearning\Regression\LeastSquares();
    
    $linearRegression->train($x, $y); // train on targets, samples

    echo "Slope: ".$linearRegression->getSlope().PHP_EOL; // show the slope
    echo "Intercept: ".$linearRegression->getIntercept().PHP_EOL; // show the intercept
        
    // return array of differences of y values from the regression line
    $differences = $linearRegression->getDifferencesFromRegressionLine();
    
    // return array of cumulative sum of the differences of y values from the regression line
    $cumulativeSum = $linearRegression->getCumulativeSumOfDifferencesFromRegressionLine();
    
    // return array of Point objects giving the x,y values of the regression line
    // for current data
    $regressionLine = $linearRegression->getRegressionLinePoints();
    
    $regressionLine[0]->getX();
    $regressionLine[0]->getY();

    echo $linearRegression->predictX($anObservationValue).PHP_EOL; // predict X

    echo $linearRegression->predictY($aTargetValue).PHP_EOL; // predict Y
    
    echo $linearRegression->getRSquared().PHP_EOL; // Regression fit; 1 = perfect fit 0 = no fit


A coded example can be run using the following, Note it relies on the classes being auto loaded via composer:-

    cd examples
    php example.php
    
    
The example reads from a CSV file in tests/data/ which has a couple of years of data in.