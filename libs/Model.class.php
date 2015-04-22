<?php
class Model {
	const NO_ANSWER="no_answer";

	private $parameters;
	private $serviceAccess;
	private $displayParameters;

	//data access objects
	private $metadata;
	private $metadataList;
	private $lomMetadata;
	private $facetsSearchTest;
	private $manifest;
	private $content;
	private $pack;
	private $contentThumb;
	private $thumbSuggestions;

	private $displayMode;
	private $displayDevice;
	private $snippetXML;

	private $resultOfMetadataImport;
	private $locationsFoundInMetadata;
	private $resultOfResourceCreationXPath;
	private $resultOfUrlRegistration;

	public function __construct($parameters) {
		$this->parameters = $parameters;
		$this->staticFilters=array();
		$this->dynamicFilters=array();
		$this->displayParameters=array();
		$this->inError=false;
		$this->displayMode="full";
		$this->displayDevice="screen";
	}

	public function __sleep() {
		return array (
				'parameters',
				'serviceAccess',
				'displayMode',
				'displayDevice',
				'metadataList'
		);
	}
	private function getServiceAccess() {
		//normally should not be null !
		if(is_null($this->serviceAccess))
			$this->serviceAccess = new ServiceAccess($this->parameters);
		return $this->serviceAccess;
	}

	public function __wakeup() {
		$this->inError=false;
	}

	public function setDisplayParameter($key, $value) {
		$this->displayParameters[$key]=$value;
	}
	public function getDisplayParameters() {
		return $this->displayParameters;
	}

	public function setMetadataId($mdid) {
		$this->metadata=new MetadataDAO($this->getServiceAccess());
		$this->metadata->setId($mdid);
		$this->metadata->build();
	}
	public function acquireScolomfrMetadata() {
		$this->lomMetadata=new ScoLOMfrDAO($this->getServiceAccess());
		$this->lomMetadata->setUrl($this->getMetadata()->getScoLomFrLink());
		$this->lomMetadata->build();
	}
	public function acquireFacetsTest() {
		assert(!is_null($this->metadata));
		$this->facetsSearchTest=new MetadataSearchTestDAO($this->serviceAccess);
		$this->facetsSearchTest->setMdid($this->getMetadata()->getLink());
		$this->facetsSearchTest->build();
	}
	public function acquireIMSLDManifest() {
		$this->manifest=new ManifestDAO($this->getServiceAccess());
		$this->manifest->setUrl($this->getPack()->getManifestLink());
		$this->manifest->build();
	}
	public function acquireThumbsSuggestions() {
		$this->thumbSuggestions=new ThumbsSuggestionsDAO($this->getServiceAccess());
		$this->thumbSuggestions->setMetadatadid($this->getMetadata()->getLink());
		$this->thumbSuggestions->build();
	}
	public function acquireSnippet() {
		$snippet = $this->getSnippet();
		$this->snippetXML=new DOMDocument();
		$this->snippetXML->loadXML($snippet);
	}
	public function acquireContentRepresentation($xmlString=null) {
		$this->content= new ContentDAO($this->getServiceAccess());
		if(!is_null($xmlString))
			$this->content->setXMLString($xmlString);
		else {
			$contentLink=$this->getMetadata()->getContentLink();
			if($contentLink==self::NO_ANSWER)
				throw new MetadataWithoutContentException('The metadata representation '.($this->getMetadata()->getId()).' has no associated ressource');
			$this->content->setUrl($contentLink);
		}
		$this->content->build();
	}
	public function acquirePackRepresentation() {
		$this->pack= new PackDAO($this->serviceAccess);
		$packLink=$this->getMetadata()->getContentLink();
		if($packLink==self::NO_ANSWER)
			throw new MetadataWithoutContentException('The metadata representation '.($this->getMetadata()->getId()).' has no associated package');
		$this->pack->setUrl($packLink);
		$this->pack->build();
	}
	public function acquireContentThumbRepresentation() {
		$this->contentThumb=new ContentThumbsDAO($this->getServiceAccess());
		$this->contentThumb->setUrl($this->getContent()->getThumbLink());
		$this->contentThumb->build();

	}
	public function prepareSearchQuery() {
		if(!isset($this->metadataList))
			$this->metadataList= new MetadataFeedDAO($this->getServiceAccess());

	}
	public function launchSearchQuery() {
		$this->metadataList->build();

	}
	public function getContent() {
		return $this->content;
	}
	public function getMetadata() {
		return $this->metadata;
	}
	public function getMetadataList() {
		return $this->metadataList;
	}
	public function getFacetsSearchTest() {
		return $this->facetsSearchTest;
	}
	public function getPack() {
		return $this->pack;
	}
	public function getLomMetadata() {
		return $this->lomMetadata;
	}
	public function getIMSCPManifest() {
		return $this->manifest;
	}
	public function getContentThumb() {
		return $this->contentThumb;
	}

	public function getSnippetXML() {
		if(is_null($this->snippetXML))
			return 'données indisponibles';
		$this->snippetXML->formatOutput=true;
		return $this->snippetXML->saveXML();
	}

	private function getSnippet() {
		return $this->getServiceAccess()->getSnippet($this->getMetadata()->getSnippetLink());
	}

