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
				'datosRequeridos' => "faltan datos"
		);
		return $errores[$id];
	}
	
	/* (non-PHPdoc)
	 * @see \Service\IPlayerService::login()
	 */
	public function login() {
		try {
			if ($_SERVER['REQUEST_METHOD'] != "POST") {
				throw new DAOException($this->devolverError(STATUS_METHOD_NOT_ALLOWED), STATUS_METHOD_NOT_ALLOWED);	
			}
			
			if(!isset($this->datosPeticion)){
				throw new DAOException($this->devolverError('datosRequeridos'), STATUS_BAD_REQUEST);
			}			
			
			$params = $this->datosPeticion;
			
			if (is_array($params) == FALSE) {
				throw new DAOException($this->devolverError('datosRequeridos'), STATUS_BAD_REQUEST);
			}				
			
			$player = new Player();
			
			if ($player->jsonUnserialize($params)) {
				//el constructor del padre ya se encarga de sanear los datos de entrada				
			
				$data = $this->playerDAO->login($player);
				$respuesta = NULL;
				
				if($data != NULL){
					$respuesta = $this->createResponse(SUCCESS, '', $data);					
				} else {
					$respuesta = $this->createResponse(ERROR, $this->devolverError('pass'));
				}
				
				$this->mostrarRespuesta($respuesta, STATUS_OK);
				
			}else{
				throw new DAOException($this->devolverError('datosRequeridos'), STATUS_BAD_REQUEST);
			}
		} catch (DAOException $e) {
			$respuesta = $this->createResponse(ERROR, $e->getMessage()); 
			$this->mostrarRespuesta($respuesta, $e->getCode());
		} catch (Exception $e) {
			$respuesta = $this->createResponse(ERROR, $this->devolverError(STATUS_INTERNAL_SERVER_ERROR)); 
			$this->mostrarRespuesta($respuesta, STATUS_INTERNAL_SERVER_ERROR);
		}
		
	}

	/* (non-PHPdoc)
	 * @see \Service\IPlayerService::players()
	 */
	public function players() {
		
		$respuesta = NULL;
		var_dump($this->datosPeticion);
		try {
				
			if ($_SERVER['REQUEST_METHOD'] != "GET") {
				throw new DAOException($this->devolverError(STATUS_METHOD_NOT_ALLOWED), STATUS_METHOD_NOT_ALLOWED);
			}
				
			$players = $this->playerDAO->getUsers();
			
			$respuesta = $this->createResponse(SUCCESS, '', $players);
			$this->mostrarRespuesta($respuesta, STATUS_OK);
				
		} catch (DAOException $e) {
			$respuesta = $this->createResponse(ERROR, $e->getMessage());
			$this->mostrarRespuesta($respuesta, $e->getCode());
		} catch (Exception $e) {
			$respuesta = $this->createResponse(ERROR, $this->devolverError(STATUS_INTERNAL_SERVER_ERROR));
			$this->mostrarRespuesta($respuesta, STATUS_INTERNAL_SERVER_ERROR);
		}
	}

	/* (non-PHPdoc)
	 * @see \com\appstions\yourChallenge\service\IPlayerService::getPlayer()
	 */
	public function getPlayer($idPlayer) {
		try {
			if ($_SERVER['REQUEST_METHOD'] != "GET") {
				throw new DAOException($this->devolverError(STATUS_METHOD_NOT_ALLOWED), STATUS_METHOD_NOT_ALLOWED);
			}
		
			if(!isset($idPlayer)){
				throw new DAOException($this->devolverError('datosRequeridos'), STATUS_BAD_REQUEST);
			}
						
			$player = new Player();
			$player->setIdPlayer($idPlayer);
			
			//el constructor del padre ya se encarga de sanear los datos de entrada
				
			$data = $this->playerDAO->getPlayer($player->getIdPlayer());
			$respuesta = NULL;
	
			if($data != NULL){
				$respuesta = $this->createResponse(SUCCESS, '', $data);
			} else {
				$respuesta = $this->createResponse(ERROR, $this->devolverError('nonExist'));
			}
	
			$this->mostrarRespuesta($respuesta, STATUS_OK);
	
		} catch (DAOException $e) {
			$respuesta = $this->createResponse(ERROR, $e->getMessage());
			$this->mostrarRespuesta($respuesta, $e->getCode());
		} catch (Exception $e) {
			$respuesta = $this->createResponse(ERROR, $this->devolverError(STATUS_INTERNAL_SERVER_ERROR));
			$this->mostrarRespuesta($respuesta, STATUS_INTERNAL_SERVER_ERROR);
		}
		

	}

}