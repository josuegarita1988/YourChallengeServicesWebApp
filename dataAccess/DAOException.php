<?php
namespace com\appstions\yourChallenge\dataAccess;

class DAOException extends \Exception{
	public function __construct($message = null, $code = null){
		parent::__construct($message, $code);
	}
	
}