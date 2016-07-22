<?php
/**
 * Created by Edward Rodriguez
 * Date: 7/9/16
 * Time: 2:39 PM
 * 
 */

namespace App\Managers\MasterMind\Exceptions;

class InvalidGuessPeg extends \Exception
{
	public function __construct($message = 'Invalid guess peg.')
	{
		parent::__construct($message);
	}
}

