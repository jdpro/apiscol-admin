<?php
abstract class AtomDAO extends AbstractDAO {
	protected function getDefaultNameSpace() {
		return 'atom';
	}
	public function getURN() {
		assert ( $this->isBuilt );
		return $this->xpath->query ( "/atom:entry/atom:id" )->item ( 0 )->textContent;
	}
	public function getLink() {
		assert ( $this->isBuilt );
		return $this->xpath->query ( "/atom:entry/atom:link[@rel='self'][@type='text/html']/@href" )->item ( 0 )->value;
	}
	public function getTitle() {
		assert ( $this->isBuilt );
		if ($this->xpath->query ( "/atom:entry/atom:title" )->length > 0)
			return $this->xpath->query ( "/atom:entry/atom:title" )->item ( 0 )->textContent;
		return "";
	}
	public function getEtag() {
		assert ( $this->isBuilt );
		return $this->xpath->query ( "atom:updated" )->item ( 0 )->textContent;
	}
}

