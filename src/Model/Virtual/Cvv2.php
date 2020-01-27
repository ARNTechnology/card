<?php


namespace ARNTech\Card\Model\Virtual;

use ARNTech\Card\Exception\Cvv2Exception;
use ARNTech\Card\Model\CardNumber;
use InvalidArgumentException;

class Cvv2
{
    /**
     * @var string
     */
    private $number;

    /**
     * Cvv2 constructor.
     * @param string|int $number
     */
    public function __construct($number)
    {
        if (!is_string($number) && !is_int($number)) {
            throw new InvalidArgumentException("CVV must be int or string");
        }
        if (is_int($number)) {
            $number = strval($number);
        }
        if (is_string($number)) {
            $number = trim($number);
            $len = strlen($number);
            if (!in_array($len, [3, 4])) {
                throw new Cvv2Exception("Invalid CVV code provided. Invalid length.");
            }
        } else {
            throw new Cvv2Exception('Invalid CVV code provided.');
        }
        $this->number = $number;
    }

    /**
     * @param string|int $type
     * @return bool
     */
    public function isValidForCardType($type)
    {
        if (is_string($type)) {
            $type = intval($type);
        }

        if (!is_int($type) || !isset(CardNumber::$names[$type])) {
            throw new InvalidArgumentException("Invalid Card type provided.");
        }
        $len = strlen($this->number);
        if (($len == 4 && CardNumber::TYPE_AMEX) || $len == 3) {
            return true;
        }
        return false;
    }

    public function __toString()
    {
        return $this->number;
    }
}