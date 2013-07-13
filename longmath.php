<?php

#var_dump($argv);

if ($argv[1] === '-h' || $argv[1] === '--help'):
    echo '
Long math options:
    -i, --interactive      Interactive mode
    -a, --add              Add 2 numbers (php longmath.php -a 1234 5678)
    -c, --compare          Subtract one number from another
    -m, --multiply         Multiply 2 numbers
';
elseif ($argv[1] === '-a' || $argv[1] === '--add'):
    
endif;
