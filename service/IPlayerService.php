<?php
namespace com\appstions\yourChallenge\service;

interface IPlayerService {
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
	public function getUser($idPlayer);
	/**
	 * Modifica los datos del usuario
	 */
	public function updateUser();
}