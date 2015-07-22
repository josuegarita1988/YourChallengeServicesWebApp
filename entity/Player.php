<?php
namespace com\appstions\yourChallenge\entity;

require_once 'entity/JsonUnserializable.php';
require_once 'entity/Position.php';
class Player implements \JsonSerializable, JsonUnserializable {
	
	private $idPlayer;
	private $userName;
	private $email;
	private $password;
	private $free;
	private $position;
	
	public function __construct(){
		$this->position = new Position();
	}
	public function setIdPlayer($idPlayer){
		$this->idPlayer = $idPlayer;
	}
	
	public function getIdPlayer(){
		return $this->idPlayer;
	}
	
	public function setUserName($userName){
		$this->userName = $userName;
	}
	
	public function getUserName(){
		return $this->userName;
	}
	
	public function setEmail($email){
		$this->email = $email;
	}
	
	public function getEmail(){
		return $this->email;
	}
	
	public function setPassword($password){
		$this->password = $password;
	}
	
	public function getPassword(){
		return $this->password;
	}
	
	public function setFree($free){
		$this->free = $free;
	}
	
	public function isFree(){
		return $this->free;
	}
	
	public function getPosition() {
		return $this->position;
	}
	
	public function setPosition(Position $position) {
		$this->position = $position;
	}
	
	public function jsonSerialize(){
		
		return [
				'idPlayer' => $this->idPlayer,
				'userName' => $this->userName,
				'email' => $this->email,
				'free' => $this->free,
				'position' => $this->position
		];	
	}
	/* (non-PHPdoc)
	 * @see \com\appstions\yourChallenge\entity\JsonUnserializable::jsonUnserialize()
	 */
	public function jsonUnserialize(array $array) {
		
		$isValid = true;
		
		foreach ($array as $key => $value) {
			if(property_exists($this, $key)){
				if($this->{$key} instanceof JsonUnserializable){
					
					if(is_array($value)){
						call_user_func_array(array($this->{$key}, 'jsonUnserialize'), array($value));	
					}else{
						$isValid = false;
					}
					
				}else{
					$this->{$key} = $value;
				}
			} else {
				$isValid = false;
			}
		}
		
		return $isValid;

	}

}