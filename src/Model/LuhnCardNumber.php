<?php
/**
 * Copyright (c) 2020
 * Alexandru Negrilã (alex-codes@arntech.ro) - ARN TECHNOLOGY
 */

namespace ARNTech\Card\Model;


use ARNTech\Card\Exception\InvalidLuhnCard;
use ARNTech\Utils\Algorithm\Luhn;

class LuhnCardNumber extends CardNumber
{
    /**
     * LuhnCardNumber constructor.
     * @param string $number
     * @throws InvalidLuhnCard
     * @throws \ARNTech\Card\Exception\CardNumberException
     * @throws \ARNTech\Card\Exception\UnknownCardTypeException
     */
    public function __construct($number)
    {
        parent::__construct($number);
        $number = null;
        $this->validatesLuhn();
    }

    /**
     * @throws InvalidLuhnCard
     */
    private function validatesLuhn()
    {
        $number = $this->getPlainNumber();
        $validatesLuhn = $this->isValidLuhn($number);
        $number = null;
        if (!$validatesLuhn) {
            throw new InvalidLuhnCard("Card number does not validate Luhn");
        }
    }

    /**
     * @param string $number
     * @return bool
     */
    private function isValidLuhn($number)
    {
        return Luhn::validateChecksum(substr($number, 0, -1), substr($number, -1, 1));
    }
}