<?php
namespace com\appstions\yourChallenge\service;

use com\appstions\yourChallenge\dataAccess\PlayerDAO;
use com\appstions\yourChallenge\dataAccess\DAOException;
use com\appstions\yourChallenge\entity\Player;

require_once 'service/IPlayerService.php';
require_once 'entity/Player.php';
require_once 'dataAccess/PlayerDAO.php';

class PlayerService extends Rest implements IPlayerService{
	
	private $playerDAO;
		
	public function __construct(){
		parent::__construct();
		$this->playerDAO = new PlayerDAO();
	}
	
	
	/* (non-PHPdoc)
	 * @see \Service\IPlayerService::login()
	 */
	public function login() {
		
		$respuesta = NULL;
		
		try {
			$this->checkPostRequest();
			
			$header = $this->datosPeticion[Rest::HEADER];
			$body = $this->datosPeticion[Rest::BODY];
			
			$countryCode = $header[Rest::COUNTRY];
			
			$player = new Player();
			
			if ($player->jsonUnserialize($body)) {
				//el constructor del padre ya se encarga de sanear los datos de entrada				
			
				$data = $this->playerDAO->login($player);
				
				if($data != NULL){
					$respuesta = $this->createResponse($countryCode, $data);					
				} else {
					$respuesta = $this->createErrorResponse($countryCode, STATUS_ERROR, IPlayerService::NOT_AUTHENTICATED);
				}
				
			}else{
				throw new DAOException(IPlayerService::REQUIRED, STATUS_BAD_REQUEST);
			}
		} catch (DAOException $e) {
			$respuesta = $this->createErrorResponse('', STATUS_ERROR, $e->getMessage());
		} catch (Exception $e) {
			$respuesta = $this->createErrorResponse('', STATUS_ERROR, SERVER_ERROR);
		}
		
		$this->mostrarRespuesta($respuesta, STATUS_OK);
		
	}

	/* (non-PHPdoc)
	 * @see \Service\IPlayerService::players()
	 */
	public function players() {
		
		$respuesta = NULL;
		
		try {
				
			$this->checkPostRequest();
				
			$header = $this->datosPeticion[Rest::HEADER];
			
			$countryCode = $header[Rest::COUNTRY];
			
			$players = $this->playerDAO->getUsers();
			
			$respuesta = $this->createResponse($countryCode, $players);
			
				
		} catch (DAOException $e) {
			$respuesta = $this->createErrorResponse('', $e->getCode(), $e->getMessage());
		} catch (Exception $e) {
			$respuesta = $this->createErrorResponse('', STATUS_ERROR, SERVER_ERROR);
		}
		
		$this->mostrarRespuesta($respuesta, STATUS_OK);
		
	}

	/* (non-PHPdoc)
	 * @see \com\appstions\yourChallenge\service\IPlayerService::getUser()
	 */
	public function getUser() {
		
		$respuesta = NULL;
		
		try {
			
			$this->checkPostRequest();
			
			$header = $this->datosPeticion[Rest::HEADER];
			$body = $this->datosPeticion[Rest::BODY];
			
			$countryCode = $header[Rest::COUNTRY];
			
			$player = new Player();
			
			if ($player->jsonUnserialize($body)) {

				$data = $this->playerDAO->getPlayer($player->getIdPlayer());
				
				if($data != NULL){
					$respuesta = $this->createResponse($countryCode, $data);
				} else {
					$respuesta = $this->createErrorResponse($countryCode, STATUS_ERROR, IPlayerService::PLAYER_NOT_EXIST);
				}
				
			}
	
		} catch (DAOException $e) {
			$respuesta = $this->createErrorResponse('', STATUS_ERROR, $e->getMessage());
		} catch (Exception $e) {
			$respuesta = $this->createErrorResponse('', STATUS_ERROR, SERVER_ERROR);
		}
		
		$this->mostrarRespuesta($respuesta, STATUS_OK);
	}

	/* (non-PHPdoc)
	 * @see \com\appstions\yourChallenge\service\IPlayerService::updateUser()
	 */
	public function updateUser() {
		
		$respuesta = NULL;
		
		try {
			
			$this->checkPostRequest();
				
			$header = $this->datosPeticion[Rest::HEADER];
			$body = $this->datosPeticion[Rest::BODY];
			
			$countryCode = $header[Rest::COUNTRY];
			
			$player = new Player();
				
			if ($player->jsonUnserialize($body)) {
				//el constructor del padre ya se encarga de sanear los datos de entrada
					
				$updated = $this->playerDAO->updateUser($player);
				
		
				if($updated == TRUE){
					
					$respuesta = $this->createResponse($countryCode, $updated);
				} else {
					$respuesta = $this->createErrorResponse($countryCode, STATUS_ERROR, IPlayerService::PLAYER_NOT_UPDATED);
				}
		
			}else{
				throw new DAOException(IPlayerService::REQUIRED, STATUS_BAD_REQUEST);
			}
		} catch (DAOException $e) {
			$respuesta = $this->createErrorResponse('', STATUS_ERROR, $e->getMessage());
		} catch (Exception $e) {
			$respuesta = $this->createErrorResponse('', STATUS_ERROR, SERVER_ERROR);
		}
		
		$this->mostrarRespuesta($respuesta, STATUS_OK);
		
	}

}