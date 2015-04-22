<?php
class ResourceImportController extends AbstractResourcesController {
	private $actions = array (
			"metadata",
			"package" 
	);
	public function completeScripts() {
		if ($_SESSION ['action'] == 'import') {
			$this->mainController->addScript ( 'layout' );
			$this->mainController->addScript ( 'form' );
			$this->mainController->addScript ( 'cookie' );
			$this->mainController->addScript ( 'init_add_metadata' );
			$this->mainController->addCss ( 'vocabnomen' );
			$this->mainController->addCss ( 'dynatree' );
		}
		$this->mainController->addScript ( 'init' );
	}
	public function defineView() {
		switch ($_SESSION ['action']) {
			case 'import' :
				$this->view = new ResourceImportView ( $this->model, $this->prefix, $this->mainController );
				break;
		}
	}
	public function processSyncRequest() {
		if (isset ( Security::$_CLEAN ['import-metadata'] )) {
			$this->processMetadataImport ();
		}
		if (isset ( Security::$_CLEAN ['url'] ) && isset ( Security::$_CLEAN ['resid'] ) && isset ( Security::$_CLEAN ['etag'] )) {
			$this->registerMetadataId ();
			$this->acquireContent ();
			$this->registerUrl ();
		}
	}
	public function processAsyncRequest() {
		if (isset ( Security::$_CLEAN ['import-metadata'] )) {
			$this->processMetadataImport ();
			$this->defineView ();
			if (! is_null ( $this->getView () ))
				echo $this->getView ()->getImportOfMetadataResultArea ();
			else {
				echo MainController::xmlErrorMessage ( "Erreur inconnue", 0, "Une erreur est survenue" );
			}
		} else if (isset ( Security::$_CLEAN ['url'] ) && isset ( Security::$_CLEAN ['metadata-id'] )) {
			$this->registerMetadataId ();
			if (! $this->mainController->isInError ())
				$this->acquireContent ();
			if (! $this->mainController->isInError ())
				$this->registerUrl ();
			$this->defineView ();
			if (! $this->mainController->isInError ())
				echo $this->getView ()->getUrlRegistrationResultArea ();
			else {
				// TODO écrire du html
				$errors = $this->mainController->getErrorMessage ();
				echo MainController::xmlErrorMessage ( $errors ['private'], 0, $errors ['public'] );
			}
		} else if (isset ( Security::$_CLEAN ['url-parsing-report'] )) {
			$url = RequestUtils::restoreProtocole ( Security::$_CLEAN ['url-parsing-report'] );
			// TODO catch bad url
			echo $this->model->getUrlParsingReport ( $url );
		}
	}
	public function registerUrl($secondTry = false) {
		$url = Security::$_CLEAN ['url'];
		try {
			$response = $this->model->getContent ()->setUrlForRemoteResource ( $url );
			$this->model->setResultOfUrlRegistration ( $response );
		} catch ( HttpRequestException $e ) {
			
			$intro = 'Il y a eu un problème... (erreur ' . $e->getCode () . ')';
			
			$intro = 'Quelquechose s\'est mal passé de notre côté (erreur ' . $e->getCode () . ')';
			
			$this->mainController->setInError ( true );
			$this->mainController->setErrorMessage ( $intro, $e->getContent () );
		}
	}
	public function processMetadataImport($secondTry = false) {
		$reponse = "";
		$locations = array (
				"general.identifier.entry" => null,
				"technical.location" => null 
		);
		$error = Security::$_CLEAN ['import-metadata'] ['error'];
		if ($error == 1 || $error == 2) {
			$message = "L'envoi de métadonnées a échoué en raison de la taille excessive du fichier.";
			$this->mainController->setInError ( true );
			$this->mainController->setErrorMessage ( $message );
		} else if ($error == 3) {
			$message = "L'envoi de métadonnées a échoué en raison d'un incident de transfert.";
			$this->mainController->setInError ( true );
			$this->mainController->setErrorMessage ( $message );
		} else if ($error == 4) {
			$message = "Vous avez envoyé des métadonnées de taille nulle.";
			$this->mainController->setInError ( true );
			$this->mainController->setErrorMessage ( $message );
		} else {
			try {
				$file = Security::$_CLEAN ['import-metadata'];
				$this->extractLocationsFromMetadata ( $file ['tmp_name'], $locations );
				try {
					$reponse = $this->model->handleMetadataImport ( $file );
					$this->model->setResultOfMetadataImport ( $reponse );
				} catch ( BadUrlRequestException $e ) {
					$message = "Vous avez envoyé des métadonnées de taille nulle.";
					$this->mainController->setInError ( true );
					$this->mainController->setErrorMessage ( "Impossible de se connecter au service", $e->getMessage () );
					return;
				}
				
				if ($this->model->metadataImportIsSucessFull ()) {
					$metadataId = $this->model->getIdOfImportedMetadata ();
					// TODO gérér les échecs
					$contentResponse = $this->model->createNewResource ( $metadataId, "url" );
					$this->model->setResultOfResourceCreation ( $contentResponse );
				}
				$this->model->registerContentLocationsFoundInMetadata ( $locations );
			} catch ( HttpRequestException $e ) {
				
				$intro = "Il y a eu un problème...";
				switch ($e->getCode ()) {
					case "400" :
						$intro = "Vos métadonnées n'ont pas l'air valides : ";
						break;
					case "404" :
						$intro = "Le service semble injoignable : ";
						break;
					case "500" :
						$intro = "Quelquechose s'est mal passé de notre côté : ";
						break;
				}
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( $intro, $e->getContent () );
			}
		}
	}
	private function extractLocationsFromMetadata($filePath, &$locations) {
		$doc = new DOMDocument ();
		$doc->load ( $filePath );
		$domXPath = new DOMXpath ( $doc );
		$rootNamespace = $doc->lookupNamespaceUri ( $doc->namespaceURI );
		$domXPath->registerNamespace ( 'lom', $rootNamespace );
		$entry = $domXPath->query ( "lom:general/lom:identifier/lom:entry" );
		if ($entry->length > 0)
			$locations ["general.identifier.entry"] = $entry->item ( 0 )->textContent;
		$location = $domXPath->query ( "lom:technical/lom:location" );
		if ($location->length > 0)
			$locations ["technical.location"] = $location->item ( 0 )->textContent;
	}
	public function getView() {
		return $this->view;
	}
}
?>