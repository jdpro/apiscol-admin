<?php
class AuthorizationFailureException extends Exception
{
	// Redéfinissez l'exception ainsi le message n'est pas facultatif
	public function __construct($message) {

		// traitement personnalisé que vous voulez réaliser ...

		// assurez-vous que tout a été assigné proprement
		parent::__construct($message, 0);
	}

	// chaîne personnalisée représentant l'objet
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}
?>