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
	public function getPlayer($idPlayer);
}