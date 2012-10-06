<?php

putenv("TEST=1");
require_once(dirname(__FILE__).'/../bootstrap.php');
require_once(GRIFFIN_ROOT.'/extlib/simpletest/autorun.php');

$all_tests = new TestSuite('All Tests');
$all_tests->add(new RouterTest());

$tr = new TextReporter();

list($usec, $sec) = explode(" ", microtime());
$start =  ((float)$usec + (float)$sec);

$all_tests->run( $tr ); # Run the test cases.

list($usec, $sec) = explode(" ", microtime());
$finish =  ((float)$usec + (float)$sec);

$runtime = round($finish - $start)/60;

echo "Time elapsed: ".round($runtime)." minute(s)";

if($tr->getFailCount() > 0) exit(1);
else exit(0);

?>