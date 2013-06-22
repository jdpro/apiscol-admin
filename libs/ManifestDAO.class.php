<?php
class ManifestDAO extends AbstractDAO {

	const VOID_RESOURCE="Contenu non disponible";

	public function ManifestDAO($serviceAccess) {
		parent :: __construct($serviceAccess);
	}

	protected function acquireXMLString() {
		assert(isset($this->url));
		return $this->serviceAccess->getManifest($this->url);
	}

	function getDefaultNameSpace() {
		return 'imsld';
	}
	function getEtag() {
		assert(false);
	}
	protected function correctDocument() {
	}

}

