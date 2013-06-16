<?php
class MetadataWithoutContentException extends Exception
{
	public function __construct($message) {
		parent::__construct($message, 0);
	}

	// chaîne personnalisée représentant l'objet
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}

}
?>