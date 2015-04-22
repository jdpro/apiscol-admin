<?php
class ResourcesListController  extends AbstractResourcesController{

	private $actions = array (
			"index",
			"folders",
			"display",
			"uris",
			"update",
			"modifications"
	);

	public function completeScripts() {
		$this->mainController->addScript('layout');
		$this->mainController->addScript('init_resources_list');
		$this->mainController->addScript('init');
	}

	public function defineView() {
		$this->view = new ResourcesListView($this->model, $this->prefix, $this->mainController);
		$start=0+$this->model->getMetadataList()->getStart();
		$end=$start+$this->model->getMetadataList()->getRows();
		if(!is_null($this->model->getMetadataList()) && $this->model->getMetadataList()->isBuilt())
			$count=$this->model->getMetadataList()->getCount();
		else $count=0;
		if(!is_null($this->model->getMetadataList()) && $this->model->getMetadataList()->isBuilt())
			$this->mainController->setTitle('Ressources pédagogiques '.$start.' à '.$end. ' sur '.$count);
	}
	public function processAsyncRequest() {
		//do nothing;
	}
	public function processSyncRequest() {
		$this->processDeleteRequest(false);
		$resetStart=false;
		$this->model->prepareSearchQuery();
		if (isset (Security :: $_CLEAN['dynamic-filters'])) {
			$this->model->getMetadataList()->addDynamicFilter(Security :: $_CLEAN['dynamic-filters']);
			$resetStart=true;
		}
		if (isset (Security :: $_CLEAN['static-filters'])) {
			$this->model->getMetadataList()->addStaticFilter(Security :: $_CLEAN['static-filters']);
			$resetStart=true;
		}
		if (isset (Security :: $_CLEAN['clear-filters'])) {
			$this->model->getMetadataList()->clearFilters(Security :: $_CLEAN['clear-filters']);
			$resetStart=true;
		}
		if (isset (Security :: $_CLEAN['active-tab'])) {
			$this->model->setDisplayParameter('active-tab', Security :: $_CLEAN['active-tab']);
		} else if($_SESSION['action']=="detail") {
			$this->model->setDisplayParameter('active-tab', 'main_menu_item_display');
		}
		if (isset (Security :: $_CLEAN['north-pane'])) {
			$this->model->setDisplayParameter('north-pane', Security :: $_CLEAN['north-pane']);
		}
		if (isset (Security :: $_CLEAN['west-pane'])) {
			$this->model->setDisplayParameter('west-pane', Security :: $_CLEAN['west-pane']);
		}
		if (isset (Security :: $_CLEAN['south-pane'])) {
			$this->model->setDisplayParameter('south-pane', Security :: $_CLEAN['south-pane']);
		}
		if (isset (Security :: $_CLEAN['query'])) {
			$this->model->getMetadataList()->setQuery(Security :: $_CLEAN['query']);
		}

		$start=0;
		if (isset (Security :: $_CLEAN['start']) && !$resetStart) {
			$start=Security :: $_CLEAN['start'];
		}
		$rows=20;
		if (isset (Security :: $_CLEAN['rows'])) {
			$rows=Security :: $_CLEAN['rows'];
		}
		$this->model->setDisplayParameter('rows', $rows);
		$this->model->getMetadataList()->setRows($rows);
		$this->model->setDisplayParameter('start', $start);
		$this->model->getMetadataList()->setStart($start);
		try {
			$this->model->launchSearchQuery();
		} catch (BadUrlRequestException $e) {
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Impossible de consulter les ressources. Le service est peut-être arrêté.", $e->getMessage());
		} catch (HttpRequestException $e) {
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Le service ApiScol Seek n'a pas répondu ou a dysfonctionné (erreur ".$e->getCode().").", $e->getContent());
		} catch (CorruptedXMLStringException $e) {
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Le service ApiScol Seek a renvoyé des données illisibles.", $e->getMessage());

		}
	}

	private function processDeleteRequest($secondTry) {
		if (isset (Security :: $_CLEAN['delete-resource'])) {
			Security::$_CLEAN['metadata-id']=Security :: $_CLEAN['delete-resource'];
			try {
				$this->registerMetadataId();
			} catch (InvalidXMLStringException $e) {
				$this->mainController->setInError(true);
				$this->mainController->setErrorMessage("Les métadonnées de la ressource n'ont pas pu être lues.", $e->getMessage());
			} catch (HttpRequestException $e) {
				$this->mainController->setInError(true);
				$this->mainController->setErrorMessage("Le service ApiScol Meta n'a pas répondu ou a dysfonctionné (erreur ".$e->getCode().").", $e->getContent());
			}
			try {
				$this->acquireContent();
				if($this->model->getContent()->isBuilt())
					$this->model->getContent()->delete();
			} catch (InvalidXMLStringException $e) {
				$this->mainController->setInError(true);
				$this->mainController->setErrorMessage("Le contenu de la ressource n'a pas été correctement obtenu", $e->getMessage());
			} catch (HttpRequestException $e) {
				$this->mainController->setInError(true);
				$this->mainController->setErrorMessage("Le service ApiScol Meta n'a pas répondu ou a dysfonctionné (erreur ".$e->getCode().").", $e->getContent());
			}

			try {
				$this->model->getMetadata()->delete();

					
			} catch (HttpRequestException $e) {
				if(! $secondTry)
				{
					$this->processDeleteRequest(true);
				} else {
					$this->mainController->setInError(true);
					$this->mainController->setErrorMessage("Le service ApiScol Meta n'a pas répondu ou a dysfonctionné (erreur ".$e->getCode().").", $e->getContent());
				}

			}


		};
	}
	public function getView() {
		return $this->view;
	}
}
?>