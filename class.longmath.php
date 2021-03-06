<?php

/**
 * Long Math Function Library.
 * 
 * Performs basic math operations on numbers represented as arbitrary length strings
 * 
 * @todo Improve efficiency
 * @todo Add more documentattion
 * @todo Complete multiply and divide functions
 * @todo Complete decimal support
 * @author Michael Meyer
 * @email m.meyer2k@gmail.com
 */
class longmath {

    /**
     * Add two long numeric strings. Decimal not currently supported.
     * @param string $str1
     * @param string $str2
     * @return string
     */
    public static function add($str1, $str2) {

        # verify that the input numbers are actually valid
        self::verify_string($str1);
        self::verify_string($str2);

        # trim inputs, just in case
        $str1 = trim($str1);
        $str2 = trim($str2);

        # determine if the output will be negative
        $negative = false;
        if (self::is_negative($str1) && self::is_negative($str2)):
            # if both input parameters are negative, output must be negative
            $negative = true;
        elseif (self::is_negative($str1) && self::is_positive($str2)):
            # if items cancel out, return 0
            if (self::absolute($str1) === $str2)
                return '0';
            if (self::return_larger($str1, $str2) === $str2):
                return self::compare($str2, self::absolute($str1));
            else:
                return self::compare(self::absolute($str1), $str2);
            endif;
        elseif (self::is_negative($str2) && self::is_positive($str1)):
            if (self::absolute($str2) === $str1)
                return 0;
            if (self::return_larger($str2, $str1) === $str1):
                return self::compare($str1, self::absolute($str2));
            else:
                return self::negative_absolute(self::compare($str1, self::absolute($str2)));
            endif;
        endif;

        if (self::is_decimal($str1) || self::is_decimal($str2))
            return self::dec_add($str1, $str2, $negative);

        # get absolute values for input numbers
        $abs1 = self::absolute($str1);
        $abs2 = self::absolute($str2);

        # get the length of the 2 numbers
        $length = strlen($abs1);
        if (strlen($abs2) > $length)
            $length = strlen($abs2);

        # pad numbers to equal length
        $abs1 = str_pad($abs1, $length, '0', STR_PAD_LEFT);
        $abs2 = str_pad($abs2, $length, '0', STR_PAD_LEFT);

        $length--;

        $estr1 = str_split($abs1);
        $estr2 = str_split($abs2);
        $carry = 0;
        $total = '';

        $x = $length;
        while ($x >= 0):
            $sum = (int) $estr1[$x] + (int) $estr2[$x];

            if ($carry > 0)
                $sum += $carry;

            if ($sum < 10):
                $total = $sum . $total;
                $carry = 0;
            else:
                $total = substr($sum, 1, 1) . $total;
                $carry = 1;
            endif;
            $x--;
        endwhile;

        # include any lingering carry amount
        if ($carry)
            $total = $carry . $total;

        # set output as negative, if needed
        if ($negative)
            $total = '-' . $total;

        return $total;
    }

    /**
     * Subtract one number from another
     * @param string $str1
     * @param string $str2
     * @return string
     */
    public static function compare($str1, $str2) {

        # verify that the input numbers are actually valid
        self::verify_string($str1);
        self::verify_string($str2);

        # trim inputs, just in case
        $str1 = trim($str1);
        $str2 = trim($str2);

        # determine if output needs to be negative
        $negative = false;
        if (self::absolute($str1) === '0')
            return '-' . $str2;
        if (self::absolute($str2) === '0')
            return $str1;

        # check for opposite sign
        if (self::is_negative($str1) && self::is_positive($str2)):
            return self::add(self::negative_absolute($str1), self::negative_absolute($str2));

        elseif (self::is_negative($str1) && self::is_negative($str2)):
            return '-' . self::add(self::absolute($str1), $str2);

        elseif (self::is_negative($str2) && self::is_positive($str1)):
        #return self::add($str1, $str2);

        elseif (self::is_positive($str1) && self::is_positive($str2)):
            if ($str1 === $str2)
                return '0';
            if (self::return_larger($str1, $str2) === $str2)
                $negative = true;
        endif;

        $compare = '';
        $carry = 0;

        $top = self::absolute(self::return_larger($str1, $str2));
        $bot = self::absolute(self::return_smaller($str1, $str2));
        $bot = str_pad($bot, strlen($top), '0', STR_PAD_LEFT);

        # get the length of longest string
        $x = strlen($top);
        $x--;

        $top = str_split($top);
        $bot = str_split($bot);

        while ($x >= 0):
            $top_digit = $top[$x] - $carry;

            if ($bot[$x] > $top_digit)
                $carry = 1;

            $compare = ($top_digit - $bot[$x]) . $compare;

            $x--;
        endwhile;

        # set output as negative, if needed
        if ($negative)
            $compare = '-' . $compare;

        return $compare;
    }

