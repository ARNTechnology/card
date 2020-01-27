<?php
/**
 * Copyright (c) 2020
 * Alexandru Negrilã (alex-codes@arntech.ro) - ARN TECHNOLOGY
 */

namespace ARNTech\Card\Interfaces;


use ARNTech\Card\Model\Financial\CardNumber;
use ARNTech\Card\Model\Financial\Expiration;
use \JsonSerializable;

interface FinancialCardInterface extends JsonSerializable
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