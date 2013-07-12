<?php

/**
 * Long Math Function Library.
 * 
 * Performs basic math operations on numbers represented as arbitrary length strings
 * 
 * @todo Improve efficiency
 * @todo Add more documentattion
 * @todo Complete multiply and divide functions
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
        
        # determine if the output will be negative
        $negative = false;
        if (self::is_negative($str1) && self::is_negative($str2)):
            $negative = true;
        elseif (self::is_negative($str1) && self::is_positive($str2)):
            if (self::absolute($str1) === $str2)
                return 0;
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

        # get absolute values for input numbers
        $abs1 = self::absolute($str1);
        $abs2 = self::absolute($str2);

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
                $total = substr($sum, 1, 1);
                $carry = 1;
            endif;
            $x--;
        endwhile;
        return $total;
    }

    /**
     * 
     * @param string $str1
     * @param string $str2
     * @return string
     */
    public static function compare($str1, $str2) {
        $bnegresult = false;
        if (self::absolute($str1) === '0')
            return $str2;
        if (self::absolute($str2) === '0')
            return $str1;

        if (self::is_negative($str1) && self::is_positive($str2)):              //check for opposite sign
            if (self::absolute($str1) === self::absolute($str2)):
                return '0';                                                     //items cancel out, return 0
            endif;
            return self::add(self::negative_absolute($str1), self::negative_absolute($str2));

        elseif (self::is_negative($str2) && self::is_positive($str1)):
            return self::add($str1, $str2);

        elseif (self::is_negative($str1) && self::is_negative($str2)):
            if ($str1 === $str2)
                return '0';
            if (self::return_larger($str1, $str2) === $str2):
                $bnegresult = false;
            else:
                $bnegresult = true;
            endif;

        elseif (self::is_positive($str1) && self::is_positive($str2)):
            if ($str1 === $str2)
                return '0';
            if (self::return_larger($str1, $str2) === $str2)
                $bnegresult = true;
        endif;

        $compare = '';
        $carry = 0;
        $top = self::absolute(self::return_larger($str1, $str2));
        $bot = self::absolute(self::return_smaller($str1, $str2));
        $bot = str_pad($bot, strlen($top), '0', STR_PAD_LEFT);
    }

    public static function multiply($str1, $str2) {
        
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

        $str1arr = explode($str1);
        $str2arr = explode($str2);

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

    public static function verify_string($str) {
        $str = trim($str);
        $allowed = "1234567890-.";
        foreach ($str as $s)
            if (strpos($allowed, $s) === false)
                throw new Exception('Invalid number');
        return true;
    }

    public static function is_positive($str) {
        $str = trim($str);
        return substr($str, 0, 1) !== '-';
    }
    

}
