<?php
namespace com\appstions\yourChallenge\dataAccess;

use com\appstions\yourChallenge\entity\Player;

require_once 'dataAccess/DAO.php';

class PlayerDAO extends DAO {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function login(Player $player){
		$query = $this->getConnection()->prepare(
				"SELECT id_player, username, email
							 	 FROM tch_player
							 	 WHERE (username=:username OR email=:email) AND pass=:pwd ");
			
		$query->bindValue(":username", $player->getUserName());
		$query->bindValue(":email", $player->getEmail());
		$query->bindValue(":pwd", /*sha1*/($player->getPassword()));
		$query->execute();
		
		$usuario = NULL;
		if ($fila = $query->fetch(\PDO::FETCH_ASSOC)) {
			
			$usuario = new Player();
			
			$usuario->setIdPlayer($fila['id_player']);
			$usuario->setUserName($fila['username']);
			$usuario->setEmail($fila['email']);
				
		}
		return $usuario;
	}
	
	public function getUsers(){
		
		$players = array();		
		
		$query = $this->getConnection()->query("SELECT id_player, username, email, free FROM tch_player");
		$rows = $query->fetchAll(\PDO::FETCH_ASSOC);
		
		foreach ($rows as $row){
			$player = new Player();
			$player->setIdPlayer($row['id_player']);
			$player->setUserName($row['username']);
			$player->setEmail($row['email']);
			$player->setFree($row['free']);
			
			array_push($players, $player);
		}
		
		return $players;	
	}
	
	public function getPlayer($idPlayer){
	
		$player = NULL;
	
		$query = $this->getConnection()->prepare("SELECT id_player, username, email FROM tch_player WHERE id_player =:id_player");
		$query->bindValue(":id_player", $idPlayer);
		$query->execute();
		
		if ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
			
			$player = new Player();
			$player->setIdPlayer($row['id_player']);
			$player->setUserName($row['username']);
			$player->setEmail($row['email']);
		}
	
		return $player;
	}
}