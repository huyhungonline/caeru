<?php

namespace App\Exceptions;

use Exception;

class UnauthenticatedCompanyException extends Exception
{

    /**
     * Create a new authentication exception.
     *
     * @param  string  $message
     * @param  array  $guards
     * @return void
     */
    public function __construct($message = 'Unauthenticated company.')
    {
        parent::__construct($message);
    }

}