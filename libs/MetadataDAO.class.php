<?php
class MetadataDAO extends AtomDAO {

	protected function acquireXMLString() {
		return $this->serviceAccess->getMetadata($this->getId());
	}
	public function getContentLink() {
		assert($this->isBuilt);
		$contentLink=$this->xpath->query("atom:link[@rel='describes'][@type='text/html']/@href");
		if($contentLink->length==0)
			return Model::NO_ANSWER;
		$link = $contentLink->item(0)->value;
		if(strlen(trim($link))==0)
			return Model::NO_ANSWER;
		return $link;
	}

	public function getScoLomFrLink() {
		assert($this->isBuilt);
		return $this->xpath->query("atom:link[@rel='describedby'][@type='application/lom+xml']/@href")->item(0)->value;
	}
	public function getSnippetLink() {
		assert($this->isBuilt);
		return $this->xpath->query("apiscol:code-snippet/@href")->item(0)->value;
	}
	public function delete() {
		assert($this->isBuilt);
		return $this->serviceAccess->deleteMetadata($this->id, $this->getEtag());
	}
	public function isPackage() {
		assert($this->isBuilt);
		return $this->xpath->query("atom:category/@term")->item(0)->value=="lesson";
	}
	public function sendRefreshRequest($target) {
		return $this->serviceAccess->createMetadataRefreshRequest($target, $this->id, $this->getEtag());
	}



}

