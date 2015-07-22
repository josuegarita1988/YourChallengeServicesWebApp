<?php

namespace com\appstions\yourChallenge\service;

define('DEFAULT_STATUS', 200);
define('DEFAULT_TYPE', "application/json");
define('SUCCESS', 'success');
define('ERROR', 'error');
define('STATUS_OK', 200);
define('STATUS_ERROR', 0);
define('STATUS_CREATED', 201);
define('STATUS_ACCEPTED', 202);
define('STATUS_NO_CONTENT', 204);
define('STATUS_MOVED_PERMANENTLY', 301);
define('STATUS_FOUND', 302);
define('STATUS_SEE_OTHER', 303);
define('STATUS_NOT_MODIFIED', 304);
define('STATUS_BAD_REQUEST', 400);
define('STATUS_UNAUTHORIZED', 401);
define('STATUS_FORBIDEN', 403);
define('STATUS_NOT_FOUND', 404);
define('STATUS_METHOD_NOT_ALLOWED', 405);
define('STATUS_INTERNAL_SERVER_ERROR', 500);

class Rest{
	public $tipo = DEFAULT_TYPE;
	public $datosPeticion = array();
	private $_codEstado = DEFAULT_STATUS;
	
	 
	
	public function __construct(){
		$this->tratarEntrada();
	}
	
	public function mostrarRespuesta($data, $estado){
		$this->_codEstado = ($estado)? $estado : DEFAULT_STATUS;
		$this->setCabecera();
		echo $data;
		
		exit;
	}
	
	private function setCabecera(){
		header("HTTP/1.1 " . $this->_codEstado . " " . $this->getCodEstado());
		header("Content-Type:" . $this->tipo . ';charset=utf-8');
	}
	
	private function limpiarEntrada($data){
		$entrada = array();
		if(is_array($data)){
			foreach ($data as $key => $value) {
				
				$entrada[$key] = $this->limpiarEntrada($value);
				
			}
		} else {
			
			if (get_magic_quotes_gpc()){
				//Quitamos las barras de un string con comillas escapadas
				//Aunque actualmente se desaconseja su uso, muchos servidores tienen activada la extensión magic_quotes_gpc.
				//Cuando esta extensión está activada, PHP añade automáticamente caracteres de escape (\) delante de las comillas 
				//que se escriban en un campo de formulario.
				$data = trim(stripslashes($data));
			}
			//eliminamos etiquetas html y php 
			$data = strip_tags($data);
			//Convertimos todos los caracteres aplicables a entidades HTML
			$data = htmlentities($data);
			$entrada = trim($data);
		}
		return $entrada;
	}
	
	private function tratarEntrada(){
		$method = $_SERVER['REQUEST_METHOD'];
		//var_dump($_SERVER);// 
		$contentType = NULL;
		$isJsonData = FALSE;
		
		if(isset($_SERVER['CONTENT_TYPE'])){
			$contentType = $_SERVER['CONTENT_TYPE'];
		}
		
		if (strpos('application/json', $contentType) !== false) {	
			$isJsonData = TRUE;
		}
		
		switch ($method	) {
			case "GET":
				$this->datosPeticion = $this->limpiarEntrada($_GET);
			break;
			case "POST":
				
				if ($isJsonData){
					
					$json = file_get_contents("php://input");
					$data = strip_tags($json);
						
					$jsonDecoded = json_decode(trim($data), TRUE);
						
					$this->datosPeticion = $this->limpiarEntrada($jsonDecoded);
					
				}else {
					$this->datosPeticion = $this->limpiarEntrada($_POST);
				} 
					
			break;
			case "DELETE":
			case "PUT":
				//php no tiene un método propiamente dicho para leer una petición PUT o DELETE por lo que se usa un "truco":
				//leer el stream de entrada file_get_contents("php://input") que transfiere un fichero a una cadena.
				//Con ello obtenemos una cadena de pares clave valor de variables (variable1=dato1&variable2=data2...)
				//que evidentemente tendremos que transformarla a un array asociativo.
				//Con parse_str meteremos la cadena en un array donde cada par de elementos es un componente del array.
				parse_str(file_get_contents("php://input"), $this->datosPeticion);
				$this->datosPeticion = $this->limpiarEntrada($this->datosPeticion);
			break;
			default:
				$this->response('', STATUS_NOT_FOUND);
			break;
		}
	}
	
	private function getCodEstado() {
		$estado = array(
				STATUS_OK => 'OK',
				STATUS_ERROR => 'ERROR',
				STATUS_CREATED => 'Created',
				STATUS_ACCEPTED => 'Accepted',
				STATUS_NO_CONTENT => 'No Content',
				STATUS_MOVED_PERMANENTLY => 'Moved Permanently',
				STATUS_FOUND => 'Found',
				STATUS_SEE_OTHER => 'See Other',
				STATUS_NOT_MODIFIED => 'Not Modified',
				STATUS_BAD_REQUEST => 'Bad Request',
				STATUS_UNAUTHORIZED => 'Unauthorized',
				STATUS_FORBIDEN => 'Forbidden',
				STATUS_NOT_FOUND => 'Not Found',
				STATUS_METHOD_NOT_ALLOWED => 'Method Not Allowed',
				STATUS_INTERNAL_SERVER_ERROR => 'Internal Server Error');
		$respuesta = ($estado[$this->_codEstado]) ? $estado[$this->_codEstado] : $estado[STATUS_INTERNAL_SERVER_ERROR];
		
		return $respuesta;
	}
	public function convertirJson($data) {
		return json_encode($data);
	}
	
	protected function createResponse($status, $message, $data = ''){
		$response = array();
		$response['status'] = $status;
		$response['message'] = $message;
		$response['data'] = $data;
		return $this->convertirJson($response);
	}
}