<?php
  $SERVER_DB = "OPENSHIFT_MYSQL_DB_HOST";
	$SERVER_PORT = "OPENSHIFT_MYSQL_DB_PORT";
	$USER_DB = "OPENSHIFT_MYSQL_DB_USERNAME";
	$PASSWORD_DB = "OPENSHIFT_MYSQL_DB_PASSWORD";
	$NAME_DB = "yourchallenge";
	
	 $connection = NULL;
	
	
		$dsn = '';
		$userName = '';
		$password = '';
	
	
			$dsn = 'mysql:dbname=' . self::NAME_DB . ';host=' . getenv($SERVER_DB).':'.getenv($SERVER_PORT);
			$userName = getenv($USER_DB);
			$password = getenv($PASSWORD_DB);
			
	
		try {
			$this->connection = new \PDO($dsn, $userName, $password);
			echo 'ok';
		} catch (\PDOException $e) {
			echo ($e->getMessage());
		}
