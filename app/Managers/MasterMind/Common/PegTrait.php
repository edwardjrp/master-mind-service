<?php
/**
 * Created by PhpStorm.
 * User: Edward Rodriguez
 * Date: 7/9/16
 * Time: 12:45 PM
 */

namespace App\Managers\MasterMind\Common;

trait PegTrait
{

	public function guessLimit()
	{
		return 5;
	}

	public function playLimit()
	{
		return 12;
	}

	public function pickRandomColor()
	{
		return array_rand($this->pegColors());
	}

	public function pegColors()
	{
		return [
			PegColor::RED    => PegColor::RED,
			PegColor::ORANGE => PegColor::ORANGE,
			PegColor::YELLOW => PegColor::YELLOW,
			PegColor::GREEN  => PegColor::GREEN,
			PegColor::BLUE   => PegColor::BLUE,
			PegColor::PURPLE => PegColor::PURPLE,
			PegColor::WHITE  => PegColor::WHITE,
			PegColor::BLACK  => PegColor::BLACK
		];
	}

	public function pegControlColors()
	{
		return [
			PegColor::RED   => PegColor::RED,
			PegColor::WHITE => PegColor::WHITE
		];
	}

}