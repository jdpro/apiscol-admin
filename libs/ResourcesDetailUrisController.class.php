<?php
class ResourcesDetailUrisController extends AbstractResourcesDetailController {


	public function completeScripts() {
		$this->mainController->addScript('details');
		$this->mainController->addScript('prettify');
		$this->mainController->addScript('init_resources_detail_uris');
		$this->mainController->addScript('init');
	}

	public function defineView() {
		$this->view = new ResourcesDetailUrisView($this->model, $this->prefix, $this->mainController);
	}
	public function processSyncRequest() {
		$this->registerMetadataId();
		if($this->mainController->isInError())
			return;
		try {
			$this->acquireContent();
		} catch (HttpRequestException $e) {
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Impossible de récupérer le contenu (données ou package) de la ressource", $e->getMessage());
		}
		if($this->model->getMetadata()->isPackage())
			$this->acquireManifest();
		try{
			$this->acquireScolomfr();
			$this->acquireSnippet();
		} catch (CorruptedXMLStringException $e) {
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Le fichier de données scolomfr est illisible.", $e->getMessage());
		}
		try {
			$this->model->acquireThumbsSuggestions();
		} catch (HttpRequestException $e) {
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Impossible de récupérer les suggestions de miniatures", $e->getMessage());
		}
	}
	public function processAsyncRequest() {
		//do nothing;
	}
}
?>