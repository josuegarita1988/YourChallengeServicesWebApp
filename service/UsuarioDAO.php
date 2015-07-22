<?php
require_once("../dataAccess/DAO.php");

class UsuarioDAO extends DAO{
	
	public function __construct(){
		parent::__construct();
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
				'datosRequeridos' => "faltan datos"
		);
		return $errores[$id];
	}
	
	public function usuarios() {
		
		$respuesta = NULL;
		
		try {
			
			if ($_SERVER['REQUEST_METHOD'] != "GET") {
				throw new DAOException($this->devolverError(STATUS_METHOD_NOT_ALLOWED), STATUS_METHOD_NOT_ALLOWED);	
			}
			
			$query = $this->getConnection()->query("SELECT id_player, username, email FROM tch_player");
			$filas = $query->fetchAll(PDO::FETCH_ASSOC);
			$num = count($filas);
			
			if ($num > 0) {
				$respuesta = $this->createResponse(SUCCESS, '', $filas);
				$this->mostrarRespuesta($respuesta, STATUS_OK);
			}else {
				throw new DAOException($this->devolverError(STATUS_NO_CONTENT), STATUS_NO_CONTENT);
			}
			
		} catch (DAOException $e) {
			$respuesta = $this->createResponse(ERROR, $e->getMessage()); 
			$this->mostrarRespuesta($respuesta, $e->getCode());
		} catch (Exception $e) {
			$respuesta = $this->createResponse(ERROR, $this->devolverError(STATUS_INTERNAL_SERVER_ERROR)); 
			$this->mostrarRespuesta($respuesta, STATUS_INTERNAL_SERVER_ERROR);
		}
		
	}
	
	public function login() {
		try {
			if ($_SERVER['REQUEST_METHOD'] != "POST") {
				throw new DAOException($this->devolverError(STATUS_METHOD_NOT_ALLOWED), STATUS_METHOD_NOT_ALLOWED);	
			}
			//echo $this->datosPeticion['data'];
			//json_decode($this->datosPeticion['data']);
			
			if (isset($this->datosPeticion['password']) && (isset($this->datosPeticion['email']) || isset($this->datosPeticion['userName']))) {
				//el constructor del padre ya se encarga de sanear los datos de entrada
				$email = $this->datosPeticion['email'];
				$pwd = $this->datosPeticion['password'];
				$userName = $this->datosPeticion['userName'];
			
				if (!empty($email)) {
					if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
						//consulta preparada ya hace mysqli_real_escape()
						$query = $this->getConnection()->prepare(
								"SELECT id_player, username, email
							 	 FROM tch_player
							 	 WHERE (username=:username OR email=:email) AND pass=:pwd ");
			
						$query->bindValue(":username", $userName);
						$query->bindValue(":email", $email);
						$query->bindValue(":pwd", /*sha1*/($pwd));
						$query->execute();
			
						if ($fila = $query->fetch(PDO::FETCH_ASSOC)) {
							$usuario = array();
							$usuario['idPlayer'] = $fila['id_player'];
							$usuario['userName'] = $fila['username'];
							$usuario['email'] = $fila['email'];
							
							$respuesta = $this->createResponse(SUCCESS, '', $usuario);
							$this->mostrarRespuesta($respuesta, STATUS_OK);
						} else{
							throw new DAOException($this->devolverError('pass'), STATUS_NOT_FOUND);
						}
					}
				}
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
	
	public function actualizarNombre($idUsuario) {
		if ($_SERVER['REQUEST_METHOD'] != "POST") {
			$this->mostrarRespuesta($this->convertirJson($this->devolverError(1)), 405);
		}
		//echo $idUsuario . "<br/>";
		if (isset($this->datosPeticion['nombre'])) {
			$nombre = $this->datosPeticion['nombre'];
			$id = (int) $idUsuario;
			if (!empty($nombre) and $id > 0) {
				$query = $this->getConnection()->prepare("update usuario set nombre=:nombre WHERE id =:id");
				$query->bindValue(":nombre", $nombre);
				$query->bindValue(":id", $id);
				$query->execute();
				$filasActualizadas = $query->rowCount();
				if ($filasActualizadas == 1) {
					$resp = array('estado' => "correcto", "msg" => "nombre de usuario actualizado correctamente.");
					$this->mostrarRespuesta($this->convertirJson($resp), 200);
				} else {
					$this->mostrarRespuesta($this->convertirJson($this->devolverError(5)), 400);
				}
			}
		}
		$this->mostrarRespuesta($this->convertirJson($this->devolverError(5)), 400);
	}
	
	public function borrarUsuario($idUsuario) {
		if ($_SERVER['REQUEST_METHOD'] != "DELETE") {
			$this->mostrarRespuesta($this->convertirJson($this->devolverError(1)), 405);
		}
		$id = (int) $idUsuario;
		if ($id >= 0) {
			$query = $this->getConnection()->prepare("DELETE FROM tch_player WHERE id_player =:id");
			$query->bindValue(":id", $id);
			$query->execute();
			//rowcount para insert, delete. update
			$filasBorradas = $query->rowCount();
			if ($filasBorradas == 1) {
				$resp = array('estado' => "correcto", "msg" => "usuario borrado correctamente.");
				$this->mostrarRespuesta($this->convertirJson($resp), 200);
			} else {
				$this->mostrarRespuesta($this->convertirJson($this->devolverError(4)), 400);
			}
		}
		$this->mostrarRespuesta($this->convertirJson($this->devolverError(4)), 400);
	}
	
	public function existeUsuario($email) {
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$query = $this->getConnection()->prepare("SELECT email FROM tch_player WHERE email = :email");
			$query->bindValue(":email", $email);
			$query->execute();
			if ($query->fetch(PDO::FETCH_ASSOC)) {
				return true;
			}
		}
		else
			return false;
	}
	
	public function crearUsuario() {
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
	}
}