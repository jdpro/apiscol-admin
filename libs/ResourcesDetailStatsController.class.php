<?php
class ResourcesDetailStatsController extends AbstractResourcesDetailController {


	public function completeScripts() {
		$this->mainController->addScript('apiscol');
		$this->mainController->addScript('init_resources_detail_view');
		$this->mainController->addScript('init');
	}


	public function defineView() {
		$this->view = new ResourcesDetailStatsView($this->model, $this->prefix, $this->mainController);
	}
	public function processSyncRequest() {
		$this->registerMetadataId();

	}
	public function processAsyncRequest() {
		;
	}
}
?>