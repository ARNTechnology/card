<?php
/**
 * Copyright (c) 2020
 * Alexandru Negrilã (alex-codes@arntech.ro) - ARN TECHNOLOGY
 */

namespace ARNTech\Card\Model;


use ARNTech\Card\Exception\UnknownCardTypeException;
use InvalidArgumentException;

/**
 * Class CardNumber
 * @package ARNTech\Card\Model
 */
class CardNumber
{
    const MII_TYPE_AIRLINES = 1;
    const MII_TYPE_ENTERTAINMENT = 3;
    const MII_TYPE_FINANCIAL = 4;
    const MII_TYPE_PETROLEUM = 7;
    const MII_TYPE_TELECOM = 8;
    const MII_TYPE_NATIONAL = 9;

    public static $cardTypes = [
        self::MII_TYPE_AIRLINES => 'Airlines',
        self::MII_TYPE_ENTERTAINMENT => 'Entertainment', //and Travel
        self::MII_TYPE_FINANCIAL => 'Financial', //banking or merchandising
        self::MII_TYPE_PETROLEUM => 'Petroleum',
        self::MII_TYPE_TELECOM => 'Telecommunications',
        self::MII_TYPE_NATIONAL => 'National', //National Assignment
    ];

    private static $miiMapping = [
        self::MII_TYPE_AIRLINES => [1, 2],
        self::MII_TYPE_ENTERTAINMENT => [3],
        self::MII_TYPE_FINANCIAL => [4, 5, 6],
        self::MII_TYPE_PETROLEUM => [7],
        self::MII_TYPE_TELECOM => [8],
        self::MII_TYPE_NATIONAL => [9]
    ];
    /**
     * @var string
     */
    private $number;

    /**
     * @var int
     */
    private $type;

    /**
     * CardNumber constructor.
     * @param string $number
     * @throws UnknownCardTypeException
     * @throws \ARNTech\Card\Exception\CardNumberException
     */
    public function __construct($number)
    {
        $this->setNumber($number);
        $this->handleType();
    }

    public function &getPlainNumber()
    {
        $number = "" . $this->number;
        return $number;
    }

    /**
     * @throws UnknownCardTypeException
     */
    private function handleType()
    {
        $mii = $this->getMII();
        $number = null;
        foreach (self::$miiMapping as $type => $miis) {
            if (in_array($mii, $miis)) {
                $this->type = $type;
                $mii = null;
                return;
            }
        }
        $mii = null;
        throw new UnknownCardTypeException("Card is of an unknown type.");
    }

    /**
     * @return string
     */
    private function &getMII()
    {
        $number = $this->getPlainNumber();
        $mii = substr($number, 0, 1);
        $number = null;
        return $mii;
    }

    /**
     * @param string $number
     * @return $this
     * @throws InvalidArgumentException
     */
    private function setNumber(&$number)
    {
        if (!is_string($number)) {
            throw new InvalidArgumentException('Card number must be string.');
        }
        $number = trim($number);
        if (empty($number)) {
            throw new InvalidArgumentException('Card number must be non empty string.');
        }
        if (!is_numeric($number)) {
            throw new InvalidArgumentException('Card number must be non numeric.');
        }

        $this->number = $number;
        $number = null;
        return $this;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        $number = $this->getPlainNumber();
        $first = substr($number, 0, 4);
        $last = substr($number, -4);
        $number = null;
        return sprintf("%s%s%s", $first, str_repeat('x', 8), $last);
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTypeTest()
    {
        return self::$cardTypes[$this->type];
    }

    public function __toString()
    {
        return $this->getNumber();
    }


}