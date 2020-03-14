<?php

namespace App\Exceptions;

use Exception;

class UndefinedCompanyException extends Exception
{

    /**
     * Create a new authentication exception.
     *
     * @param  string  $message
     * @param  array  $guards
     * @return void
     */
    public function __construct($message = 'Undefined company.')
    {
        parent::__construct($message);
    }

}