<?php
/**
 * Created by Edward Rodriguez
 * Date: 7/9/16
 * Time: 2:01 PM
 * 
 */

namespace App\Managers\MasterMind\Contracts;

interface PegBoardInterface
{
	/**
	 * @param array $guess An array of Pegs
	 *
	 * @return mixed
	 */
	public function guess(array $guess);

	/**
	 * @return boolean
	 */
	public function canPlay();

	/**
	 * @return int
	 */
	public function attemptsLeft();

	/**
	 * @return int
	 */
	public function getAttempts();

	/**
	 * @return boolean
	 */
	public function isValidPlay();

	/**
	 *
	 * @return mixed
	 */
	public function resetGame();

}

