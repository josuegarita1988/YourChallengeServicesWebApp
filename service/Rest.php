<?php
namespace com\appstions\yourChallenge\service;

use com\appstions\yourChallenge\dataAccess\DAOException;

require_once 'dataAccess/DAOException.php';

;
class Rest{

	const DEFAULT_STATUS =  200;
	const STATUS_BAD_REQUEST =  400;
	const STATUS_UNAUTHORIZED =  401;
	const STATUS_FORBIDEN =  403;
	const STATUS_NOT_FOUND =  404;
	const STATUS_METHOD_NOT_ALLOWED =  405;
	const STATUS_OK = 200;
	const STATUS_ERROR = 500;
	
	const APPLICATION_JSON = "application/json";
	const DEFAULT_TYPE =  "application/json";
	
	const SUCCESS =  'success';
	const FAIL =  'fail';
	
	const HEADER = "header";
	const BODY = "body";
	const COUNTRY = "country";
	const STATUS = "status";
	const ERROR_CODE = "errorCode";
	const ERROR_MESSAGE = "message";
	
	const SERVER_ERROR =  "Hubo un error en el sistema";
	
	public $tipo = self::DEFAULT_TYPE;
	public $datosPeticion = array();
	private $_codEstado = self::DEFAULT_STATUS;
	
	 
	
	public function __construct(){
		$this->tratarEntrada();
	}
	
	public function mostrarRespuesta($data, $estado){
		$this->_codEstado = ($estado)? $estado : self::DEFAULT_STATUS;
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
		
		$contentType = NULL;
		$isJsonData = FALSE;
		
		if(isset($_SERVER['CONTENT_TYPE'])){
			$contentType = $_SERVER['CONTENT_TYPE'];
		}
		
		if (strpos(self::APPLICATION_JSON, $contentType) !== false) {	
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
				$this->response('', self::STATUS_NOT_FOUND);
			break;
		}
	}
	
	private function getCodEstado() {
		$estado = array(
				self::STATUS_OK => 'OK',
				self::STATUS_ERROR => 'ERROR',
				self::STATUS_BAD_REQUEST => 'Bad Request',
				self::STATUS_UNAUTHORIZED => 'Unauthorized',
				self::STATUS_FORBIDEN => 'Forbidden',
				self::STATUS_NOT_FOUND => 'Not Found',
				self::STATUS_METHOD_NOT_ALLOWED => 'Method Not Allowed',
				self::STATUS_ERROR => 'Internal Server Error');
		$respuesta = ($estado[$this->_codEstado]) ? $estado[$this->_codEstado] : $estado[STATUS_INTERNAL_SERVER_ERROR];
		
		return $respuesta;
	}
	
	private function convertirJson($data) {
		return json_encode($data);
	}
	
	/**
	 * Crea la respuesta en estado exitoso
	 * @param string $country Codigo de Pais
	 * @param string $data Datos que se quieren retornar
	 */
	private function createSuccessResponse($country, $data = ''){
		$response = array();
		
		$response[self::HEADER][self::COUNTRY] = $country;
		$response[self::HEADER][self::STATUS] = self::SUCCESS;
		$response[self::HEADER][self::ERROR_CODE] = '';
		$response[self::HEADER][self::ERROR_MESSAGE] = '';
		
		$response[self::BODY] = $data;
		return $this->convertirJson($response);
	}
	
	/**
	 * Crea la respuesta para los errores
	 * @param string $country Codigo de Pais
	 * @param string $errorCode Codigo del error
	 * @param string $message Mensaje del error
	 */
	private function createErrorResponse($country, $errorCode = '', $message){
		$response = array();
	
		$response[self::HEADER][self::COUNTRY] = $country;
		$response[self::HEADER][self::STATUS] = self::FAIL;
		$response[self::HEADER][self::ERROR_CODE] = $errorCode;
		$response[self::HEADER][self::ERROR_MESSAGE] = $message;
	
		$response[self::BODY] = '';
		return $this->convertirJson($response);
	}
	
	/**
	 * Revisa si los parametros estan bien tipados y si viene por POST
	 * @throws DAOException
	 */
	protected function checkPostRequest(){
		if ($_SERVER['REQUEST_METHOD'] != "POST") {
			throw new DAOException('petición no aceptada', self::STATUS_METHOD_NOT_ALLOWED);
		}
		
		if(!isset($this->datosPeticion)){
			throw new DAOException('faltan los parametros', self::STATUS_BAD_REQUEST);
		}
			
		if(!isset($this->datosPeticion[self::HEADER])){
			throw new DAOException('falta el header', self::STATUS_BAD_REQUEST);
		}
			
		if(!isset($this->datosPeticion[self::BODY])){
			throw new DAOException('faltan el body', self::STATUS_BAD_REQUEST);
		}
		
		if (is_array($this->datosPeticion[self::HEADER]) == FALSE || is_array($this->datosPeticion[self::BODY]) == FALSE) {
			throw new DAOException('faltan datos', self::STATUS_BAD_REQUEST);
		}
		
		if(!isset($this->datosPeticion[self::HEADER][self::COUNTRY])){
			throw new DAOException('faltan el codigo de pais', self::STATUS_BAD_REQUEST);
		}
	}
	/**
	 * Procesa el mensaje de exito
	 * @param string $countryCode
	 * @param object $data
	 */
	public function processSuccessResponse($countryCode, $data){
		$respuesta = $this->createSuccessResponse($countryCode, $data);
		$this->mostrarRespuesta($respuesta, self::STATUS_OK);
	}
	/**
	 * Procesa el mensaje de error
	 * @param string $country
	 * @param string $errorCode
	 * @param string $message
	 */
	public function processErrorResponse($country, $errorCode, $message){
		
		$respuesta = $this->createErrorResponse($country, $errorCode, $message);
		$this->mostrarRespuesta($respuesta, self::STATUS_OK);
	}
}