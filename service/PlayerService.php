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
	
	
	private function devolverError($id) {
		$errores = array(
				STATUS_METHOD_NOT_ALLOWED => "petición no aceptada",
				STATUS_NO_CONTENT => "petición sin contenido",
				STATUS_INTERNAL_SERVER_ERROR => 'Hubo un error en el sistema',
				'pass' => "email o password incorrectos",
				'borrando' => "error borrando usuario",
				'actualizando' => "error actualizando nombre de usuario",
				'email' => "error buscando usuario por email",
				'creando' => "error creando usuario",
				'existe' => "usuario ya existe",
				'nonExist' => "el jugador no existe",
				'datosRequeridos' => "faltan datos",
				'updateError' => "Hubo un error a la hora de actualizar los datos del usuario"
		);
		return $errores[$id];
	}
	
	/* (non-PHPdoc)
	 * @see \Service\IPlayerService::login()
	 */
	public function login() {
		
		$respuesta = NULL;
		
		try {
			$this->checkPostRequest();
			
			$header = $this->datosPeticion['header'];
			$body = $this->datosPeticion['body'];
			
			$countryCode = $header['country'];
			
			$player = new Player();
			
			if ($player->jsonUnserialize($body)) {
				//el constructor del padre ya se encarga de sanear los datos de entrada				
			
				$data = $this->playerDAO->login($player);
				
				if($data != NULL){
					$respuesta = $this->createResponse($countryCode, $data);					
				} else {
					$respuesta = $this->createErrorResponse($countryCode, STATUS_ERROR, $this->devolverError('pass'));
				}
				
			}else{
				throw new DAOException($this->devolverError('datosRequeridos'), STATUS_BAD_REQUEST);
			}
		} catch (DAOException $e) {
			$respuesta = $this->createErrorResponse('', STATUS_ERROR, $e->getMessage());
		} catch (Exception $e) {
			$respuesta = $this->createErrorResponse('', STATUS_ERROR, $this->devolverError(STATUS_INTERNAL_SERVER_ERROR));
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
				
			$header = $this->datosPeticion['header'];
			$body = $this->datosPeticion['body'];
				
			$countryCode = $header['country'];
			
			$players = $this->playerDAO->getUsers();
			
			$respuesta = $this->createResponse($countryCode, $players);
			
				
		} catch (DAOException $e) {
			$respuesta = $this->createErrorResponse('CRI', $e->getCode(), $e->getMessage());
			$this->mostrarRespuesta($respuesta, STATUS_OK);
		} catch (Exception $e) {
			$respuesta = $this->createErrorResponse('CRI', '500', $this->devolverError(STATUS_INTERNAL_SERVER_ERROR));
			$this->mostrarRespuesta($respuesta, STATUS_OK);
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
			
			$header = $this->datosPeticion['header'];
			$body = $this->datosPeticion['body'];
			
			$countryCode = $header['country'];
			
			$player = new Player();
			
			if ($player->jsonUnserialize($body)) {

				$data = $this->playerDAO->getPlayer($player->getIdPlayer());
				
				if($data != NULL){
					$respuesta = $this->createResponse($countryCode, $data);
				} else {
					$respuesta = $this->createErrorResponse($countryCode, STATUS_ERROR, $this->devolverError('nonExist'));
				}
				
			}
	
		} catch (DAOException $e) {
			$respuesta = $this->createErrorResponse('', STATUS_ERROR, $e->getMessage());
		} catch (Exception $e) {
			$respuesta = $this->createErrorResponse('', STATUS_ERROR, $this->devolverError(STATUS_INTERNAL_SERVER_ERROR));
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
				
			$header = $this->datosPeticion['header'];
			$body = $this->datosPeticion['body'];
				
			$countryCode = $header['country'];
			
			$player = new Player();
				
			if ($player->jsonUnserialize($body)) {
				//el constructor del padre ya se encarga de sanear los datos de entrada
					
				$updated = $this->playerDAO->updateUser($player);
				
		
				if($updated == TRUE){
					
					$respuesta = $this->createResponse($countryCode, $updated);
				} else {
					$respuesta = $this->createErrorResponse($countryCode, STATUS_ERROR, $this->devolverError('updateError'));
				}
		
			}else{
				throw new DAOException($this->devolverError('datosRequeridos'), STATUS_BAD_REQUEST);
			}
		} catch (DAOException $e) {
			$respuesta = $this->createErrorResponse('', STATUS_ERROR, $e->getMessage());
		} catch (Exception $e) {
			$respuesta = $this->createErrorResponse('', STATUS_ERROR, $this->devolverError(STATUS_INTERNAL_SERVER_ERROR));
		}
		
		$this->mostrarRespuesta($respuesta, STATUS_OK);
		
	}

}