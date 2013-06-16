<?php
class AddPackageView extends AbstractView   implements IView {


	public function AddPackageView($model, $prefix, $mainController) {
		parent :: __construct($model, $prefix, $mainController);

	}
	public function build() {
		$this->createHiddenInputs();
		$this->addContent();
	}


	private function addContent() {
		$this->render .= HTMLLoader::load('add-package');

	}
}
?>