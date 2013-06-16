<?php
abstract class AbstractView implements IView {

	protected $render;
	protected $model;
	protected $prefix;
	protected $hiddenInputs;
	protected $proc;
	protected $mainController;

	public function __construct($model, $prefix, $mainController){
		$this->render='';
		$this->model=$model;
		$this->prefix=$prefix;
		$this->mainController=$mainController;
	}
	protected function createHiddenInputs() {
		$this->hiddenInputs="";
		$displayParameters=$this->model->getDisplayParameters();
		if(is_array($displayParameters))
			foreach ($displayParameters as $key=>$value) {
			$this->hiddenInputs.='<input type="hidden" id="'.$key.'" value="'.$value.'" />';
		}

	}
	protected  function getXSLTProcessor($styleSheetPath) {
		$xsl = new DOMDocument;
		$xsl->load($styleSheetPath);
		$proc = new XSLTProcessor();
		$proc->importStyleSheet($xsl);
		return $proc;
	}

	public function toHTML() {
		$this->render=str_replace("[HIDDEN]", $this->hiddenInputs, $this->render);
		return $this->render;
	}

	protected  function getScolomfrForm($url) {
		assert(!is_null($this->model->getLomMetadata()) && $this->model->getLomMetadata()->isBuilt());
		$scolomfrXML=$this->model->getLomMetadata()->getDocument();
		return $this->transformScolomfrToForm($scolomfrXML, $url);
	}
	private function transformScolomfrToForm($scolomfrXML, $url) {
		$this->proc = $this->getXSLTProcessor('xsl/scolomfrForm.xsl');
		$this->proc->setParameter('', 'prefix', $this->prefix);
		$this->proc->setParameter('', 'url', $url);
		return $this->proc->transformToXML($scolomfrXML);
	}


}
?>