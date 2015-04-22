<?php
class ResourcesDetailEditController extends AbstractResourcesDetailController {
	public function completeScripts() {
		$this->mainController->addScript ( 'layout' );
		$this->mainController->addScript ( 'form' );
		$this->mainController->addScript ( 'jsonp' );
		$this->mainController->addScript ( 'cookie' );
		$this->mainController->addScript ( 'details' );
		$this->mainController->addScript ( 'dynatree' );
		$this->mainController->addScript ( 'tagit' );
		$this->mainController->addScript ( 'vcard' );
		$this->mainController->addScript ( 'scolomfr_client' );
		$this->mainController->addScript ( 'init_scolomfr' );
		$this->mainController->addCss ( 'vocabnomen' );
		$this->mainController->addCss ( 'dynatree' );
		$this->mainController->addCss ( 'tagit' );
		$this->mainController->addScript ( 'init_resources_detail_edit' );
		$this->mainController->addScript ( 'init' );
	}
	public function defineView() {
		$this->view = new ResourcesDetailEditView ( $this->model, $this->prefix, $this->mainController );
	}
	public function processSyncRequest() {
		$this->asyncMode = false;
		$this->registerMetadataId ();
		if ($this->mainController->isInError ())
			return;
		try {
			$this->acquireScolomfr ();
			$this->processUpdateMetadataAction ();
			$this->processAddContentAction ();
			$this->registerMetadataId ();
			$this->acquireContent ();
			$this->acquireScolomfr ();
			$this->registerResourceType ();
			$this->processFileSending ();
			$this->processFileActionMain ();
			$this->processFileActionDelete ();
			$this->processUrlUpdate ();
		} catch ( CorruptedXMLStringException $e ) {
			$this->mainController->setInError ( true );
			$this->mainController->setErrorMessage ( "Le fichier de données scolomfr est illisible.", $e->getMessage () );
		}
	}
	public function processAsyncRequest() {
		$this->asyncMode = true;
		if (isset ( Security::$_CLEAN ['update-metadata'] )) {
			$this->registerMetadataId ();
			$this->acquireScolomfr ();
			$this->processUpdateMetadataAction ();
		} else if (isset ( Security::$_CLEAN ['file-for-resource'] )) {
			$this->registerMetadataId ();
			if (! $this->mainController->isInError ())
				$this->acquireContent ();
			if (! $this->mainController->isInError ())
				echo $this->processFileSending ();
			if ($this->mainController->isInError ()) {
				$errors = $this->mainController->getErrorMessage ();
				echo MainController::xmlErrorMessage ( $errors ['private'], 0, $errors ['public'] );
			}
		} else if (isset ( Security::$_CLEAN ['file-list'] )) {
			$this->registerMetadataId ();
			if (! $this->mainController->isInError ())
				$this->acquireContent ();
			if (! $this->mainController->isInError ()) {
				$this->defineView ();
				echo $this->getView ()->getFileListArea ();
			} 

			else {
				// TODO écrire du html
				$errors = $this->mainController->getErrorMessage ();
				echo MainController::xmlErrorMessage ( $errors ['private'], 0, $errors ['public'] );
			}
		} else if (isset ( Security::$_CLEAN ['file-action'] ) && Security::$_CLEAN ['file-action'] == 'do-main') {
			$this->registerMetadataId ();
			if (! $this->mainController->isInError ())
				$this->acquireContent ();
			if (! $this->mainController->isInError ())
				$this->processFileActionMain ();
			if (! $this->mainController->isInError ()) {
				$this->defineView ();
				echo $this->getView ()->getFileListArea ();
			} 

			else {
				// TODO écrire du html
				$errors = $this->mainController->getErrorMessage ();
				echo MainController::xmlErrorMessage ( $errors ['private'], 0, $errors ['public'] );
			}
		} else if (isset ( Security::$_CLEAN ['file-action'] ) && Security::$_CLEAN ['file-action'] == 'delete') {
			$this->registerMetadataId ();
			if (! $this->mainController->isInError ())
				$this->acquireContent ();
			if (! $this->mainController->isInError ())
				$this->processFileActionDelete ();
			if (! $this->mainController->isInError ()) {
				$this->defineView ();
				echo $this->getView ()->getFileListArea ();
			} 

			else {
				// TODO écrire du html
				$errors = $this->mainController->getErrorMessage ();
				echo MainController::xmlErrorMessage ( $errors ['private'], 0, $errors ['public'] );
			}
		} else if (isset ( Security::$_CLEAN ['file-transfer-report'] )) {
			$url = RequestUtils::restoreProtocole ( Security::$_CLEAN ['file-transfer-report'] );
			echo $this->model->getFileTransferReport ( $url );
		} else if (isset ( Security::$_CLEAN ['url-parsing-report'] )) {
			$url = RequestUtils::restoreProtocole ( Security::$_CLEAN ['url-parsing-report'] );
			// TODO catch bad url
			echo $this->model->getUrlParsingReport ( $url );
		} else if (isset ( Security::$_CLEAN ['url'] )) {
			$this->registerMetadataId ();
			if (! $this->mainController->isInError ())
				$this->acquireContent ();
			if (! $this->mainController->isInError ())
				echo $this->processUrlUpdate ();
			else {
				$errors = $this->mainController->getErrorMessage ();
				echo MainController::xmlErrorMessage ( $errors ['private'], 0, $errors ['public'] );
			}
		}
	}
	public function registerResourceType($secondTry = false) {
		if (isset ( Security::$_CLEAN ['resource-type'] )) {
			try {
				$response = $this->model->getContent ()->setResourceType ( Security::$_CLEAN ['resource-type'] );
				$this->model->acquireContentRepresentation ( $response ["content"] );
			} catch ( HttpRequestException $e ) {
				
				if (! $secondTry) {
					$this->registerResourceType ( true );
				} else {
					$intro = 'Il y a eu un problème... (erreur ' . $e->getCode () . ')';
					switch ($e->getCode ()) {
						case 403 :
							$intro = 'Vous n\'avez plus l\'autorisation d\'écrire (erreur ' . $e->getCode () . ')';
							break;
						case 500 :
							$intro = 'Quelquechose s\'est mal passé de notre côté (erreur ' . $e->getCode () . ')';
							break;
						case 422 :
							$intro = 'Vous ne pouvez pas transformer une ressource en lien si elle contient des fichiers.';
							break;
					}
					$this->mainController->setInError ( true );
					$this->mainController->setErrorMessage ( $intro, $e->getContent () );
				}
			}
		}
	}
	public function processUpdateMetadataAction($secondTry = false) {
		if (isset ( Security::$_CLEAN ['update-metadata'] )) {
			if (array_key_exists ( 'general-title', Security::$_CLEAN ))
				$this->model->getLomMetadata ()->updateTitle ( Security::$_CLEAN ['general-title'] );
			if (array_key_exists ( 'general-description', Security::$_CLEAN ))
				$this->model->getLomMetadata ()->updateDescription ( Security::$_CLEAN ['general-description'] );
			if (array_key_exists ( 'general-coverage', Security::$_CLEAN ))
				$this->model->getLomMetadata ()->updateCoverage ( Security::$_CLEAN ['general-coverage'] );
			if (array_key_exists ( 'general-keyword', Security::$_CLEAN ))
				$this->model->getLomMetadata ()->updateKeywords ( Security::$_CLEAN ['general-keyword'] );
			if (array_key_exists ( 'general-generalResourceType', Security::$_CLEAN ))
				$this->model->getLomMetadata ()->updateGeneralResourceType ( Security::$_CLEAN ['general-generalResourceType'] );
			if (array_key_exists ( 'educational-description', Security::$_CLEAN ))
				$this->model->getLomMetadata ()->updateEducationalDescription ( Security::$_CLEAN ['educational-description'] );
			if (array_key_exists ( 'educational-learningResourceType', Security::$_CLEAN ))
				$this->model->getLomMetadata ()->updateLearningResourceType ( Security::$_CLEAN ['educational-learningResourceType'] );
			if (array_key_exists ( 'educational-place', Security::$_CLEAN ))
				$this->model->getLomMetadata ()->updatePlace ( Security::$_CLEAN ['educational-place'] );
			if (array_key_exists ( 'educational-educationalMethod', Security::$_CLEAN ))
				$this->model->getLomMetadata ()->updateEducationalMethod ( Security::$_CLEAN ['educational-educationalMethod'] );
			if (array_key_exists ( 'educational-activity', Security::$_CLEAN ))
				$this->model->getLomMetadata ()->updateActivity ( Security::$_CLEAN ['educational-activity'] );
			if (array_key_exists ( 'educational-intendedEndUserRole', Security::$_CLEAN ))
				$this->model->getLomMetadata ()->updateIntendedEndUserRole ( Security::$_CLEAN ['educational-intendedEndUserRole'] );
			if (array_key_exists ( 'educational-difficulty', Security::$_CLEAN ))
				$this->model->getLomMetadata ()->updateDifficulty ( Security::$_CLEAN ['educational-difficulty'] );
			if (array_key_exists ( 'classifications', Security::$_CLEAN ))
				$this->model->getLomMetadata ()->updateClassifications ( Security::$_CLEAN ['classifications'] );
			if (array_key_exists ( 'lifeCycle-contributor-vcard', Security::$_CLEAN ) && array_key_exists ( 'lifeCycle-contributor-date', Security::$_CLEAN ) && array_key_exists ( 'lifeCycle-contributor-role', Security::$_CLEAN ))
				$this->model->getLomMetadata ()->updateContributors ( Security::$_CLEAN ['lifeCycle-contributor-vcard'], Security::$_CLEAN ['lifeCycle-contributor-role'], Security::$_CLEAN ['lifeCycle-contributor-date'] );
			try {
				$this->model->getLomMetadata ()->send ( $this->model->getMetadata ()->getId (), $this->model->getMetadata ()->getEtag () );
			} catch ( HttpRequestException $e ) {
				if (! $secondTry) {
					
					$this->processUpdateMetadataAction ( true );
				} else {
					$intro = 'Il y a eu un problème... (erreur ' . $e->getCode () . ')';
					switch ($e->getCode ()) {
						case 403 :
							$intro = 'Vous n\'avez plus l\'autorisation d\'écrire (erreur ' . $e->getCode () . ')';
							break;
						case 500 :
							$intro = 'Quelquechose s\'est mal passé de notre côté (erreur ' . $e->getCode () . ')';
							break;
					}
					$this->mainController->setInError ( true );
					$this->mainController->setErrorMessage ( $intro, $e->getContent () );
				}
			}
		}
	}
	public function processAddContentAction($secondTry = false) {
		if (isset ( Security::$_CLEAN ['add-content'] )) {
			$type = Security::$_CLEAN ['add-content'];
			try {
				$metadataId = $this->model->getMetadata ()->getLink ();
				$contentResponse = $this->model->createNewResource ( $metadataId, $type );
				// wait during content-meta synchronization
				sleep ( 1 );
			} catch ( HttpRequestException $e ) {
				if (! $secondTry) {
					
					$this->processAddContentAction ( true );
				} else {
					$intro = 'Il y a eu un problème... (erreur ' . $e->getCode () . ')';
					switch ($e->getCode ()) {
						case 403 :
							$intro = 'Vous n\'avez plus l\'autorisation d\'écrire (erreur ' . $e->getCode () . ')';
							break;
						case 500 :
							$intro = 'Quelquechose s\'est mal passé de notre côté (erreur ' . $e->getCode () . ')';
							break;
					}
					$this->mainController->setInError ( true );
					$this->mainController->setErrorMessage ( $intro, $e->getContent () );
				}
			}
		}
	}
	public function processFileSending($secondTry = false) {
		if (isset ( Security::$_CLEAN ['file-for-resource'] )) {
			$error = Security::$_CLEAN ['file-for-resource'] ['error'];
			if ($error == 1 || $error == 2) {
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( "L'envoi du fichier a échoué en raison de sa taille excessive." );
			} else if ($error == 3) {
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( "L'envoi du fichier a échoué en raison d'un incident de transfert." );
			} else if ($error == 4) {
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( "Vous avez envoyé un fichier de taille nulle." );
			} else {
				try {
					return $this->model->getContent ()->sendFileForResource ( Security::$_CLEAN ['file-for-resource'], isset ( Security::$_CLEAN ['is_archive'] ) && Security::$_CLEAN ['is_archive'] == 'on' );
				} catch ( HttpRequestException $e ) {
					
					if (! $secondTry) {
						
						$this->processFileSending ( true );
					} else {
						$intro = 'Il y a eu un problème... (erreur ' . $e->getCode () . ')';
						switch ($e->getCode ()) {
							case 403 :
								$intro = 'Vous n\'avez plus l\'autorisation d\'écrire (erreur ' . $e->getCode () . ')';
								break;
							case 500 :
								$intro = 'Quelquechose s\'est mal passé de notre côté (erreur ' . $e->getCode () . ')';
								break;
						}
						$this->mainController->setInError ( true );
						$this->mainController->setErrorMessage ( $intro, $e->getContent () );
					}
				}
			}
		}
	}
	public function processFileActionMain($secondTry = false) {
		if (isset ( Security::$_CLEAN ['file-action'] ) && Security::$_CLEAN ['file-action'] == 'do-main') {
			if (! isset ( Security::$_CLEAN ['fname'] ))
				return;
			try {
				$response = $this->model->getContent ()->setMainFile ( Security::$_CLEAN ['fname'] );
				$this->model->acquireContentRepresentation ( $response ["content"] );
			} catch ( HttpRequestException $e ) {
				
				if (! $secondTry) {
					
					$this->registerResourceType ( true );
				} else {
					$intro = 'Il y a eu un problème... (erreur ' . $e->getCode () . ')';
					switch ($e->getCode ()) {
						case 403 :
							$intro = 'Vous n\'avez plus l\'autorisation d\'écrire (erreur ' . $e->getCode () . ')';
							break;
						case 500 :
							$intro = 'Quelquechose s\'est mal passé de notre côté (erreur ' . $e->getCode () . ')';
							break;
					}
					$this->mainController->setInError ( true );
					$this->mainController->setErrorMessage ( $intro, $e->getContent () );
				}
			}
		}
	}
	public function processFileActionDelete($secondTry = false) {
		if (isset ( Security::$_CLEAN ['file-action'] ) && Security::$_CLEAN ['file-action'] == 'delete') {
			if (! isset ( Security::$_CLEAN ['fname'] ))
				return;
			try {
				$response = $this->model->getContent ()->deleteFile ( Security::$_CLEAN ['fname'] );
				// TODO faire qqc de la réponse
				$this->model->acquireContentRepresentation ();
			} catch ( HttpRequestException $e ) {
				
				if (! $secondTry) {
					
					$this->registerResourceType ( true );
				} else {
					$intro = 'Il y a eu un problème... (erreur ' . $e->getCode () . ')';
					switch ($e->getCode ()) {
						case 403 :
							$intro = 'Vous n\'avez plus l\'autorisation d\'écrire (erreur ' . $e->getCode () . ')';
							break;
						case 500 :
							$intro = 'Quelquechose s\'est mal passé de notre côté (erreur ' . $e->getCode () . ')';
							break;
					}
					$this->mainController->setInError ( true );
					$this->mainController->setErrorMessage ( $intro, $e->getContent () );
				}
			}
		}
	}
	public function processUrlUpdate($secondTry = false) {
		if (isset ( Security::$_CLEAN ['url'] )) {
			try {
				return $this->model->getContent ()->setUrlForRemoteResource ( Security::$_CLEAN ['url'] );
			} catch ( HttpRequestException $e ) {
				
				if (! $secondTry) {
					
					$this->processUrlUpdate ( true );
				} else {
					$intro = 'Il y a eu un problème... (erreur ' . $e->getCode () . ')';
					switch ($e->getCode ()) {
						case 403 :
							$intro = 'Vous n\'avez plus l\'autorisation d\'écrire (erreur ' . $e->getCode () . ')';
							break;
						case 500 :
							$intro = 'Quelquechose s\'est mal passé de notre côté (erreur ' . $e->getCode () . ')';
							break;
					}
					$this->mainController->setInError ( true );
					$this->mainController->setErrorMessage ( $intro, $e->getContent () );
				}
			}
		}
	}
}
?>