<?php
class ResourcesDetailSearchController extends AbstractResourcesDetailController {


	public function completeScripts() {
		$this->mainController->addScript('layout');
		$this->mainController->addScript('init_resources_detail_search');
		$this->mainController->addScript('init');
	}


	public function defineView() {
		$this->view = new ResourcesDetailSearchView($this->model, $this->prefix, $this->mainController);
	}
	public function processSyncRequest() {
		$this->registerMetadataId();
		if(!$this->model->getMetadata()->isBuilt())
			return;	
		$this->acquireFacetsTest();
		if (isset (Security :: $_CLEAN['query'])) {
			$this->model->prepareSearchQuery();
			$this->model->getMetadataList()->setQuery(Security :: $_CLEAN['query']);
			$this->model->getMetadataList()->clearFilters("all");
			$this->model->getMetadataList()->setStart(0);
			$this->model->getMetadataList()->setRows(10000);
			$this->model->launchSearchQuery();
		}

	}
	public function processAsyncRequest() {
		$this->registerMetadataId();
		if (isset (Security :: $_CLEAN['query'])) {
			$this->model->prepareSearchQuery();
			$this->model->getMetadataList()->setQuery(Security :: $_CLEAN['query']);
			$this->model->getMetadataList()->clearFilters("all");
			$this->model->getMetadataList()->setStart(0);
			$this->model->getMetadataList()->setRows(10000);
			$this->model->launchSearchQuery();
		}
		$this->defineView();
		$results=$this->getView()->transformQuerySearchXMLResults();
		//TODO traiter les cas d'erruer
		echo $results;
	}
}
?>