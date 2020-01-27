<?php
/**
 * Copyright (c) 2020
 * Alexandru Negrilã (alex-codes@arntech.ro) - ARN TECHNOLOGY
 */

namespace ARNTech\Card\Model\Financial;

use ARNTech\Card\Exception\CardNumberException;
use ARNTech\Card\Exception\ExpirationException;
use ARNTech\Card\Exception\FinancialCardNumberException;
use ARNTech\Card\Exception\InvalidLuhnCard;
use ARNTech\Card\Exception\UnknownCardTypeException;
use ARNTech\Card\Interfaces\FinancialCardInterface;
use ARNTech\Utils\Helper\DateTimeHelper;
use \DateTime;
use Exception;
use InvalidArgumentException;

class Card implements FinancialCardInterface
{
    /**
     * @var CardNumber
     */
    private $number;

    /**
     * @var Expiration
     */
    private $expiration;

    /**
     * Card constructor.
     * @param string|CardNumber $number
     * @param string|int|DateTime|Expiration $expiration
     * @throws CardNumberException
     * @throws InvalidLuhnCard
     * @throws UnknownCardTypeException
     * @throws FinancialCardNumberException
     * @throws ExpirationException
     */
    public function __construct($number, $expiration)
    {
        $this->setNumber($number);
        $number = null;
        $this->setExpiration($expiration);
    }

    /**
     * @param $number
     * @throws CardNumberException
     * @throws InvalidLuhnCard
     * @throws UnknownCardTypeException
     * @throws FinancialCardNumberException
     */
    protected function setNumber(&$number)
    {
        $this->number = ($number instanceof CardNumber) ? $number : new CardNumber($number);
        if (is_string($number)) {
            $number = null;
        }
    }

    /**
     * @param $expiration
     * @return $this
     * @throws ExpirationException
     */
    protected function setExpiration($expiration)
    {
        try {
            if (is_string($expiration) || is_int($expiration)) {
                $expiration = DateTimeHelper::dateTimeFromYearMonthString($expiration);
            }
            if ($expiration instanceof DateTime) {
                $expiration = new Expiration($expiration->format("Y-m-d"));
                DateTimeHelper::moveDateToEndOfDay($expiration);
                $expiration = DateTimeHelper::moveDateToEndOfMonth($expiration);
            } else {
                throw new InvalidArgumentException("Invalid expiration date.");
            }
        } catch (Exception $e) {
            throw new ExpirationException("Invalid expiration.", 0, $e);
        }

        $this->expiration = $expiration;
        return $this;
    }

    /**
     * @return CardNumber
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return Expiration
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    public function jsonSerialize()
    {
        return ['number' => strval($this->getNumber()), 'expiration' => strval($this->getExpiration())];
    }
}