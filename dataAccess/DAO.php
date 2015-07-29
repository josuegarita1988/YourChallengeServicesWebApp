<?php
namespace com\appstions\yourChallenge\dataAccess;

require_once('DAOException.php');

class DAO {
	
	const SERVER_DB = "127.8.45.2";//"yourchallenge-appstions.rhcloud.com";
	const USER_DB = "adminy9lkYNE";
	const PASSWORD_DB = "sz-z8rkJPbLV";
	const NAME_DB = "yourchallenge";
	
	const SERVER_DB_LOCAL = "localhost";
	const USER_DB_LOCAL = "root";
	const PASSWORD_DB_LOCAL = "";
	const NAME_DB_LOCAL = "yourchallenge";
	
	private $connection = NULL;
	
	public function __construct() {
		$this->conectarDB(true);
	}
	
	private function conectarDB($isProduction) {
		$dsn = '';
		$userName = '';
		$password = '';
	
		if ($isProduction == true){
			$dsn = 'mysql:dbname=' . self::NAME_DB . ';host=' . self::SERVER_DB;
			$userName = self::USER_DB;
			$password = self::PASSWORD_DB;
		}else{
			$dsn = 'mysql:dbname=' . self::NAME_DB_LOCAL . ';host=' . self::SERVER_DB_LOCAL;
			$userName = self::USER_DB_LOCAL;
			$password = self::PASSWORD_DB_LOCAL;
		}
			
	
		try {
			$this->connection = new \PDO($dsn, $userName, $password);
		} catch (PDOException $e) {
			echo 'Falló la conexión: ' . $e->getMessage();
		}
	}
	/**
	 * Obtiene la conexión de la base de datos
	 * @return PDO Conexión de la base de datos
	 */
	public function getConnection(){
		return $this->connection;
	}
}
