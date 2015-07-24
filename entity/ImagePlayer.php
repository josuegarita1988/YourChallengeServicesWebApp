<?php
namespace com\appstions\yourChallenge\entity;

require_once 'entity/JsonUnserializable.php';

class ImagePlayer implements \JsonSerializable, JsonUnserializable {
	
	private $idImagePlayer;
	private $logo;
	private $description;
	private $extension;
	
	public function __construct($idImagePlayer = 0, $logo = NULL, $description = NULL, $extension = NULL){
		$this->idImagePlayer = $idImagePlayer;
		$this->logo = $logo;
		$this->description = $description;
		$this->extension = $extension;
	}
	
	public function getIdImagePlayer() {
		return $this->idImagePlayer;
	}
	public function setIdImagePlayer($idImagePlayer) {
		$this->idImagePlayer = $idImagePlayer;
	}
	public function getLogo() {
		return $this->logo;
	}
	public function setLogo($logo) {
		$this->logo = $logo;
	}
	public function getDescription() {
		return $this->description;
	}
	public function setDescription($description) {
		$this->description = $description;
	}
	public function getExtension() {
		return $this->extension;
	}
	public function setExtension($extension) {
		$this->extension = $extension;
	}
	
	/* (non-PHPdoc)
	 * @see JsonSerializable::jsonSerialize()
	 */
	public function jsonSerialize() {
		
		return [
				'idImagePlayer' => $this->idImagePlayer,
				'logo' => $this->logo,
				'description' => $this->description,
				'extension' => $this->extension
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