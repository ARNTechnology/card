<?php


namespace ARNTech\Card\Interfaces;

use ARNTech\Card\Model\CardNumber;
use ARNTech\Card\Model\Expiration;

interface CardInterface extends \JsonSerializable
{
    /**
     * Get the card number
     *
     * @return CardNumber
     */
    public function getNumber();

    /**
     * Get expiration date of the card
     *
     * @return Expiration
     */
    public function getExpiration();
}