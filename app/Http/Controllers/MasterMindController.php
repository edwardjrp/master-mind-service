<?php

namespace App\Http\Controllers;

use App\Managers\MasterMind\Common\PegColor;
use App\Managers\MasterMind\Common\PegTrait;
use App\Managers\MasterMind\Factory\PegFactory;
use App\Managers\MasterMind\Peg;
use App\Managers\MasterMind\PegBoard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class MasterMindController extends Controller
{

	use PegTrait;

	/**
	 * @var Request
	 */
	private $request;
	/**
	 * @var PegBoard
	 */
	private $pegBoard;

	public function __construct(Request $request, PegBoard $pegBoard)
    {
	    $this->request = $request;
	    $this->pegBoard = $pegBoard;
    }

	public function appName()
	{
		return $this->returnOk([
			'app' => 'Master mind game'
		]);
	}

	public function showSecretCode()
	{
		$currentCode = $this->pegBoard->secretCodeToArray();
		return $this->returnOk(['secret_code' => $currentCode]);
	}

	public function playUserGuess()
	{
		$userPlays = $this->request->input('guess', []);

		$validate = $this->validateInput($userPlays);
		if ( $validate instanceof Response) {
			return $validate;
		}

		$guessPegs = [];
		foreach($userPlays as $p) {
			$guessPegs[] = PegFactory::makePeg($p);
		}
		$playResult = $this->pegBoard->guess($guessPegs);

		return $this->returnOk($playResult);

	}

	public function restartGame()
	{
		if ($this->pegBoard->resetGame()) {
			return $this->returnOk(['message' => 'Game was resetted successfully']);
		}

		return $this->returnInternalError(['message' => 'Something went wrong restarting the game']);
	}


	private function validateInput( $input )
	{

		if (!is_array($input)) {
			return $this->returnBadRequest([
				'errors' => [
					'field'   => 'guess',
					'message' => 'Invalid guess parameters'
				]
			]);
		}

		if (count($input) < 5) {
			return $this->returnBadRequest([
				'errors' => [
					'field'   => 'guess',
					'message' => 'Must send array of 5 color coded guesses'
				]
			]);
		}


		foreach($input as $peg) {
			if ( !Arr::has( $this->pegColors(), strtoupper($peg) ) ) {
				return $this->returnBadRequest(
					[
						'errors' => [
							'field'   => 'guess',
							'message' => 'Valid guess elements are: red, orange, yellow, green, blue, purple, white, black'
						]
					]
				);

			}
		}

		return true;
	}

	/**
	 * Standarize HTTP 200 OK response
	 *
	 * @param array $content
	 *
	 * @return Response
	 */
	private function returnOk( array $content )
	{
		return response(json_encode(['data' => $content]), Response::HTTP_OK, [
			'Content-Type' => 'application/json'
		]);
	}

	private function returnBadRequest( array $content )
	{
		return response(json_encode(['data' => $content]), Response::HTTP_BAD_REQUEST, [
			'Content-Type' => 'application/json'
		]);
	}

	private function returnInternalError( array $content )
	{
		return response(json_encode(['data' => $content]), Response::HTTP_SERVICE_UNAVAILABLE, [
			'Content-Type' => 'application/json'
		]);
	}

}
