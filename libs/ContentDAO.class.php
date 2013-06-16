<?php
class ContentDAO extends AtomDAO {

	const VOID_RESOURCE="Contenu non disponible";

	public function ContentDAO($serviceAccess) {
		parent :: __construct($serviceAccess);

	}

	protected function acquireXMLString() {
		return $this->serviceAccess->getContent($this->url);
	}
	public function build() {
		parent::build();
		$this->setId(RequestUtils::extractIdFromRestUri($this->getLink()));
	}
	public function isUrl() {
		assert($this->isBuilt);
		return $this->xpath->query("/atom:entry/atom:category/@term")->item(0)->value=='url';
	}
	public function getDownloadLink() {
		assert($this->isBuilt);
		if($this->isUrl())
			return $this->xpath->query("atom:content/xhtml:a/@href")->item(0)->value;
		if($this->xpath->query("atom:content/apiscol:files/apiscol:file")->length==0)
			return self::VOID_RESOURCE;
		return $this->xpath->query("atom:content/apiscol:files/apiscol:file[atom:title=//apiscol:files/@main]/atom:link/@href")->item(0)->value;
	}
	public function getArchiveLink() {
		assert($this->isBuilt);
		if($this->isUrl())
			return self::VOID_RESOURCE;
		if($this->xpath->query("atom:content/apiscol:archive")->length==0)
			return self::VOID_RESOURCE;
		return $this->xpath->query("atom:content/apiscol:archive/@src")->item(0)->value;
	}
	public function getThumbLink() {
		assert($this->isBuilt);
		return $this->xpath->query("atom:link[@rel='icon'][@type='application/atom+xml']/@href")->item(0)->value;
	}
	public function getPreviewLink() {
		assert($this->isBuilt);
		return $this->xpath->query("atom:link[@rel='preview']/@href")->item(0)->value;
	}
	public function getFiles() {
		assert($this->isBuilt);
		$files=array();
		$filesNodes = $this->xpath->query("atom:content/apiscol:files/apiscol:file");
		for ($i = 0; $i < $filesNodes->length; $i++) {
			$fileNode=$filesNodes->item($i);
			$files[$fileNode->getElementsByTagName("title")->item(0)->textContent]=$fileNode->getElementsByTagName("link")->item(0)->getAttribute("href");
		}
		return $files;
	}
	public function sendFileForResource($file, $isArchive) {
		return $this->serviceAccess->sendFileForResource($file, $isArchive, $this->id, $this->getEtag());
	}
	public function setUrlForRemoteResource($url) {
		return $this->serviceAccess->setUrlForRemoteResource(trim($url), $this->id, $this->getEtag());
	}
	public function setResourceType($type) {
		return $this->serviceAccess->setResourceType($type, $this->id, $this->getEtag());
	}
	public function sendRefreshRequest($target) {
		return $this->serviceAccess->createContentRefreshRequest($target, $this->id, $this->getEtag());
	}
	public function setMainFile($fname) {
		return $this->serviceAccess->setMainFile($fname, $this->id, $this->getEtag());
	}
	public function deleteFile($fname) {
		return $this->serviceAccess->deleteFile($fname, $this->id, $this->getEtag());
	}
	public function delete() {
		return $this->serviceAccess->deleteContent($this->id, $this->getEtag());
	}

}

