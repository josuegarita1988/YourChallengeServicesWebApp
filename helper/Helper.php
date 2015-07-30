<?php
namespace com\appstions\yourChallenge\helper;


final class Helper{
	
	//Constante para la semilla del password a la hora de registrar un usuario 
	const GENERAL_SEED = "APPS_YOUR_CHALLENGE";
	//Cosntante para el valor m�nimo 
	const MIN_VALUE = 1;
	//Constante para el valor m�ximo
	const MAX_VALUE = 9999999;
	
	/**
	 * Encripta el password del ususario 
	 */
	public static function cryptUserPassword($password){
		$encryptedPass = crypt($password, self::GENERAL_SEED);
		
		return $encryptedPass;
	}
	
	/**
	 * Crea la semilla inicial base para la creaci�n de los token de la sesion
	 */
	public static function generateSeed(){
		$seed = self::GENERAL_SEED.rand(self::MIN_VALUE, self::MAX_VALUE);
		
		return $seed;
	}
	
	/**
	 * Crea el token a la hora de llevar a cabo el registro del usuario en la aplicaci�n 
	 */
	public static function  generateToken($idSeed){
		return null;
	}
}
