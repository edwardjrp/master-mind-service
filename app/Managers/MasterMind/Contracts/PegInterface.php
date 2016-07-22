<?php
/**
 * Created by Edward Rodriguez
 * Date: 7/9/16
 * Time: 12:43 PM
 * 
 */

namespace App\Managers\MasterMind\Contracts;

interface PegInterface 
{
	/**
	 * Validates if the construction of a Peg has appropiate properties
	 *
	 * @return boolean
	 */
	public function validatePeg();

	public function isValidColor();

	public function isValidControlColor();
}

