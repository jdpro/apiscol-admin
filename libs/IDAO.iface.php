<?php

interface IDAO {

	public function getDocument();
	public function getDocumentAsString();
	public function getXPath();
	public function getEtag();
	public function getId();
	public function build();
	public function isBuilt();

}


?>