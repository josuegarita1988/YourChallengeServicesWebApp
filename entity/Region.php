<?php
namespace com\appstions\yourChallenge\entity;

require_once 'entity/JsonUnserializable.php';

class Region implements \JsonSerializable, JsonUnserializable {
	private $idRegion;
	private $latitude;
	private $longitude;
	
	public function __construct($idRegion = 0, $latitude = NULL, $longitude = NULL){
		$this->idRegion = $idRegion;
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}
	
	public function getIdRegion() {
		return $this->idRegion;
	}
	public function setIdRegion($idRegion) {
		$this->idRegion = $idRegion;
	}
	public function getLatitude() {
		return $this->latitude;
	}
	public function setLatitude($latitude) {
		$this->latitude = $latitude;
	}
	public function getLongitude() {
		return $this->longitude;
	}
	public function setLongitude($longitude) {
		$this->longitude = $longitude;
	}
	
	/* (non-PHPdoc)
	 * @see JsonSerializable::jsonSerialize()
	 * Realiza la conversión del arreglo al tipo JSON
	 */
	public function jsonSerialize() {
		
		return [
				'idRegion' => $this->idRegion,
				'latitude' => $this->latitude,
				'longitude' => $this->longitude
		];
	}

	/* (non-PHPdoc)
	 * @see \com\appstions\yourChallenge\entity\JsonUnserializable::jsonUnserialize()
	 * Realiza la conversión del arreglo al tipo objeto 
	 */
	public function jsonUnserialize(array $array) {
		
		$isValid = true;
		
		foreach ($array as $key => $value) {
		   //Del core de php 
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