<?php
class ResourcesDetailDisplayView extends AbstractResourceDetailView {


	public function ResourcesDetailDisplayView($model, $prefix, $mainController) {
		parent :: __construct($model, $prefix, $mainController);

	}

	protected function addContent() {
		parent::addContent();
		$this->render = str_replace("[PANEL]", HTMLLoader::load('resources-detail-display'), $this->render);
		$displaySnippet='<br/>Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		$customThumbsArea='';
		$thumbsChoiceArea='Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		if($this->mainController->userIsAllowedToRead()) {
			$displaySnippet=$this->getDisplaySnippet();
		}

		if($this->mainController->userIsAllowedToWrite()) {
			$thumbsChoiceArea=$this->getThumbsChoiceArea();
			$customThumbsArea=$this->getCustomThumbArea();
		}
		$this->render=str_replace("[DISPLAY]", $displaySnippet, $this->render);
		$this->render=str_replace("[DISPLAY-MODE-LABEL]", $this->model->getDisplayModeLabel(), $this->render);
		$this->render=str_replace("[DISPLAY-DEVICE-LABEL]", $this->model->getDisplayDeviceLabel(), $this->render);
		$this->render=str_replace("[THUMBS]", $thumbsChoiceArea, $this->render);
		$this->render=str_replace("[CUSTOM-THUMBS]", $customThumbsArea, $this->render);
	}

	private function getDisplaySnippet() {
		if($this->mainController->isInError())
			return '';
		return '<a data-mode="'.$this->model->getDisplayMode().'" data-style="inherit" href="'.$this->model->getMetadata()->getLink().'">'.$this->model->getMetadata()->getTitle().'</a>';
	}

	private function transformXMLThumbsSuggestions($XMLsuggestions) {
		$this->proc = $this->getXSLTProcessor('xsl/thumbsSuggestionsList.xsl');
		$this->proc->setParameter('', 'prefix', $this->prefix);
		$this->proc->setParameter('', 'random', rand(0, 1000));
		$this->proc->setParameter('', 'url', $this->prefix.'/resources/detail/'.$this->model->getMetadata()->getId().'/display');
		return $this->proc->transformToXML($XMLsuggestions);
	}
	public function getThumbsChoiceArea() {
		$area='';
		if($this->mainController->isInError())
			return $area;
		try {
			$thumbsSuggestions = $this->model->getThumbsSuggestions();
			$area.= $this->transformXMLThumbsSuggestions($thumbsSuggestions->getDocument());

		} catch (HttpRequestException $e) {
			$area.="Donnee Indisponible";
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Le choix de miniatures semble indisponible.", $e->getContent());
		}
		if($this->mainController->isInError())
		{
			$errors=$this->mainController->getErrorMessage();
			$area.='<div class="ui-state-error"><strong>'.$errors["private"].'</strong><br/>'.$errors["public"].'</strong></div>' ;
		}

		$area.='</div>';
		return $area;
	}
	private function getRefreshArea() {
		$area='';
		return $area;
	}

	private function getCustomThumbArea() {
		if($this->mainController->isInError())
			return '';
		$action=$this->prefix.'/resources/detail/'.$this->model->getMetadata()->getId().'/display';
		$area='<div class="custom-image-input-container"><form id="set_custom_thumb" enctype="multipart/form-data"	action="'.$action.'" method="POST"><input id="image_upload" type="file" name="custom-thumb" /><input id="image_submit" type="submit" value="Ou proposez votre propre image" />'.
				'</form>	<div class="progress">	<div class="bar"></div><div class="percent">0%</div><div id="status"></div></div>';
		return $area;
	}

}
?>