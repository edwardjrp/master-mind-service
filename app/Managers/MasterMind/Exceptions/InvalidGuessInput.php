<?php
/**
 * Created by Edward Rodriguez
 * Date: 7/9/16
 * Time: 2:39 PM
 * 
 */

namespace App\Managers\MasterMind\Exceptions;

class InvalidGuessInput extends \Exception
{
	public function __construct($message = 'Invalid guess play. Amount of pegs in guess must be 5')
	{
		parent::__construct($message);
	}
}

