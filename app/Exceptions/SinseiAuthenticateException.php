<?php

namespace App\Exceptions;

use Exception;

class SinseiAuthenticateException extends Exception
{
	
	/**
	 * Create a new authentication exception.
	 *
	 * @param  string  $message
	 * @param  array  $guards
	 * @return void
	 */
	public function __construct($message = 'Unauthenticated sinsei user.')
	{
		parent::__construct($message);
	}
	
}