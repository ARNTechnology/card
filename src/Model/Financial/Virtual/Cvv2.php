<?php
/**
 * Copyright (c) 2020
 * Alexandru Negrilã (alex-codes@arntech.ro) - ARN TECHNOLOGY
 */

namespace ARNTech\Card\Model\Financial\Virtual;

use ARNTech\Card\Exception\Cvv2Exception;
use ARNTech\Card\Model\Financial\CardNumber;
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
     * @throws Cvv2Exception
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

        if (!is_int($type) || !isset(CardNumber::$vendors[$type])) {
            throw new InvalidArgumentException("Invalid Card type provided.");
        }
        $len = strlen($this->number);
        if (($len == 4 && $type == CardNumber::VENDOR_AMEX) || $len == 3) {
            return true;
        }
        return false;
    }

    public function __toString()
    {
        return $this->number;
    }
}