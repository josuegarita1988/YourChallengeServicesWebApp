<?php
namespace com\appstions\yourChallenge\entity;

require_once 'entity/JsonUnserializable.php';
require_once 'entity/Position.php';
require_once 'entity/Region.php';
require_once 'entity/GameCategory.php';
require_once 'entity/ImagePlayer.php';

class Player implements \JsonSerializable, JsonUnserializable {
	
	private $idPlayer;
	private $userName;
	private $email;
	private $password;
	private $free;
	private $position;
	private $region;
	private $gameCategory;
	private $imagePlayer;
	
	public function __construct(){
		$this->position = new Position();
		$this->region = new Region();
		$this->gameCategory = new GameCategory();
		$this->imagePlayer = new ImagePlayer();
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
	public function getRegion() {
		return $this->region;
	}
	public function setRegion($region) {
		$this->region = $region;
	}
	public function getGameCategory() {
		return $this->gameCategory;
	}
	public function setGameCategory($gameCategory) {
		$this->gameCategory = $gameCategory;
	}
	public function getImagePlayer() {
		return $this->imagePlayer;
	}
	public function setImagePlayer($imagePlayer) {
		$this->imagePlayer = $imagePlayer;
	}
			
	public function jsonSerialize(){
		
		return [
				'idPlayer' => $this->idPlayer,
				'userName' => $this->userName,
				'email' => $this->email,
				'free' => $this->free,
				'position' => $this->position,
				'region' => $this->region,
				'gameCategory' => $this->gameCategory,
				'imagePlayer' => $this->imagePlayer
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