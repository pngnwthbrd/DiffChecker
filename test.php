<?php

require 'DiffChecker.php';

$a = '
// check if foo is bar
if ($foo == $bar) {
        echo \'foo is bar \';
} else {
        echo \'foo is not bar \'
}
';

$b = '
// check if foo is bar
if ($foo === $bar) {
        echo \'foo is bar \';
} else {
        echo \'foo is not bar \';
}
';

$diffChecker = new DiffChecker();
$diffChecker->compare($a, $b);

echo $diffChecker->diff;