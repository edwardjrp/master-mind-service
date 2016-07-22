<?php
/**
 * Created by Edward Rodriguez
 * Date: 7/9/16
 * Time: 1:30 PM
 * 
 */

namespace App\Managers\MasterMind;

use App\Managers\MasterMind\Common\PegColor;
use App\Managers\MasterMind\Common\PegTrait;
use App\Managers\MasterMind\Contracts\PegBoardInterface;
use App\Managers\MasterMind\Exceptions\InvalidGuessInput;
use App\Managers\MasterMind\Exceptions\InvalidGuessPeg;
use App\Managers\MasterMind\Factory\PegFactory;
use Illuminate\Support\Collection;

class PegBoard implements PegBoardInterface
{

	use PegTrait;

	/**
	 * @var Collection
	 */
	private $secretCode;

	private $attempts = 0;

	private $guessResponse = [];

	private $guessPlay;

	public function __construct()
	{
		$this->secretCode = new Collection();
		$this->createSecret();
		$this->guessResponse = new Collection();
		$this->guessPlay = new Collection();
		$this->attempts = $this->getAttemptsContent();
	}

	public function guess( array $guess )
	{
		$play = $this->validatePlay($guess);
		if (!$play->isValidPlay()) {
			throw new InvalidGuessInput;
		}

		//Game ends
		if ($this->getAttempts() >= $this->playLimit()) {
			return [
				'winner'        => $this->isWinner(),
				'attemptsLeft'  => $this->attemptsLeft(),
				'message'       => 'Game is over. To start a new one call restart-game endpoint with DELETE HTTTP method'
			];
		}

		if ($this->canPlay()) {
			$this->compareGuess();
		}

		return [
			'winner'        => $this->isWinner(),
			'attemptsLeft'  => $this->attemptsLeft(),
			'guessResponse' => $this->guessResponseToArray(),
			'guessPlayed'   => $this->guessPlayedToArray()
		];
	}

	public function isWinner()
	{
		if ($this->guessResponse->count() != $this->guessPlay->count()) {
			return false;
		}

		$totalRed = $this->guessResponse->filter(function($p) {
			return ($p->getColor() == PegColor::RED);
		});

		return ($totalRed->count() == $this->guessPlay->count());
	}

	private function compareGuess()
	{
		$this->guessPlay->each(function($peg) {
			//Check peg are same color and same position
			if ($this->getSecretCode()->contains($peg)) {
				return $this->guessResponse->push(PegFactory::makeControlPeg(PegColor::RED));
			}

			//Check which pegs match color and have different position
			$this->getSecretCode()->each(function($s) use ($peg) {
				if (($peg->getColor() == $s->getColor()) && ( $peg->getPosition() != $s->getPosition() )) {
					return $this->guessResponse->push(PegFactory::makeControlPeg(PegColor::WHITE));
				}
			});

		});

		$this->computeGuess();
	}

	public function resetGame()
	{
		try {
			$this->attempts = 0;
			$this->secretCode = '';
			$this->saveAttemptCount();
			$this->saveSecretContent();
		} catch (\Exception $e) {
			return false;
		}

		return true;
	}

	private function validatePlay(array $guess) {
		$position = 1;
		foreach($guess as $g) {
			if (!($g instanceof Peg)) {
				throw new InvalidGuessPeg;
			}
			if($g->isControlPeg()) {
				throw new InvalidGuessPeg('Control pegs are for response purposes only');
			}

			/**
			 * If no position is specify when the peg object was created, it will auto-assign positions in
			 * the order the peg comes in the array
			 */
			if ($g->getPosition() == 0) {
				$g->setPosition($position);
			}

			$this->guessPlay->push($g);
			$position++;
		}
		return $this;
	}

	public function isValidPlay()
	{
		return ($this->guessPlay->count() == $this->guessLimit());
	}

	/**
	 * @return Collection
	 */
	public function getSecretCode()
	{
		return $this->secretCode;
	}

	public function canPlay()
	{
		return ($this->getAttempts() <= $this->playLimit());
	}

	public function attemptsLeft()
	{
		$attempts = ($this->playLimit() - $this->getAttempts());
		if ($attempts < 0 ) {
			$attempts = 0;
		}
		return $attempts;
	}

	/**
	 * @return int
	 */
	public function getAttempts()
	{
		return $this->attempts;
	}

	private function computeGuess()
	{
		$count = $this->getAttemptsContent();
		$count++;
		$this->attempts = $count;
		$this->saveAttemptCount();
	}

	private function saveAttemptCount()
	{
		file_put_contents($this->getAttemptsFilename(), serialize($this->getAttempts()));
	}

	private function createSecret()
	{
		$existingSecret = $this->getSecretContent();
		if (($existingSecret == '') || $existingSecret == false) {
			if ( $this->getSecretCode()->isEmpty() ) {
				$this->addPegs();
			}
		} else {
			$this->secretCode = $existingSecret;
		}
	}

	private function getAttemptsContent()
	{
		if (file_exists($this->getAttemptsFilename())) {
			return unserialize(file_get_contents($this->getAttemptsFilename()));
		}
		file_put_contents($this->getAttemptsFilename(), serialize(''));

		return $this->getAttemptsContent();
	}

	private function getAttemptsFilename()
	{
		return  __DIR__."/attempts_count.txt";
	}

	/**
	 * Get file with secret generated pegs if it doesnt exists it creates an empty one
	 *
	 * @return mixed
	 */
	private function getSecretContent()
	{
		if (file_exists($this->secretFilename())) {
			return unserialize(file_get_contents($this->secretFilename()));
		}
		file_put_contents($this->secretFilename(), serialize(''));

		return $this->getSecretContent();
	}

	private function saveSecretContent()
	{
		file_put_contents($this->secretFilename(), serialize($this->getSecretCode()));
	}

	private function secretFilename()
	{
		return  __DIR__."/secret_pegs.txt";
	}

	private function addPegs()
	{
		for($i = 0; $i <= 4; $i++ ) {
			$this->getSecretCode()->push(PegFactory::makePeg($this->pickRandomColor(), ($i + 1)));
		}
		$this->saveSecretContent();
	}

	/**
	 * @return Collection
	 */
	public function getGuessResponse()
	{
		return $this->guessResponse;
	}

	public function guessResponseToArray()
	{
		$guesses = $this->getGuessResponse()->all();
		$toArray = [];
		foreach($guesses as $g) {
			$toArray[] = $g->toArray();
		}

		return $toArray;
	}

	public function guessPlayedToArray()
	{
		$guesses = $this->guessPlay->all();
		$toArray = [];
		foreach($guesses as $g) {
			$toArray[] = $g->toArray();
		}

		return $toArray;
	}

	public function secretCodeToArray()
	{
		$secretCode = $this->getSecretCode()->all();
		$toArray = [];
		foreach($secretCode as $p) {
			$toArray[] = $p->toArray();
		}

		return $toArray;
	}
}

