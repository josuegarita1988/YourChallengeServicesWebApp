<?php
namespace com\appstions\yourChallenge\service;

interface IPlayerService {
	
	const METHOD_NOT_FOUND = "Peticion no encontrada";
	const NO_CONTENT = "Peticion sin contenido";
	const NOT_AUTHENTICATED = "Email o password incorrectos";
	const REQUIRED = "Faltan datos";
	const PLAYER_NOT_UPDATED = "Hubo un error a la hora de actualizar los datos del usuario";
	const PLAYER_NOT_CREATED = "Hubo un error a la hora de crear el usuario";
	const PLAYER_NOT_DELETED = "Hubo un error a la hora de borrar el usuario";
	const PLAYER_EXIST = "EL usuario ya existe";
	const PLAYER_NOT_EXIST = "El jugador no existe";
	
	/**
	 * Verifica si las credenciales del usuario
	 */
	public function login();
	/**
	 * Obtiene todos los jugadores
	 */
	public function players();
	/**
	 * Obtiene un jugador
	 */
	public function getUser();
	/**
	 * Modifica los datos del usuario
	 */
	public function updateUser();
}