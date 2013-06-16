<?php
class PackDAO extends AtomDAO {

	const VOID_RESOURCE="Contenu non disponible";

	public function PackDAO($serviceAccess) {
		parent :: __construct($serviceAccess);

	}

	protected function acquireXMLString() {
		return $this->serviceAccess->getPack($this->url);
	}
	public function build() {
		parent::build();
		$this->setId(RequestUtils::extractIdFromRestUri($this->getLink()));
	}
	
	public function getManifestLink() {
		assert($this->isBuilt);
		return $this->xpath->query("atom:link[@rel='alternate'][@type='application/xml']/@href")->item(0)->value;
	}
	public function delete() {
		return $this->serviceAccess->deletePack($this->id, $this->getEtag());
	}

}

