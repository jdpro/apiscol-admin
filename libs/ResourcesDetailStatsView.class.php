<?php
class ResourcesDetailStatsView extends AbstractResourceDetailView {


	protected function addContent() {
		parent::addContent();
		$this->render = str_replace("[PANEL]", HTMLLoader::load('resources-detail-display'), $this->render);
		$displaySnippet='<br/>Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		if($this->mainController->userIsAllowedToRead()) {
			$displaySnippet=$this->getDisplaySnippet();
		}
		$this->render=str_replace("[DISPLAY]", $displaySnippet, $this->render);
		$this->render=str_replace("[DISPLAY-MODE-LABEL]", $this->model->getDisplayModeLabel(), $this->render);
		$this->render=str_replace("[DISPLAY-DEVICE-LABEL]", $this->model->getDisplayDeviceLabel(), $this->render);
	}

	private function getDisplaySnippet() {
		return '<a data-mode="'.$this->model->getDisplayMode().'" data-style="inherit" href="'.$this->model->getMetadata()->getLink().'">'.$this->model->getMetadata()->getTitle().'</a>';
	}

}
?>