<?php
class ThumbsSuggestionsDAO extends AbstractDAO {

	private $mdid;
	public function ThumbsSuggestionsDAO($serviceAccess) {
		parent :: __construct($serviceAccess);
	}

	protected function acquireXMLString() {
		assert(isset($this->mdid));
		return $this->serviceAccess->getThumbsSuggestions($this->mdid);
	}

	public 	function getDefaultNameSpace() {
		return 'apiscol';
	}
	public 	function getEtag() {
		return $this->document->firstChild->getAttribute("version");
	}
	public 	function setMetadatadid($mdid) {
		$this->mdid=$mdid;
	}
	public 	function getPresentThumbLink(){
		assert($this->isBuilt());
		$href=$this->xpath->query("/apiscol:thumbs/apiscol:thumb[@mdid]/apiscol:link/@href");
		if($href->length>0)
			return $href->item(0)->value;
		else return "--";
	}

}

