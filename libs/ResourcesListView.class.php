<?php
class ResourcesListView extends AbstractView implements IView {
	private $resourcesList;
	private $controls;

	public function build() {
		$this->createControls();
		$this->createHiddenInputs();
		$this->addContent();
	}
	private function transformXMLResults() {
		$this->proc=$this->getXSLTProcessor('xsl/metadataList.xsl');
		$this->proc->setParameter('', 'prefix', $this->prefix);
		$currentPage=floor($this->model->getMetadataList()->getStart()/$this->model->getMetadataList()->getRows());
		$this->proc->setParameter('', 'currentPage', $currentPage);
		$this->proc->setParameter('', 'rowsPerPage', $this->model->getMetadataList()->getRows());
		$this->proc->setParameter('', 'query', $this->model->getMetadataList()->getQuery());
		$this->proc->setParameter('', 'write_permission', $this->mainController->userIsAllowedToWrite());
		try {
			$resourcesList=$this->model->getMetadataList()->getDocumentAsString();
			$doc=new DOMDocument();
			$doc->loadXML($resourcesList);
			return $this->proc->transformToXML($doc);
		} catch (HttpRequestException $e) {
			$this->mainController->setInError(true);
			//TODO traduire
			$this->mainController->setErrorMessage("{ERROR-IMPOSSIBLE-CONNECT-META}", $e->getContent());
			return "";
		} catch (BadUrlRequestException $e) {
			$this->mainController->setInError(true);
			//TODO traduire
			$this->mainController->setErrorMessage("{ERROR-IMPOSSIBLE-CONNECT-META}", $e->getMessage());
		}
	}
	private function createControls() {
		//TODO traduire
		$this->controls='<div class="filters-list"><a href="'.$this->prefix.'/resources/list/clear-filters/[all]" class="ui-state-default ui-corner-all" title="{SUPPRESS-ALL-FILTERS}"><span class="ui-icon ui-icon-refresh"></span></a> <span>{RESOURCES-LIST-FILTERS} :</span>';
		$staticFacets=$this->model->getMetadataList()->getStaticFilters();
		foreach ($staticFacets as $key=>$value) {
			$this->controls.=' <span class="facet"><a href="'.$this->prefix.'/resources/list/clear-filters/['.$this->encode($key).']" title="{SUPPRESS-THIS-FILTER}"><span class="ui-icon ui-icon-circle-close"></span></a>'.$value.'</span> ';
		}
		$dynamicFacets=$this->model->getMetadataList()->getDynamicFilters();
		foreach ($dynamicFacets as $key=>$value) {
			$this->controls.=' <span class="facet"><a href="'.$this->prefix.'/resources/list/clear-filters/['.$this->encode($key).']" title="{SUPPRESS-THIS-FILTER}"><span class="ui-icon ui-icon-circle-close"></span></a>'.$value.'</span> ';
		}
		$this->controls.='</div>';
	}
	private function addContent() {
		$this->render .= HTMLLoader::load('resources-index');
		$resourcesList='';
		$controls='';
		if($this->mainController->userIsAllowedToRead()) {
			if(!(is_null($this->model->getMetadataList())) && $this->model->getMetadataList()->isbuilt())
				$resourcesList=$this->transformXMLResults();
			$controls=$this->controls;
		} else {
			$this->mainController->setInError(true);
			//TODO traduire
			$this->mainController->setErrorMessage("{RIGHTS-IMPOSSIBLE-BROWSE-RESOURCES}");
		}
		$this->render=str_replace("[RESOURCES_LIST]", $resourcesList, $this->render);
		$this->render=str_replace("[CONTROLS]", $controls, $this->render);
	}

	public function encode($pathSegment) {
		return preg_replace('/\?/', '%3F', $pathSegment);
	}
}
?>