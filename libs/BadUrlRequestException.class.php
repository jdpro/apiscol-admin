<?php
class BadUrlRequestException extends Exception
{
	public function __construct($url) {
		parent::__construct('The web service does not answer on this url : '.$url, 0);
	}

	public function __toString() {
		return __CLASS__ . ": {$this->message}\n";
	}
}
?>