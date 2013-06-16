<?php
class ConnexionFailureException extends Exception
{
	public function __construct($message, $headers) {
		$message.=" Detail : ".$this->getDisplayableError($headers);
		parent::__construct($message, 0);
	}

	// chaîne personnalisée représentant l'objet
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}

	private function getDisplayableError($headers) {
		$errorArray=error_get_last();
		$error ='';
		$error.='Type '.$errorArray["type"];
		$error.=' Message '.$errorArray["message"];
		$error.=' File '.$errorArray["file"];
		$error.=' Line '.$errorArray["line"];
		$error.=' Headers :';
		$error.='  [Code '.$headers[0].'],';
		$error.='  [Etag '.$headers[2].'],';
		$error.='  [Content-Type '.$headers[3].'],';
		return $error;
	}
}
?>