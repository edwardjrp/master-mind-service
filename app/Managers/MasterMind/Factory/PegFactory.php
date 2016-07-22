<?php
/**
 * Created by Edward Rodriguez
 * Date: 7/9/16
 * Time: 1:39 PM
 * 
 */

namespace App\Managers\MasterMind\Factory;

use App\Managers\MasterMind\Peg;

class PegFactory
{
	public static function makePeg($color, $position = 0)
	{
		return new Peg(strtoupper($color), false, $position);
	}

	public static function makeControlPeg($color)
	{
		return new Peg(strtoupper($color), true);
	}
}

