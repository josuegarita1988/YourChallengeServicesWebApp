<?php
namespace com\appstions\yourChallenge\entity;

require_once 'entity/JsonUnserializable.php';

class Position implements \JsonSerializable, JsonUnserializable {
	
	private $idPosition;
	private $description;
	
	public function __construct($idPosition = NULL, $description = NULL){
		$this->idPosition = $idPosition;
		$this->description = $description;
	}
	
	public function getIdPosition() {
		return $this->idPosition;
	}
	public function setIdPosition($idPosition) {
		$this->idPosition = $idPosition;
	}
	public function getDescription() {
		return $this->description;
	}
	public function setDescription($description) {
		$this->description = $description;
	}
	
	
	/* (non-PHPdoc)
	 * @see JsonSerializable::jsonSerialize()
	 */
	public function jsonSerialize() {
		return [
				'idPosition' => $this->idPosition,
				'description' => $this->description
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