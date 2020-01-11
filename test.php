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

$d = new DiffChecker();
$d->compare($a, $b);

echo $d->diff;