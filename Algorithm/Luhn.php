<?php


namespace ARNTech\Utils\Algorithm;

use \InvalidArgumentException;
use \Exception;

class Luhn
{
    private static $sumTable = array(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9), array(0, 2, 4, 6, 8, 1, 3, 5, 7, 9));
    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Can not unserialize.");
    }

    /**
     * @param string|int $number
     * @return int
     * @throws InvalidArgumentException
     */
    public static function calculateChecksum($number)
    {
        if(is_int($number)) {
            $number = strval($number);
        }elseif (!is_string($number)){
            throw new \InvalidArgumentException('The number must be int or string.');
        }
        $length = strlen($number);
        $sum = 0;
        $flip = 1;
        // Sum digits (last one is check digit, which is not in parameter)
        for($i = $length-1; $i >= 0; --$i) $sum += self::$sumTable[$flip++ & 0x1][$number[$i]];
        // Multiply by 9
        $sum *= 9;
        // Last digit of sum is check digit
        return (int)substr($sum, -1, 1);
    }

    /**
     * @param string|int $number
     * @param string|int $checksumDigit - single digit
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function validateChecksum($number, $checksumDigit)
    {
        if(is_string($checksumDigit)) {
            $checksumDigit = trim($checksumDigit);
            if(!is_numeric($checksumDigit)) {
                throw new \InvalidArgumentException('Checksum must be numeric.');
            }
            $checksumDigit=intval($checksumDigit);
        }elseif (!is_int($checksumDigit)) {
            throw new \InvalidArgumentException('Checksum must be numeric.');
        }
        if ($checksumDigit > 9 || $checksumDigit < 0) {
            throw new \InvalidArgumentException('Checksum must be a single positive digit.');
        }
        return (self::calculateChecksum($number) == $checksumDigit);
    }
}