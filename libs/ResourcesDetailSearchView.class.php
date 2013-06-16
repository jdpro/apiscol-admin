<?php
class ResourcesDetailSearchView extends AbstractResourceDetailView {
	protected function addContent() {
		parent::addContent();
		$this->render = str_replace("[PANEL]", HTMLLoader::load('resources-detail-search'), $this->render);
		$displaySnippet='<br/>Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		$results='';
		$facets='';

		if($this->mainController->userIsAllowedToRead()) {
			$facets=$this->transformSearchTestXMLResults();
			if(!is_null($this->model->getMetadataList()) && $this->model->getMetadataList()->isBuilt())
				$results=$this->transformQuerySearchXMLResults();
		} else {
			$this->mainController->setInError(true);
			//TODO traduire
			$this->mainController->setErrorMessage("{RIGHTS-IMPOSSIBLE-BROWSE-RESOURCES}");
		}
		$this->render=str_replace("[RESULTS]", $results, $this->render);
		$this->render=str_replace("[FACETS]", $facets, $this->render);
		$value="";
		if(isset(Security::$_CLEAN["query"]))
			$value=Security::$_CLEAN["query"];
		$this->render=str_replace("[QUERY-VALUE]", $value, $this->render);
	}
	public function transformQuerySearchXMLResults() {
		if(!$this->model->getMetadata()->isBuilt())
			return '';
		$this->proc=$this->getXSLTProcessor('xsl/researchTest.xsl');
		$this->proc->setParameter('', 'prefix', $this->prefix);
		$this->proc->setParameter('', 'query',Security::$_CLEAN["query"]);
		$this->proc->setParameter('', 'targetmdlink', $this->model->getMetadata()->getLink());
		try {
			$results=$this->model->getMetadataList()->getDocumentAsString();
			$doc=new DOMDocument();
			$doc->loadXML($results);
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
	private function transformSearchTestXMLResults() {
		if(!$this->model->getMetadata()->isBuilt())
			return '';
		$this->proc=$this->getXSLTProcessor('xsl/facetsTest.xsl');
		$this->proc->setParameter('', 'prefix', $this->prefix);
		try {
			$results=$this->model->getFacetsSearchTest()->getDocumentAsString();
			$doc=new DOMDocument();
			$doc->loadXML($results);
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


}
?>