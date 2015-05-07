<?php
class ServiceAccess {
	const NO_ANSWER = "no_answer";
	private $parameters;
	private $client;
	public function __construct($parameters) {
		$this->parameters = $parameters;
		if (! is_null ( $this->parameters ) && is_array ( $this->parameters ) )
			$this->client = new RestClient ( $this->parameters ["services"] ["edit"]);
		else
			throw new Exception ( "La session a expiré" );
	}
	public function __sleep() {
		return array (
				'parameters',
				'client' 
		);
	}
	public function __wakeup() {
	}
	public function getMetadataList($query = null, $dynamicFilters = array(), $staticFilters = array(), $start, $rows) {
		$params = array (
				"desc" => "true",
				"start" => $start,
				"rows" => $rows 
		);
		if (isset ( $query ))
			$params ["query"] = urlencode ( $query );
		if (count ( $dynamicFilters ) > 0)
			$params ["dynamic-filters"] = json_encode ( $dynamicFilters );
		if (count ( $staticFilters ) > 0)
			$params ["static-filters"] = json_encode ( $staticFilters );
		$response = $this->client->setUrl ( $this->parameters ["services"] ["seek"] )->get ( $params );
		return $response ["content"];
	}
	public function getSearchTest($mdid) {
		$params = array (
				"mdid" => $mdid 
		);
		$response = $this->client->setUrl ( $this->parameters ["services"] ["seek"] )->get ( $params );
		return $response ["content"];
	}
	public function getMetadata($metadataId) {
		$params = array (
				"desc" => "true" 
		);
		$response = $this->client->setUrl ( $this->parameters ["services"] ["meta"] . '/' . $metadataId )->get ( $params );
		return $response ["content"];
	}
	public function getScoLomFr($scoLomFrLink) {
		$scoLomFrLink = str_replace ( '/lom', '/lom/nocache', $scoLomFrLink );
		$response = $this->client->setUrl ( $scoLomFrLink )->get ( array (
				"time",
				time () 
		) );
		return $response ["content"];
	}
	public function getManifest($manifestLink) {
		$response = $this->client->setUrl ( $manifestLink )->get ();
		return $response ["content"];
	}
	public function getSnippet($snippetLink) {
		$response = $this->client->setUrl ( $snippetLink )->get ();
		return $response ["content"];
	}
	public function getContent($contentLink) {
		$response = $this->client->setUrl ( $contentLink )->get ();
		return $response ["content"];
	}
	public function getPack($packLink) {
		$params = array (
				"desc" => "true" 
		);
		$response = $this->client->setUrl ( $packLink )->get ( $params );
		return $response ["content"];
	}
	public function getContentThumb($contentThumbLink) {
		$response = $this->client->setUrl ( $contentThumbLink )->get ();
		return $response ["content"];
	}
	public function getQuerySuggestion($query) {
		$params ["query"] = urlencode ( $query );
		$response = $this->client->setUrl ( $this->parameters ["services"] ["seek"] . '/suggestions' )->get ( $params );
		return $response ["content"];
	}
	public function getFileTransferReport($url) {
		// TODO tester si commence par adresse de edit
		$response = $this->client->setUrl ( $url )->get ();
		return $response ["content"];
	}
	public function getUrlParsingReport($url) {
		// TODO tester si commence par adresse de edit
		$response = $this->client->setUrl ( $url )->get ();
		return $response ["content"];
	}
	public function getRefreshProcessReport($url) {
		// TODO tester si commence par adresse de content
		$response = $this->client->setUrl ( $url )->get ();
		return $response ["content"];
	}
	public function getThumbsSuggestions($metadataLink) {
		$params ["mdid"] = $metadataLink;
		$response = $this->client->setUrl ( $this->parameters ["services"] ["thumbs"] . '/suggestions' )->get ( $params );
		// TODO traiter les cas d'erreur
		return $response ["content"];
	}
	public function assignThumbToMetadata($thumbUri, $metadataLink, $ifMatch) {
		// problem with mod_rewrite : workaround
		$thumbUri = RequestUtils::restoreProtocole ( $thumbUri );
		$thumbUri = urlencode ( $thumbUri );
		$params ["mdid"] = $metadataLink;
		$params ["src"] = $thumbUri;
		$response = $this->client->setUrl ( $this->parameters ["services"] ["edit"] . '/thumb' )->put ( null, $params, "application/xml", "application/x-www-form-urlencoded", $ifMatch, null );
	}
	public function assignCustomThumbToMetadata($file, $metadataLink, $ifMatch) {
		$params ["mdid"] = $metadataLink;
		$response = $this->client->setUrl ( $this->parameters ["services"] ["edit"] . '/thumb' )->postMultipartWithFile ( $params, "image", $file, "application/xml", $ifMatch, null );
	}
	public function sendFileForResource($file, $isArchive, $resourceId, $ifMatch) {
		$params ["resid"] = $resourceId;
		$params ["is_archive"] = $isArchive ? "true" : "false";
		$response = $this->client->setUrl ( $this->parameters ["services"] ["edit"] . '/transfer/' )->postMultipartWithFile ( $params, "file", $file, "application/xml", $ifMatch, null );
		return $response ["content"];
	}
	public function setUrlForRemoteResource($url, $resourceId, $ifMatch) {
		$params ["resid"] = $resourceId;
		$params ["url"] = $url;
		$response = $this->client->setUrl ( $this->parameters ["services"] ["edit"] . '/url_parsing' )->post ( $params, array (), "application/xml", "application/x-www-form-urlencoded", $ifMatch, null );
		return $response ["content"];
	}
	public function createContentRefreshRequest($target, $resourceId, $ifMatch) {
		$params ["index"] = $target == "content-index" ? "true" : "false";
		$params ["preview"] = $target == "preview" ? "true" : "false";
		$params ["archive"] = $target == "archive" ? "true" : "false";
		$params ["sync-tech-infos"] = $target == "sync-tech-infos" ? "true" : "false";
		$response = $this->client->setUrl ( $this->parameters ["services"] ["edit"] . '/resource/' . $resourceId . '/refresh' )->post ( $params, array (), "application/xml", "application/x-www-form-urlencoded", $ifMatch, null );
		return $response ["content"];
	}
	public function createMetadataRefreshRequest($target, $metadataId, $ifMatch) {
		$params ["index"] = $target == "metadata-index" ? "true" : "false";
		$response = $this->client->setUrl ( $this->parameters ["services"] ["edit"] . '/meta/' . $metadataId . '/refresh' )->post ( $params, array (), "application/xml", "application/x-www-form-urlencoded", $ifMatch, null );
		return $response ["content"];
	}
	public function createNewResource($metadataId, $resourceType) {
		$params = array ();
		$params ["mdid"] = $metadataId;
		$params ["type"] = $resourceType;
		$response = $this->client->setUrl ( $this->parameters ["services"] ["edit"] . '/resource' )->post ( $params, array (), "application/xml", "application/x-www-form-urlencoded", "whatyouwant", null );
		return $response ["content"];
	}
	public function handleMetadataImport($file) {
		$params = array ();
		$response = $this->client->setUrl ( $this->parameters ["services"] ["edit"] . '/meta' )->postMultipartWithFile ( $params, "file", $file, "application/xml", null, null );
		return $response ["content"];
	}
	public function sendMetadataFile($xmlString, $mdid, $ifMatch) {
		$params = array ();
		$response = $this->client->setUrl ( $this->parameters ["services"] ["edit"] . '/meta/' . $mdid )->putMultipartWithXML ( $params, "file", $xmlString, "application/xml", $ifMatch, null );
		return $response ["content"];
	}
	public function setResourceType($type, $resourceId, $ifMatch) {
		$params ["type"] = $type;
		$response = $this->client->setUrl ( $this->parameters ["services"] ["edit"] . '/resource/' . $resourceId )->put ( $params, null, "application/xml", "application/x-www-form-urlencoded", $ifMatch, null );
		return $response;
	}
	public function setMainFile($fname, $resourceId, $ifMatch) {
		$params ["main_filename"] = $fname;
		$response = $this->client->setUrl ( $this->parameters ["services"] ["edit"] . '/resource/' . $resourceId )->put ( $params, null, "application/xml", "application/x-www-form-urlencoded", $ifMatch, null );
		return $response;
	}
	public function deleteFile($fname, $resourceId, $ifMatch) {
		$params ["fname"] = $fname;
		$response = $this->client->setUrl ( $this->parameters ["services"] ["edit"] . '/resource/' . $resourceId )->delete ( null, $params, "application/xml", "application/x-www-form-urlencoded", $ifMatch, null );
		return $response;
	}
	public function deleteContent($resourceId, $ifMatch) {
		$response = $this->client->setUrl ( $this->parameters ["services"] ["edit"] . '/resource/' . $resourceId )->delete ( null, null, "application/xml", "application/x-www-form-urlencoded", $ifMatch, null );
		return $response;
	}
	public function deleteMetadata($mdid, $ifMatch) {
		$response = $this->client->setUrl ( $this->parameters ["services"] ["edit"] . '/meta/' . $mdid )->delete ( null, null, "application/xml", "application/x-www-form-urlencoded", $ifMatch, null );
		return $response;
	}
}
?>