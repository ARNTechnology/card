<?php


namespace ARNTech\Card\Model;


use ARNTech\Card\Exception\ExpirationException;
use ARNTech\Card\Interfaces\CardInterface;
use ARNTech\Utils\Helper\DateTimeHelper;
use \DateTime;
use \InvalidArgumentException;

class Card implements CardInterface
{
    /**
     * @var CardNumber
     */
    private $cardNumber;

    /**
     * @var DateTime
     */
    private $expiration;

    /**
     * Card constructor.
     * @param string|CardNumber $cardNumber
     * @param string|int|DateTime|Expiration $expiration
     */
    public function __construct($cardNumber, $expiration)
    {
        $this->setNumber($cardNumber);
        $this->setExpiration($expiration);
    }

    /**
     * @param CardNumber|string $number
     * @return $this
     * @throws InvalidArgumentException
     */
    protected function setNumber($number)
    {
        if (!is_string($number) && !$number instanceof CardNumber) {
            throw new InvalidArgumentException("Invalid card type provided.");
        }
        $this->cardNumber = ($number instanceof CardNumber) ? $number : new CardNumber($number);
        return $this;
    }

    /**
     * @param Expiration|DateTime|string|int $expiration
     * @return $this
     */
    protected function setExpiration($expiration)
    {
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
        if ($expiration <= new DateTime()) {
            throw new ExpirationException("Card is expired.");
        }
        $this->expiration = $expiration;
        return $this;
    }

    /**
     * @return CardNumber
     */
    public function getNumber()
    {
        return $this->cardNumber;
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