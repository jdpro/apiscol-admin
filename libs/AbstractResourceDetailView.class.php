<?php
class AbstractResourceDetailView extends AbstractView implements IView {

	protected $metadataId;

	public function ResourceDetailView($model, $prefix, $mainController) {
		parent :: __construct($model, $prefix, $mainController);

	}
	public function build() {
		$this->createHiddenInputs();
		$this->addContent();
		if(!is_null($this->model->getMetadata()) && $this->model->getMetadata()->isBuilt())
			$this->mainController->setTitle("Détail de la ressource : ".$this->model->getMetadata()->getTitle());
	}


	protected function addContent() {
		$this->render .= HTMLLoader::load('resources-detail-frame');
		if($this->mainController->userIsAllowedToRead()) {
			$this->metadataId=$this->model->getMetadata()->getId();
		} else {
			$this->metadataId='0';
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Vos droits ne vous permettent pas de consulter les ressources.");
		}
	}

	protected function encode($pathSegment) {
		return preg_replace('/\?/', '%3F', $pathSegment);
	}

	public function toHTML() {
		$this->render=str_replace("[MDID]", $this->metadataId, $this->render);
		return parent::toHTML();
	}
}
?>