	public function getQuerySuggestion($query) {
		return $this->getServiceAccess()->getQuerySuggestion($query);
	}
	public function getFileTransferReport($url) {
		return $this->getServiceAccess()->getFileTransferReport($url);
	}
	public function getUrlParsingReport($url) {
		return $this->getServiceAccess()->getUrlParsingReport($url);
	}
	public function getRefreshProcessReport($url) {
		return $this->getServiceAccess()->getRefreshProcessReport($url);
	}

	public function getThumbsSuggestions() {
		return $this->thumbSuggestions;
	}
	public function getThumbsSuggestionsLink() {
		return $this->parameters["services"]["thumbs"].'/suggestions?mdid='.$this->getMetadata()->getLink();
	}
	public function technicalInfosSyncRequest() {
		return $this->getServiceAccess()->createContentRefreshRequest("sync-tech-infos",$this->getContent()->getId(), $this->getContent()->getEtag());
	}
	public function assignThumbToMetadata($thumbUri) {
		$this->getServiceAccess()->assignThumbToMetadata($thumbUri, $this->getMetadata()->getLink(), $this->getThumbsSuggestions()->getEtag());
	}
	public function assignCustomThumbToMetadata($file) {
		$this->getServiceAccess()->assignCustomThumbToMetadata($file, $this->getMetadata()->getLink(), $this->getThumbsSuggestions()->getEtag());
	}

	public function createNewResource($metadataId, $resourceType) {
		return $this->getServiceAccess()->createNewResource($metadataId, $resourceType);
	}

	public function setDisplayMode($mode) {
		$this->displayMode=$mode;
	}
	public function getDisplayMode() {
		return $this->displayMode;
	}
	public function getDisplayModeLabel() {
		switch ($this->displayMode) {
			case "full":
				return "normal";
				break;
			case "base":
				return "mini";
				break;
		};
	}
	public function setDisplayDevice($device) {
		$this->displayDevice=$device;
	}
	public function getDisplayDevice() {
		return $this->displayDevice;
	}
	public function getDisplayDeviceLabel() {
		switch ($this->displayDevice) {
			case "auto":
				return "auto";
				break;
			case "screen":
				return "moniteur";
				break;
			case "mobile":
				return "mobile";
				break;
		};
	}
	public function getServerAdress($name) {
		return $this->parameters["services"][$name];
	}
	public function handleMetadataImport($file) {
		return $this->getServiceAccess()->handleMetadataImport($file);
	}
	public function postVoidMetadata() {
		$file= array();
		$file['tmp_name']='scolomfr/void-scolomfr.xml';
		$file['name']='void-scolomfr.xml';
		$file['type']='application/xml';
		return $this->getServiceAccess()->handleMetadataImport($file);
	}
	public function setResultOfMetadataImport($result) {
		if(strlen($result)==0)
			debug_print_backtrace();
		$this->resultOfMetadataImport=new DOMDocument();
		$this->resultOfMetadataImport->loadXML($result);
	}
	public function setResultOfUrlRegistration($result) {
		$this->resultOfUrlRegistration=new DOMDocument();
		$this->resultOfUrlRegistration->loadXML($result);
	}
	public function setResultOfResourceCreation($result) {
		$resultOfResourceCreation=new DOMDocument();
		$resultOfResourceCreation->loadXML($result);
		$this->resultOfResourceCreationXPath = new DOMXpath($resultOfResourceCreation);
		$rootNamespace = $resultOfResourceCreation->lookupNamespaceUri($resultOfResourceCreation->namespaceURI);
		if($resultOfResourceCreation->documentElement->localName=="entry")
			$this->resultOfResourceCreationXPath->registerNamespace('atom', $rootNamespace);
		else $this->resultOfResourceCreationXPath->registerNamespace('apiscol', $rootNamespace);
	}
	public function getResultOfUrlRegistration() {
		return $this->resultOfUrlRegistration;
	}
	public function registerContentLocationsFoundInMetadata($locations) {
		if(!$this->metadataImportIsSucessFull())
			return;
		$this->locationsFoundInMetadata=$locations;
	}
	public function getLocationsFoundInMetadata() {
		return $this->locationsFoundInMetadata;
	}
	public function metadataImportIsSucessFull() {
		return !is_null($this->resultOfMetadataImport) && $this->resultOfMetadataImport->documentElement->localName=="entry";
	}
	public function getIdOfImportedMetadata() {
		return $this->getResultOfMetadataImport()->query("/atom:entry/atom:link[@rel='self'][@type='text/html']/@href")->item(0)->value;
	}
	public function getIdOfCreatedResource() {
		return RequestUtils::extractIdFromRestUri($this->resultOfResourceCreationXPath->query("/atom:entry/atom:link[@rel='self'][@type='text/html']/@href")->item(0)->value);
	}
	public function getResultOfMetadataImport() {
		if(is_null($this->resultOfMetadataImport))
			return null;
		$resultOfMetadataImportXPath = new DOMXpath($this->resultOfMetadataImport);
		$rootNamespace = $this->resultOfMetadataImport->lookupNamespaceUri($this->resultOfMetadataImport->namespaceURI);
		if($this->resultOfMetadataImport->documentElement->localName=="entry")
			$resultOfMetadataImportXPath->registerNamespace('atom', $rootNamespace);
		else $resultOfMetadataImportXPath->registerNamespace('apiscol', $rootNamespace);
		return $resultOfMetadataImportXPath;
	}


}
?>