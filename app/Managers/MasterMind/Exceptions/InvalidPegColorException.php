<?php
/**
 * Created by Edward Rodriguez
 * Date: 7/9/16
 * Time: 1:00 PM
 * 
 */

namespace App\Managers\MasterMind\Exceptions;

class InvalidPegColorException extends \Exception
{
	public function __construct($message = 'Invalid color for peg')
	{
		parent::__construct($message);
	}
}

