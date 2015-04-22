<?php
class ResourcesDetailRefreshController extends AbstractResourcesDetailController {


	public function completeScripts() {
		$this->mainController->addScript('layout');
		$this->mainController->addScript('form');
		$this->mainController->addScript('init_resources_detail_refresh');
		$this->mainController->addScript('init');
	}


	public function defineView() {
		$this->view = new ResourcesDetailRefreshView($this->model, $this->prefix, $this->mainController);
	}
	public function processAsyncRequest() {
		$this->registerMetadataId();

		if (isset (Security :: $_CLEAN['refresh-resource'])) {
			if(! $this->mainController->isInError())
				$this->acquireContent();
			if(! $this->mainController->isInError())
				echo $this->processRefreshRequest();
			else {
				$errors=$this->mainController->getErrorMessage();
				echo MainController::xmlErrorMessage($errors['private'],0,$errors['public']);
			}
		} else if (isset (Security :: $_CLEAN['refresh-process-report'])) {
			$url=RequestUtils::restoreProtocole(Security :: $_CLEAN['refresh-process-report']);
			try {
				echo $this->model->getRefreshProcessReport($url);
			} catch (HttpRequestException $e) {
				echo MainController::xmlErrorMessage($url. "  ".$e->getMessage(),404,"Le service ne semble pas répondre");
			}
		}
	}
	public function processSyncRequest() {
		$this->registerMetadataId();
		if(! $this->mainController->isInError())
			$this->acquireContent();
		if (isset (Security :: $_CLEAN['refresh-resource'])) {
			if(! $this->mainController->isInError())
				$this->processRefreshRequest();
		}
	}
	public function processRefreshRequest($secondTry=false) {
		$target=Security :: $_CLEAN['refresh-resource'];
		if(in_array($target, array("preview",
				"archive",
				"content-index")))
			try {
			return $this->model->getContent()->sendRefreshRequest($target);
		} catch (HttpRequestException $e) {

			if(! $secondTry)
			{
				$this->processRefreshRequest(true);
			} else {
				$intro='Il y a eu un problème... (erreur '.$e->getCode().')';
				switch ($e->getCode()) {
					case 403:
						$intro='Vous n\'avez plus l\'autorisation d\'écrire (erreur '.$e->getCode().')';
						break;
					case 500:
						$intro='Quelquechose s\'est mal passé de notre côté (erreur '.$e->getCode().')';
						break;
				}
				$this->mainController->setInError(true);
				$this->mainController->setErrorMessage($intro, $e->getContent());
			}
		}

		if($target=='sync-tech-infos')
			try {
			return $this->model->technicalInfosSyncRequest();
		} catch (HttpRequestException $e) {
			if($e->getCode()=="403")
			{
				return $this->model->technicalInfosSyncRequest();
			}

		}
		if($target=='metadata-index')
			try {
			return $this->model->getMetadata()->sendRefreshRequest($target);
		} catch (HttpRequestException $e) {

			if(! $secondTry)
			{
				$this->processRefreshRequest(true);
			} else {
				$intro='Il y a eu un problème... (erreur '.$e->getCode().')';
				switch ($e->getCode()) {
					case 403:
						$intro='Vous n\'avez plus l\'autorisation d\'écrire (erreur '.$e->getCode().')';
						break;
					case 500:
						$intro='Quelquechose s\'est mal passé de notre côté (erreur '.$e->getCode().')';
						break;
				}
				$this->mainController->setInError(true);
				$this->mainController->setErrorMessage($intro, $e->getContent());
			}
		}

	}
}
?>