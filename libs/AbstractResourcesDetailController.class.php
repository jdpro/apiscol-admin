<?php
abstract class AbstractResourcesDetailController extends AbstractResourcesController {

	protected $asyncMode;




public function acquireScolomfr() {
		try {
			$this->model->acquireScolomfrMetadata();
		} catch (HttpRequestException $e) {
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Problèmes de connexion à ApiScol pour obtenir les métadonnées scoLOMfr.", $e->getContent());
		} catch (BadUrlRequestException $e) {
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Problèmes de connexion à ApiScol pour obtenir les métadonnées scoLOMfr.", $e->getMessage());
		}
	}
	public function acquireManifest() {
		try {
			$this->model->acquireIMSLDManifest();
		} catch (HttpRequestException $e) {
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Problèmes de connexion à ApiScol pour obtenir le manifeste IMS LD", $e->getContent());
		} catch (BadUrlRequestException $e) {
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Problèmes de connexion à ApiScol pour obtenir le manifeste IMS LD", $e->getMessage());
		}
	}
	protected function acquireSnippet() {
		try {
			$this->model->acquireSnippet();
		} catch (HttpRequestException $e) {
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Problèmes de connexion à ApiScol pour obtenir les snippets de code.", $e->getContent());
		} catch (BadUrlRequestException $e) {
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Problèmes de connexion à ApiScol pour obtenir les snippets de code.", $e->getMessage());
		}
	}


	public function getView() {
		return $this->view;
	}
}
?>