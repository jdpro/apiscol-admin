<?php
class ResourcesDetailDisplayController extends AbstractResourcesDetailController {


	public function __construct($mainController, $model, $prefix) {
		parent::__construct($mainController, $model, $prefix);
	}
	public function completeScripts() {
		$this->mainController->addScript('apiscol');
		$this->mainController->addScript('layout');
		$this->mainController->addScript('rcarousel');
		$this->mainController->addScript('form');
		$this->mainController->addCss('rcarousel');
		$this->mainController->addScript('init_resources_detail_view');
		$this->mainController->addScript('init');
	}


	public function defineView() {
		$this->view = new ResourcesDetailDisplayView($this->model, $this->prefix, $this->mainController);
	}
	public function processSyncRequest() {
		$this->registerMetadataId();
		
		if($this->mainController->isInError())
			return;
		try {
			$this->model->acquireThumbsSuggestions();
			$this->processThumbChoice();
			$this->processCustomThumb();
			$this->model->acquireThumbsSuggestions();
		} catch (HttpRequestException $e) {
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Impossible de récupérer les suggestions de miniatures", $e->getMessage());
		}

	}
	public function processAsyncRequest() {
		$this->registerMetadataId();
		if($this->mainController->isInError())
			return;
		try {
			$this->model->acquireThumbsSuggestions();
			$this->processCustomThumb();
			$this->processThumbChoice();
			$this->model->acquireThumbsSuggestions();
			$this->defineView();
			echo $this->getView()->getThumbsChoiceArea();
		} catch (HttpRequestException $e) {
			echo "Impossible de récupérer les suggestions de miniatures :".$e->getMessage();
		}

	}
	public function processThumbChoice($secondTry=false) {
		if (isset (Security :: $_CLEAN['choose-thumb'])) {
			//TODO cHECK authorizations
			try {
				$this->model->assignThumbToMetadata(Security :: $_CLEAN['choose-thumb']);
			} catch (HttpRequestException $e) {
				$intro='Il y a eu un problème... (erreur '.$e->getCode().')';
				switch ($e->getCode()) {					
					case "412":
						$intro='Quelqu\'un a modifé cette ressource en même temps que vous (erreur '.$e->getCode().')';
						break;
					case "500":
						$intro='Quelquechose s\'est mal passé de notre côté (erreur '.$e->getCode().')';
						break;
				}
				$this->mainController->setInError(true);
				$this->mainController->setErrorMessage($intro, $e->getContent());
			}
		}
	}
	public function processCustomThumb($secondTry=false)  {
		if (isset(Security::$_CLEAN['custom-thumb'])) {
			$error = Security :: $_CLEAN['custom-thumb']['error'];
			if($error==1 || $error==2)
			{
				$this->mainController->setInError(true);
				$this->mainController->setErrorMessage("L'envoi de l'image a échoué en raison de sa taille excessive.");
			} else if($error==3)
			{
				$this->mainController->setInError(true);
				$this->mainController->setErrorMessage("L'envoi de l'image a échoué en raison d'un incident de transfert.");
			} else if($error==4)
			{
				$this->mainController->setInError(true);
				$this->mainController->setErrorMessage("Vous avez envoyé une image de taille nulle.");
			} else {
				try {
					$this->model->assignCustomThumbToMetadata(Security::$_CLEAN['custom-thumb']);
				} catch (HttpRequestException $e) {
					$intro='Il y a eu un problème... (erreur '.$e->getCode().')';
					switch ($e->getCode()) {						
						case "412":
							$intro='Quelqu\'un a modifé cette ressource en même temps que vous (erreur '.$e->getCode().')';
							break;
						case "500":
							$intro='Quelquechose s\'est mal passé de notre côté (erreur '.$e->getCode().')';
							break;
					}
					$this->mainController->setInError(true);
					$this->mainController->setErrorMessage($intro, $e->getContent());
				}
			}

		}
	}

}
?>