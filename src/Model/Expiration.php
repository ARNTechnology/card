<?php


namespace ARNTech\Card\Model;

use \DateTime;

class Expiration extends DateTime
{

    public function __toString()
    {
        return $this->format('m-Y');
    }
}