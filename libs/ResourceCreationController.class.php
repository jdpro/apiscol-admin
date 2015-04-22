<?php
class ResourceCreationController extends AbstractResourcesController {
	public function completeScripts() {
	}
	public function defineView() {
	}
	public function processSyncRequest() {
		if (Security::$_CLEAN ['action'] == "new") {
			try {
				$reponse = $this->model->postVoidMetadata ();
				$this->model->setResultOfMetadataImport ( $reponse );
			} catch ( BadUrlRequestException $e ) {
				$message = "Vous avez envoyé des métadonnées de taille nulle.";
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( "Impossible de se connecter au service", $e->getMessage () );
				return;
			} catch ( HttpRequestException $e ) {
				// TODO définir une vue
				var_dump ( $e );
				$message = "Une requête reste aux web services a échoué.";
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( "Impossible de se connecter au service", $e->getMessage () );
				return;
			}
			if ($this->model->metadataImportIsSucessFull ()) {
				$metadataId = RequestUtils::extractIdFromRestUri ( $this->model->getIdOfImportedMetadata () );
				header ( 'Location: ' . $this->prefix . '/resources/detail/' . $metadataId . '/edit' );
				exit ();
			}
		}
	}
	public function processAsyncRequest() {
		// nothing
	}
	public function getView() {
		return null;
	}
}
?>