    /**
     * 
     * @param string $str1
     * @param string $str2
     * @return string
     */
    public static function multiply($str1, $str2) {

        # verify that the input numbers are actually valid
        self::verify_string($str1);
        self::verify_string($str2);

        # trim inputs, just in case
        $str1 = trim($str1);
        $str2 = trim($str2);

        # return 0 if either input is 0
        if ($str1 === '0' || $str2 === '0')
            return '0';

        # simple multiplication by 1
        if ($str1 === '1')
            return $str2;
        if ($str2 === '1')
            return $str1;

        $negative = false;
        if (self::is_negative($str1) && self::is_positive($str2)):
            $negative = true;
        elseif (self::is_negative($str2) && self::is_positive($str1)):
            $negative = true;
        endif;

        $top = self::return_larger($str1, $str2);
        $bot = self::return_smaller($str1, $str2);

        $top = array_reverse(str_split($top));
        $bot = array_reverse(str_split($bot));

        $lines = array();
        $count = 0;
        foreach ($bot as $b):
            $carry = 0;
            $line = '';
            foreach ($top as $t):
                $digits_product = ($t * $b);

                if ($carry):
                    $digits_product+= $carry;
                    $carry = 0;
                endif;

                # get last digit of product
                $line_product = substr($digits_product, -1);


                if ($digits_product > 9)
                    $carry = substr($digits_product, 0, 1);

                $line = $line_product . $line;
            endforeach;

            if ($carry)
                $line = $carry . $line;

            $lines[] = $line . str_repeat('0', $count);
            $count++;
        endforeach;

        $product = '0';

        foreach ($lines as $l)
            $product = self::add($product, $l);

        # set output as negative, if needed
        if ($negative)
            $compare = '-' . $compare;

        return $product;
    }

    public static function divide($str1, $str2) {
        # verify that the input numbers are actually valid
        self::verify_string($str1);
        self::verify_string($str2);

        # trim inputs, just in case
        $str1 = trim($str1);
        $str2 = trim($str2);

        if (self::absolute($str2) === '0')
            throw new Exception('Division by zero');
    }

    public static function return_smaller($str1, $str2) {
        if (self::return_larger($str1, $str2) === $str1)
            return $str2;
        return $str1;
    }

    public static function return_larger($str1, $str2) {

        if ($str1 === $str2)
            return $str1;

        if (self::is_positive($str1) && self::is_negative($str2))
            return $str1;
        if (self::is_positive($str2) && self::is_negative($str1))
            return $str2;

        $str1 = self::absolute($str1);
        $str2 = self::absolute($str2);

        $str1len = strlen($str1);
        $str2len = strlen($str2);

        if ($str1len > $str2len)
            return $str1;
        if ($str1len < $str2len)
            return $str2;

        $str1arr = str_split($str1);
        $str2arr = str_split($str2);

        for ($x = 0; $x <= $str1len; $x++):
            if ((int) $str1arr[$x] > (int) $str2arr[$x])
                return $str1;
            if ((int) $str1arr[$x] < (int) $str2arr[$x])
                return $str2;
        endfor;
    }

    public static function is_negative($str) {
        if (substr($str, 0, 1) === '-')
            return true;
        return false;
    }

    public static function absolute($str) {
        if (self::is_negative($str))
            return substr($str, 1);
        return $str;
    }

    public static function negative_absolute($str) {
        return '-' . self::absolute($str);
    }

    public static function verify_string($str, $throw = true) {
        $str = trim($str);

        if (substr_count($str, '-') > 1)
            throw new Exception('Invalid number');

        if (substr_count($str, '.') > 1)
            throw new Exception('Invalid number');

        $allowed = "1234567890-";
        $str = str_split($str);
        foreach ($str as $s):
            if (strpos($allowed, $s) === false):
                if ($throw):
                    throw new Exception('Invalid number');
                else:
                    return false;
                endif;
            endif;
        endforeach;
        return true;
    }

    public static function is_positive($str) {
        $str = trim($str);
        return substr($str, 0, 1) !== '-';
    }

    public static function is_decimal($str) {
        return strpos($str, '.') !== false;
    }

    private static function dec_add($str1, $str2, $negative) {

        # normalize input that might not have a decimal place
        if (!self::is_decimal($str1))
            $str1 .= '.0';
        if (!self::is_decimal($str2))
            $str2 .= '.0';

        $exp1 = explode('.', $str1);
        $exp2 = explode('.', $str2);

        # gather decimal places from exploded numbers
        # remove trailing zeros
        $dec1 = rtrim($exp1[1], '0') ? : '0';
        $dec2 = rtrim($exp2[1], '0') ? : '0';

        # get the length of the longest decimal to have a carryover comparison
        $length = strlen($dec1);
        if (strlen($dec2) > $length)
            $length = strlen($dec2);

        $dec1 = str_pad($dec1, $length, '0');
        $dec2 = str_pad($dec2, $length, '0');

        $decimal_result = self::add($dec1, $dec2);

        $integer_result = self::add($exp1[0], $exp2[0]);

        if (strlen($decimal_result) > $length):
            $integer_result = self::add($integer_result, '1');
            $decimal_result = substr($decimal_result, 1);
        endif;

        $negative = $negative ? '-' : '';

        return $negative . $integer_result . '.' . $decimal_result;
    }

}
