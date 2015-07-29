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
		
		try{
			$this->playerDAO = new PlayerDAO();
		} catch (\Exception $e) {
			$this->processErrorResponse('', Rest::STATUS_ERROR, $e->getMessage());
		}
	}
	
	
	/* (non-PHPdoc)
	 * @see \Service\IPlayerService::login()
	 */
	public function login() {
		
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
					$this->processSuccessResponse($countryCode, $data);					
				} else {
					$this->processErrorResponse($countryCode, Rest::STATUS_ERROR, IPlayerService::NOT_AUTHENTICATED);
				}
				
			}else{
				throw new DAOException(IPlayerService::REQUIRED, Rest::STATUS_BAD_REQUEST);
			}
		} catch (DAOException $e) {
			$this->processErrorResponse('', Rest::STATUS_ERROR, $e->getMessage());
		} catch (Exception $e) {
			$this->processErrorResponse('', Rest::STATUS_ERROR, Rest::SERVER_ERROR);
		}
		
	}

	/* (non-PHPdoc)
	 * @see \Service\IPlayerService::players()
	 */
	public function players() {
		
		try {
				
			$this->checkPostRequest();
				
			$header = $this->datosPeticion[Rest::HEADER];
			
			$countryCode = $header[Rest::COUNTRY];
			
			$players = $this->playerDAO->getUsers();
			
			$this->processSuccessResponse($countryCode, $players);
			
				
		} catch (DAOException $e) {
			$this->processErrorResponse('', $e->getCode(), $e->getMessage());
		} catch (Exception $e) {
			$this->processErrorResponse('', Rest::STATUS_ERROR, Rest::SERVER_ERROR);
		}
		
	}

	/* (non-PHPdoc)
	 * @see \com\appstions\yourChallenge\service\IPlayerService::getUser()
	 */
	public function getUser() {
		
		try {
			
			$this->checkPostRequest();
			
			$header = $this->datosPeticion[Rest::HEADER];
			$body = $this->datosPeticion[Rest::BODY];
			
			$countryCode = $header[Rest::COUNTRY];
			
			$player = new Player();
			
			if ($player->jsonUnserialize($body)) {

				$data = $this->playerDAO->getPlayer($player->getIdPlayer());
				
				if($data != NULL){
					$this->processSuccessResponse($countryCode, $data);
				} else {
					$this->processErrorResponse($countryCode, Rest::STATUS_ERROR, IPlayerService::PLAYER_NOT_EXIST);
				}
				
			}
	
		} catch (DAOException $e) {
			$this->processErrorResponse('', Rest::STATUS_ERROR, $e->getMessage());
		} catch (Exception $e) {
			$this->processErrorResponse('', Rest::STATUS_ERROR, Rest::SERVER_ERROR);
		}
	}

	/* (non-PHPdoc)
	 * @see \com\appstions\yourChallenge\service\IPlayerService::updateUser()
	 */
	public function updateUser() {
		
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
					$this->processSuccessResponse($countryCode, $updated);
				} else {
					$this->processErrorResponse($countryCode, Rest::STATUS_ERROR, IPlayerService::PLAYER_NOT_UPDATED);
				}
		
			}else{
				throw new DAOException(IPlayerService::REQUIRED, Rest::STATUS_BAD_REQUEST);
			}
		} catch (DAOException $e) {
			$this->processErrorResponse('', Rest::STATUS_ERROR, $e->getMessage());
		} catch (Exception $e) {
			$this->processErrorResponse('', Rest::STATUS_ERROR, Rest::SERVER_ERROR);
		}
		
	}
	
	/*public function crearUsuario() {
		if ($_SERVER['REQUEST_METHOD'] != "POST") {
			$this->mostrarRespuesta($this->convertirJson($this->devolverError(1)), 405);
		}
		if (isset($this->datosPeticion['nombre'], $this->datosPeticion['email'], $this->datosPeticion['pwd'])) {
			$nombre = $this->datosPeticion['nombre'];
			$pwd = $this->datosPeticion['pwd'];
			$email = $this->datosPeticion['email'];
			if (!$this->existeUsuario($email)) {
				$query = $this->getConnection()->prepare("INSERT into usuario (username,email,password) VALUES (:nombre, :email, :pwd)");
				$query->bindValue(":nombre", $nombre);
				$query->bindValue(":email", $email);
				$query->bindValue(":pwd", sha1($pwd));
				$query->execute();
				if ($query->rowCount() == 1) {
					$id = $this->getConnection()->lastInsertId();
					$respuesta['estado'] = 'correcto';
					$respuesta['msg'] = 'usuario creado correctamente';
					$respuesta['usuario']['id'] = $id;
					$respuesta['usuario']['nombre'] = $nombre;
					$respuesta['usuario']['email'] = $email;
					$this->mostrarRespuesta($this->convertirJson($respuesta), STATUS_OK);
				}
				else
					$this->mostrarRespuesta($this->convertirJson($this->devolverError(7)), 400);
			}
			else
				$this->mostrarRespuesta($this->convertirJson($this->devolverError(8)), 400);
		} else {
			$this->mostrarRespuesta($this->convertirJson($this->devolverError(7)), 400);
		}
	}*/

}
