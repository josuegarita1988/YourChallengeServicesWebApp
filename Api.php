<?php
use com\appstions\yourChallenge\service\Rest;

require_once("service/Rest.php");
require_once("service/PlayerService.php");

class Api {

	const NAMESPACE_ROOT = "com\appstions\yourChallenge\service";
	const REQUEST_NOT_FOUND = "petición no encontrada";
	
	private $class;
	private $method;
	private $arguments;
	
	public function __construct() {
	}
	
	private function getClass($className){
		
		$respuesta = NULL;

		$estado = array(
				'player' => self::NAMESPACE_ROOT . '\PlayerService');
		
		if(array_key_exists($className, $estado)){
			$respuesta = ($estado[$className]) ? $estado[$className] : NULL;
		}
		
		return $respuesta;
	}
	
	private function instanceClass($class){
		$newInstance = NULL;
		try {
			
			$className = $this->getClass($class);
			if($className != NULL && ((int) class_exists($className, true) > 0)){
					
				$newInstance = new $className();
			}
			
			
		} catch (\Exception $e) {
			$rest = new Rest();
			$rest->processErrorResponse('', Rest::STATUS_METHOD_NOT_ALLOWED, $e->getMessage());
		}
		return $newInstance;
	}
	
	public function processRequest() {
		
		if (isset($_REQUEST['url'])) {
			//si por ejemplo pasamos explode('/','////controller///method////args///') el resultado es un array con elem vacios;
			//Array ( [0] => [1] => [2] => [3] => [4] => controller [5] => [6] => [7] => method [8] => [9] => [10] => [11] => args [12] => [13] => [14] => )
			$url = explode('/', trim($_REQUEST['url']));
			//con array_filter() filtramos elementos de un array pasando función callback, que es opcional.
			//si no le pasamos función callback, los elementos false o vacios del array serán borrados
			//por lo tanto la entre la anterior función (explode) y esta eliminamos los '/' sobrantes de la URL
			$url = array_filter($url);
			
			$this->class = $this->instanceClass(strtolower(array_shift($url)));
			 
			$this->method = strtolower(array_shift($url));
			
			$this->arguments = $url;
			$func = $this->method;
			
			if ( $this->class != NULL && ((int) method_exists($this->class, $func) > 0)) {
				
				if (count($this->arguments) > 0) {
					call_user_func_array(array($this->class, $this->method), $this->arguments);
				} else {//si no lo llamamos sin argumentos, al metodo del controlador
					call_user_func(array($this->class, $this->method));
				}
				
			} else {
				$rest = new Rest();
				$rest->processErrorResponse('', Rest::STATUS_METHOD_NOT_ALLOWED, self::REQUEST_NOT_FOUND);
			}
		}
		$rest = new Rest();
		$rest->processErrorResponse('', Rest::STATUS_METHOD_NOT_ALLOWED, self::REQUEST_NOT_FOUND);
	}
	
}

$api = new Api();
$api->processRequest();
