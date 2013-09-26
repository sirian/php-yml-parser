<?php

namespace Sirian\YMLParser\Exception;

use Exception;

class UnsupportedOfferTypeException extends YMLException
{
    public function __construct($message = "Unsupported offer type", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
