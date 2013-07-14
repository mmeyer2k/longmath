<?php

# include the longmath class
require_once 'class.longmath.php';

# begin handling main option flags
if ($argv[1] === '-h' || $argv[1] === '--help'):
    echo '
Longmath, a PHP library for arbitrary precision mathematics
https://github.com/mmeyer2k/longmath
Command line options:
    -i, --interactive      Interactive mode
    -a, --add              Add 2 numbers (php longmath.php -a 1234 5678)
    -c, --compare          Subtract one number from another
    -m, --multiply         Multiply 2 numbers
    -d, --divide           Divide one number out of another
    -p, --power            Raise one number to the power of another
    -z, --collatz          Test the Collatz Conjecture on a given large number
';

elseif ($argv[1] === '-a' || $argv[1] === '--add'):
    echo longmath::add($argv[2], $argv[3]);

elseif ($argv[1] === '-c' || $argv[1] === '--compare'):
    echo longmath::compare($argv[2], $argv[3]);

elseif ($argv[1] === '-i' || $argv[1] === '--interactive'):
    
endif;
