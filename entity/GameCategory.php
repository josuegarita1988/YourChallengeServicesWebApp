<?php
namespace com\appstions\yourChallenge\entity;

require_once 'entity/JsonUnserializable.php';

class GameCategory implements \JsonSerializable, JsonUnserializable {
	
	private $idGameCategory;
	private $description;
	
	public function __construct($idGameCategory = 0, $description = NULL){
		$this->idGameCategory = $idGameCategory;
		$this->description = $description;
	}
	
	public function getIdGameCategory() {
		return $this->idGameCategory;
	}
	public function setIdGameCategory($idGameCategory) {
		$this->idGameCategory = $idGameCategory;
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
				'idGameCategory' => $this->idGameCategory,
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