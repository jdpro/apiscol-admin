<?php
class HomeIndexView implements IView {
	
	public function __construct($modele){
		$this->render='';
		$this->modele=$modele;
	
	}
	public function build() {
		$this->addContent();
	}
	function addContent() {
	    $this->render .= HTMLLoader::load('home-index');
	}
	function toHTML() {
		return $this->render;
	}
}
?>