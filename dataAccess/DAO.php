<?php
namespace com\appstions\yourChallenge\dataAccess;

require_once('DAOException.php');

class DAO {
	
	const SERVER_DB = "sql213.260mb.net";
	const USER_DB = "n260m_16405978";
	const PASSWORD_DB = "satelite";
	const NAME_DB = "n260m_16405978_Team_Challenge_DB";
	
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
