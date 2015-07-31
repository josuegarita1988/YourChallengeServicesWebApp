<?php
namespace com\appstions\yourChallenge\dataAccess;

require_once('DAOException.php');

class DAO {
	
	const SERVER_DB = "OPENSHIFT_MYSQL_DB_HOST";
	const SERVER_PORT = "OPENSHIFT_MYSQL_DB_PORT";
	const USER_DB = "OPENSHIFT_MYSQL_DB_USERNAME";
	const PASSWORD_DB = "OPENSHIFT_MYSQL_DB_PASSWORD";
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
			
			//mysql://$OPENSHIFT_MYSQL_DB_HOST:$OPENSHIFT_MYSQL_DB_PORT/
			$dsn = 'mysql:dbname=' . self::NAME_DB . ';host=' . getenv(self::SERVER_DB).':'.getenv(self::SERVER_PORT);
			$userName = getenv(self::USER_DB);
			$password = getenv(self::PASSWORD_DB);
		}else{
			$dsn = 'mysql:dbname=' . self::NAME_DB_LOCAL . ';host=' . self::SERVER_DB_LOCAL;
			$userName = self::USER_DB_LOCAL;
			$password = self::PASSWORD_DB_LOCAL;
		}
			
	
		try {
			$this->connection = new \PDO($dsn, $userName, $password);
		} catch (\PDOException $e) {
			echo $e->getMessage();
			throw  new \Exception($e->getMessage(), $e->getCode());
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

