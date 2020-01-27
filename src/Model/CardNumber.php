<?php


namespace ARNTech\Card\Model;

use ARNTech\Card\Exception\CardNumberException;
use ARNTech\Utils\Algorithm\Luhn;
use \InvalidArgumentException;

class CardNumber
{
    const TYPE_VISA = 1;
    const TYPE_MASTERCARD = 2;
    const TYPE_AMEX = 3;
    const TYPE_MAESTRO = 4;
    const TYPE_DISCOVER = 5;
    const TYPE_DINERS = 6;
    const TYPE_DINERSUS = 7;
    const TYPE_JCB = 8;
    const TYPE_UNIONPAY = 9;
    const TYPE_LASER = 10;
    const TYPE_SOLO = 11;

    public static $names = [
        self::TYPE_VISA => 'Visa',
        self::TYPE_MASTERCARD => 'Mastercard',
        self::TYPE_AMEX => 'American Express',
        self::TYPE_MAESTRO => 'Maestro',
        self::TYPE_DISCOVER => 'Discover',
        self::TYPE_DINERS => 'Diners Club',
        self::TYPE_DINERSUS => 'Diners Club US',
        self::TYPE_JCB => 'JCB',
        self::TYPE_UNIONPAY => 'UnionPay',
        self::TYPE_LASER => 'Laser',
        self::TYPE_SOLO => 'Solo',
    ];

    private static $validators = [
        self::TYPE_AMEX => [
            'length' => [15],
            'prefix' => ['34', '37']
        ],
        self::TYPE_DINERS => [
            'length' => [14],
            'prefix' => ['300', '301', '302', '303', '304', '305', '36']
        ],
        self::TYPE_DINERSUS => [
            'length' => [16],
            'prefix' => ['54', '55']
        ],
        self::TYPE_DISCOVER => [
            'length' => [16],
            'prefix' => ['6011', '622126', '622127', '622128', '622129', '62213', '62214', '62215', '62216', '62217', '62218', '62219', '6222', '6223', '6224', '6225', '6226', '6227', '6228', '62290', '62291', '622920', '622921', '622922', '622923', '622924', '622925', '644', '645', '646', '647', '648', '649', '65']
        ],
        self::TYPE_JCB => [
            'length' => [16],
            'prefix' => ['3528', '3529', '353', '354', '355', '356', '357', '358']
        ],
        self::TYPE_LASER => [
            'length' => [16, 17, 18, 19],
            'prefix' => ['6304', '6706', '6771', '6709']
        ],
        self::TYPE_MAESTRO => [
            'length' => [12, 13, 14, 15, 16, 17, 18, 19],
            'prefix' => ['5018', '5020', '5038', '6304', '6759', '6761', '6762', '6763', '6764', '6765', '6766']
        ],
        self::TYPE_MASTERCARD => [
            'length' => [16],
            'prefix' => ['51', '52', '53', '54', '55']
        ],
        self::TYPE_SOLO => [
            'length' => [16, 18, 19],
            'prefix' => ['6334', '6767']
        ],
        self::TYPE_UNIONPAY => [
            'length' => [16, 17, 18, 19],
            'prefix' => ['622126', '622127', '622128', '622129', '62213', '62214', '62215', '62216', '62217', '62218', '62219', '6222', '6223', '6224', '6225', '6226', '6227', '6228', '62290', '62291', '622920', '622921', '622922', '622923', '622924', '622925']
        ],
        self::TYPE_VISA => [
            'length' => [16],
            'prefix' => ['4']
        ]
    ];

    /**
     * @var int
     */
    private $cardType;

    /**
     * @var string
     */
    private $number;

    /**
     * CardNumber constructor.
     * @param string $number
     * @throws InvalidArgumentException
     */
    public function __construct($number)
    {
        $this->setNumber($number);
    }

    /**
     * @param string $number
     * @throws InvalidArgumentException
     * @throws CardNumberException
     */
    private function setNumber($number)
    {
        if (!is_string($number)) {
            throw new InvalidArgumentException("Provided number must be string.");
        }
        $number = trim($number);
        $len = strlen($number);
        if ($len < 12 || $len > 19) {
            throw new CardNumberException("Card number has wrong length.");
        }
        if (!$this->isValidLuhn($number)) {
            throw new CardNumberException("Card format is invalid.");
        }
        foreach (self::$validators as $cardType => $validator) {
            if (!in_array($len, $validator['length'])) {
                continue;
            }
            foreach ($validator['prefix'] as $prefix) {
                if (substr($number, 0, strlen($prefix)) == $prefix) {
                    $this->cardType = $cardType;
                    break(2);
                }
            }
        }
        if (empty($this->cardType)) {
            throw new CardNumberException("Card number is invalid. Could not determine type.");
        }
        if (in_array($this->cardType, [self::TYPE_LASER, self::TYPE_SOLO])) {
            throw new CardNumberException(
                sprintf(
                    "Card seems to be of type \"%s\" which is discontinued.",
                    self::$names[$this->cardType]
                )
            );
        }

        $this->number = $number;
    }

    /**
     * @param string $number
     * @return bool
     * @throws InvalidArgumentException
     */
    private function isValidLuhn($number)
    {
        if (!is_string($number) || empty($number)) {
            throw new InvalidArgumentException("Can not validate an empty Card number.");
        }
        return Luhn::validateChecksum(substr($number, 0, -1), substr($number, -1, 1));
    }

    /**
     * @return string
     */
    public function getCardNumber()
    {
        return sprintf(
            "%s%s%s",
            substr($this->getPlainCardNumber(), 0, 4),
            str_repeat('x', strlen($this->getPlainCardNumber()) - 8),
            substr($this->getPlainCardNumber(), -4),
        );
    }

    /**
     * @return string
     */
    public function getPlainCardNumber()
    {
        return $this->number;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->cardType;
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return self::$names[$this->cardType];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getCardNumber();
    }

}