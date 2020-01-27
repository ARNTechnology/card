<?php
/**
 * Copyright (c) 2020
 * Alexandru Negrilã (alex-codes@arntech.ro) - ARN TECHNOLOGY
 */

namespace ARNTech\Card\Model\Financial;

use \DateTime;

class Expiration extends DateTime
{
    public function __toString()
    {
        return $this->format('m-Y');
    }
}