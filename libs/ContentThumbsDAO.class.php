<?php
class ContentThumbsDAO extends AbstractDAO {


	public function ContentThumbsDAO($serviceAccess) {
		parent :: __construct($serviceAccess);
	}

	protected function acquireXMLString() {
		assert(isset($this->url));
		return $this->serviceAccess->getContentThumb($this->url);
	}

	function getDefaultNameSpace() {
		return 'apiscol';
	}
	function getEtag() {
		assert(false);
	}
	function setXPathNameSpace() {
		parent::setXPathNameSpace();
		$atomNamespace = $this->document->lookupNamespaceUri("atom");
		$this->xpath->registerNamespace('atom', $atomNamespace);
	}

}

