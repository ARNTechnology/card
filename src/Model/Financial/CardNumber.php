<?php
/**
 * Copyright (c) 2020
 * Alexandru Negrilã (alex-codes@arntech.ro) - ARN TECHNOLOGY
 */

namespace ARNTech\Card\Model\Financial;

use ARNTech\Card\Exception\FinancialCardNumberException;
use ARNTech\Card\Model\LuhnCardNumber;
use ARNTech\Card\Exception\CardNumberException;
use ARNTech\Card\Exception\InvalidLuhnCard;
use ARNTech\Card\Exception\UnknownCardTypeException;

class CardNumber extends LuhnCardNumber
{
    const VENDOR_VISA = 1;
    const VENDOR_MASTERCARD = 2;
    const VENDOR_AMEX = 3;
    const VENDOR_MAESTRO = 4;
    const VENDOR_DISCOVER = 5;
    const VENDOR_DINERS = 6;
    const VENDOR_DINERSUS = 7;
    const VENDOR_JCB = 8;
    const VENDOR_UNIONPAY = 9;
    const VENDOR_LASER = 10;
    const VENDOR_SOLO = 11;

    /**
     * @var array
     */
    public static $vendors = [
        self::VENDOR_VISA => 'Visa',
        self::VENDOR_MASTERCARD => 'Mastercard',
        self::VENDOR_AMEX => 'American Express',
        self::VENDOR_MAESTRO => 'Maestro',
        self::VENDOR_DISCOVER => 'Discover',
        self::VENDOR_DINERS => 'Diners Club',
        self::VENDOR_DINERSUS => 'Diners Club US',
        self::VENDOR_JCB => 'JCB',
        self::VENDOR_UNIONPAY => 'UnionPay',
        self::VENDOR_LASER => 'Laser',
        self::VENDOR_SOLO => 'Solo'
    ];

    private static $vendorValidators = [
        self::VENDOR_AMEX => [
            'length' => [15],
            'prefix' => ['34', '37']
        ],
        self::VENDOR_DINERS => [
            'length' => [14],
            'prefix' => ['300', '301', '302', '303', '304', '305', '36']
        ],
        self::VENDOR_DINERSUS => [
            'length' => [16],
            'prefix' => ['54', '55']
        ],
        self::VENDOR_DISCOVER => [
            'length' => [16],
            'prefix' => ['6011', '622126', '622127', '622128', '622129', '62213', '62214', '62215', '62216', '62217', '62218', '62219', '6222', '6223', '6224', '6225', '6226', '6227', '6228', '62290', '62291', '622920', '622921', '622922', '622923', '622924', '622925', '644', '645', '646', '647', '648', '649', '65']
        ],
        self::VENDOR_JCB => [
            'length' => [16],
            'prefix' => ['3528', '3529', '353', '354', '355', '356', '357', '358']
        ],
        self::VENDOR_LASER => [
            'length' => [16, 17, 18, 19],
            'prefix' => ['6304', '6706', '6771', '6709']
        ],
        self::VENDOR_MAESTRO => [
            'length' => [12, 13, 14, 15, 16, 17, 18, 19],
            'prefix' => ['5018', '5020', '5038', '6304', '6759', '6761', '6762', '6763', '6764', '6765', '6766']
        ],
        self::VENDOR_MASTERCARD => [
            'length' => [16],
            'prefix' => ['51', '52', '53', '54', '55']
        ],
        self::VENDOR_SOLO => [
            'length' => [16, 18, 19],
            'prefix' => ['6334', '6767']
        ],
        self::VENDOR_UNIONPAY => [
            'length' => [16, 17, 18, 19],
            'prefix' => ['622126', '622127', '622128', '622129', '62213', '62214', '62215', '62216', '62217', '62218', '62219', '6222', '6223', '6224', '6225', '6226', '6227', '6228', '62290', '62291', '622920', '622921', '622922', '622923', '622924', '622925']
        ],
        self::VENDOR_VISA => [
            'length' => [16],
            'prefix' => ['4']
        ]
    ];

    /**
     * @var int
     */
    private $vendor = 0;

    /**
     * CardNumber constructor.
     * @param string $number
     * @throws FinancialCardNumberException
     * @throws CardNumberException
     * @throws InvalidLuhnCard
     * @throws UnknownCardTypeException
     */
    public function __construct($number)
    {
        parent::__construct($number);
        $number = null;
        $this->handleVendor();
    }

    /**
     * @return int
     */
    public function getVendor()
    {
        return $this->vendor;
    }
    /**
     * @return string
     */
    public function getVendorName()
    {
        return self::$vendors[$this->vendor];
    }

    /**
     * @throws FinancialCardNumberException
     */
    private function handleVendor()
    {
        $iin = $this->getIIN();
        $cardLen = $this->getPlainNumber();
        $cardLen = strlen($cardLen);
        foreach (self::$vendorValidators as $vendor => $validator) {
            if (!in_array($cardLen, $validator['length'])) {
                continue;
            }
            foreach ($validator['prefix'] as $prefix) {
                if (substr($iin, 0, strlen($prefix)) == $prefix) {
                    $this->vendor = $vendor;
                    break(2);
                }
            }
        }
        $iin = null;
        if ($this->getVendor() == 0) {
            throw new FinancialCardNumberException("Vendor not found");
        }
    }

    /**
     * Get IIN number according to ISO 7812
     * @return string
     */
    private function getIIN()
    {
        $number = $this->getPlainNumber();
        $iin = substr($number, 0, 8);
        $number = null;
        return $iin;
    }
}