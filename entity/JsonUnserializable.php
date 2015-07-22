<?php

namespace com\appstions\yourChallenge\entity;

interface JsonUnserializable {
	/**
	 * 
	 * @param array $array
	 */
	public function jsonUnserialize(array $array);
}