<?php
class HomeController {
	private $view;
	private $mainController;
	private $model;

	private $actions = array (
			"index"
	);

	public function __construct($mainController, $model, $prefix) {
		$this->mainController=$mainController;
		$this->model = $model;

	}

	public function completeScripts() {

		$this->mainController->addScript('init_home');
		$this->mainController->addScript('init');

	}
	public function defineView() {
		switch ($_SESSION['action']) {
			case 'index' :
			default :
				$this->view = new HomeIndexView($this->model);
				break;

		}
	}
	public function processSyncRequest() {
		if (isset (Security :: $_CLEAN['warning'])) {
			$this->mainController->setInError(true);
			//TODO traduire
			$this->mainController->setErrorMessage("Une action non autorisée a été détectée.");
		}

	}
	public function getView() {
		return $this->view;
	}
}
?>