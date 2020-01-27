<?php

namespace App\Exception\Yad;

use Exception;

class RefusedAccess extends \Exception
{
    public function __construct()
    {
        parent::__construct('Please refresh page and grant access.');
    }
}