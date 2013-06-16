<?php
class MetadataSearchTestDAO extends AtomFeedDAO {

	private $mdid;

	protected function acquireXMLString() {
		assert(isset($this->mdid));
		return $this->serviceAccess->getSearchTest($this->mdid);
	}

	public function setMdid($mdid) {
		$this->mdid=$mdid;
	}

}

