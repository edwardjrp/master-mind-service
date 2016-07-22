<?php
/**
 * Created by Edward Rodriguez
 * Date: 7/9/16
 * Time: 12:31 PM
 * 
 */

namespace App\Managers\MasterMind;

use App\Managers\MasterMind\Common\PegTrait;
use App\Managers\MasterMind\Contracts\PegInterface;
use App\Managers\MasterMind\Exceptions\InvalidPegColorException;
use Illuminate\Support\Arr;

class Peg implements PegInterface
{
	use PegTrait;

	/**
	 * @var
	 */
	private $color;
	/**
	 * @var int
	 */
	private $position;
	/**
	 * @var bool
	 */
	private $controlPeg;

	public function __construct($color, $isControlPeg = false, $position = 0)
	{
		$this->color = $color;
		$this->position = $position;
		$this->controlPeg = $isControlPeg;
		$this->validatePeg();
	}

	public function validatePeg()
	{
		if ($this->isControlPeg()) {
			if (!$this->isValidControlColor()) {
				throw new InvalidPegColorException;
			}
		}

		if (!$this->isValidColor()) {
			throw new InvalidPegColorException;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function isValidControlColor()
	{
		return Arr::has($this->pegControlColors(), $this->getColor());
	}

	/**
	 * @return bool
	 */
	public function isValidColor()
	{
		return Arr::has($this->pegColors(), $this->getColor());
	}


	/**
	 * @return mixed
	 */
	public function getColor()
	{
		return $this->color;
	}

	/**
	 * @return int
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * @return boolean
	 */
	public function isControlPeg()
	{
		return $this->controlPeg;
	}

	/**
	 * @param int $position
	 */
	public function setPosition( $position )
	{
		$this->position = $position;
	}

	public function toArray()
	{
		if ($this->isControlPeg()) {
			return [
				'PEG' => [
					'color' => $this->getColor()
				]
			];
		}

		return [
			'PEG' => [
				'color'    => $this->getColor(),
				'position' => $this->getPosition()
			]
		];

	}
}

