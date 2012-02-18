<?php
namespace Quipp\Validation;

class StdRules {
    public function alph($in) {
        $pattern = '[[:alnum:]]';
        return (boolean)preg_match("/" . $pattern . "/i", $in);
    }

    public function mail($in) {
        
        if (filter_var($in, FILTER_VALIDATE_EMAIL)) {
            list($user, $domain) = explode('@', $in);
            return (boolean)strpos($domain, '.');
        } 
        
        return false;
    }

    /**
     * @param string Pattern: ([1-]555-555-5555)
     */
    public function phon($in) {
        $pattern = '^(?:\+?1[-. ]?)?\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$';
//$pattern = '1?\W*([2-9][0-8][0-9])\W*([2-9][0-9]{2})\W*([0-9]{4})(\se?x?t?(\d*))?';
        return (boolean)preg_match("/" . $pattern . "/i", $in);
    }

    public function post($in) {
        if (empty($in)) {
            return false;
        }

        $pattern = '^([abceghjklmnprstvxy][0-9][a-z][\s-]*[0-9][a-z][0-9])?$|^([0-9]{5})?$|^([0-9]{5}-[0-9]{4})?$';
        return (boolean)preg_match("/" . $pattern . "/i", $in);
    }

    public function prov($in) {
        return (boolean)preg_match("%^[A-Z]{2}$%",strtoupper($in));
    }
    
    public function ctry($in) {
        return (boolean)preg_match("%^(CANADA|(UNITED\sSTATES))$%",strtoupper($in));
    }

    public function user($in) {
        $pattern = '%^[A-Za-z0-9\-\_]{3,50}$%';
    	return (boolean)(preg_match($pattern, $in));
    }

    public function gndr($in) {
        return in_array(strtolower($in), array('male', 'female'));
    }

    /**
     * @param string YYYY-MM-DD
     */
    public function date($in) {
        if (substr_count($in, '-') !== 2) {
            return false;
        }
        list($y, $m, $d) = explode('-', $in);
        return checkdate($m, $d, $y);
    }

    public function numb($in) {
        return (boolean)filter_var($in, FILTER_VALIDATE_INT);
    }

    public function webs($in) {
        if (false === (strpos($in, '.'))) {
            return false;
        }

        return (boolean)filter_var($in, FILTER_VALIDATE_URL);
    }

    public function ccnm($in) {

        // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
        $number=preg_replace('/\D/', '', $in);
        
        if (str_replace(array('-', ' '), '', $in) != $number) {
            return false;
        }
        
        // Set the string length and parity
        $number_length = strlen($number);
        $parity = $number_length % 2;
        
        // Loop through each digit and do the maths
        $total = 0;
        for ($i = 0; $i < $number_length; $i++) {
            $digit = $number[$i];
            // Multiply alternate digits by two
            
            if ($i % 2 == $parity) {
                $digit *= 2;
                // If the sum is two digits, add them together (in effect)
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            // Total up the digits
            $total += $digit;
        }
        
        // If the total mod 10 equals 0, the number is valid
        return ($total % 10 == 0);
    }

    public function chck($in) {
        return true;
    }
}