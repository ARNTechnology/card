<?php
/**
 * Copyright (c) 2020
 * Alexandru Negrilã (alex-codes@arntech.ro) - ARN TECHNOLOGY
 */

namespace ARNTech\Card\Model\Financial\Virtual;

use ARNTech\Card\Exception\CardNumberException;
use ARNTech\Card\Exception\Cvv2Exception;
use ARNTech\Card\Exception\ExpirationException;
use ARNTech\Card\Exception\FinancialCardNumberException;
use ARNTech\Card\Exception\InvalidLuhnCard;
use ARNTech\Card\Exception\UnknownCardTypeException;
use ARNTech\Card\Model\Financial\Card as BaseCard;
use ARNTech\Card\Model\Financial\CardNumber;
use ARNTech\Card\Model\Financial\Expiration;
use DateTime;


class Card extends BaseCard
{
    /**
     * @var Cvv2
     */
    private $cvv2;

    /**
     * Card constructor.
     * @param string|CardNumber $number
     * @param string|int|DateTime|Expiration $expiration
     * @param string|Cvv2 $cvv2
     * @throws Cvv2Exception
     * @throws CardNumberException
     * @throws InvalidLuhnCard
     * @throws UnknownCardTypeException
     * @throws FinancialCardNumberException
     * @throws ExpirationException
     */
    public function __construct($number, $expiration, $cvv2)
    {
        parent::__construct($number, $expiration);
        $this->setCvv2($cvv2);
    }

    /**
     * @param string|int|Cvv2 $cvv2
     * @return $this
     * @throws Cvv2Exception
     */
    private function setCvv2($cvv2)
    {
        $this->cvv2 = ($cvv2 instanceof Cvv2) ? $cvv2 : new Cvv2($cvv2);

        if (!$this->cvv2->isValidForCardType($this->getNumber()->getVendor())) {
            throw new Cvv2Exception("Card number and CVV2 do not match.");
        }
        return $this;
    }

    /**
     * @return Cvv2
     */
    public function getCvv2()
    {
        return $this->cvv2;
    }

    public function isExpired()
    {
        return $this->getExpiration() <= new DateTime();
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), ['cvv2' => strval($this->getCvv2())]);
    }
}