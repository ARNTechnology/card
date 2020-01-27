<?php


namespace ARNTech\Card\Model\Virtual;

use ARNTech\Card\Model\Card as BaseCard;
use ARNTech\Card\Model\CardNumber;
use \DateTime;
use \InvalidArgumentException;

class Card extends BaseCard
{
    /**
     * @var Cvv2
     */
    private $cvv2;

    /**
     * Card constructor.
     * @param CardNumber|string $number
     * @param DateTime|string $expiration - ex: new Datetime('2020-01'), 012020, 0120, 01-20, 01/20, 01:20, 01/2020, 2020/01, 2020:01, 2020-01
     * @param string|int|Cvv2 $cvv2
     */
    public function __construct($number, $expiration, $cvv2)
    {
        parent::__construct($number, $expiration);
        $this->setCvv2($cvv2);
    }

    /**
     * @param string|int|Cvv2 $cvv2
     * @return $this
     * @throws InvalidArgumentException
     */
    private function setCvv2($cvv2)
    {
        $this->cvv2 = ($cvv2 instanceof Cvv2) ? $cvv2 : new Cvv2($cvv2);

        if (!$this->cvv2->isValidForCardType($this->getNumber()->getType())) {
            throw new InvalidArgumentException("Card number and CVV2 do not match.");
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

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), ['cvv2' => strval($this->getCvv2())]);
    }
}