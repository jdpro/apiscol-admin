<?php
class RequestUtils {

	public static function join($params) {
		$paramsJoined = array();
		if(is_null($params) || array_count_values($params)==0)
			return "";
		foreach($params as $param => $value) {
			if(trim($param)=="dynamic-filters" || trim($param)=="static-filters")
				$value=self::encodeAsFacetFilter($value);
			if(trim($param)=="fname")
				$value=self::encodeAsFileName($value);
			$paramsJoined[] = "$param=$value";
		}
		$query = implode('&', $paramsJoined);
		return $query;
	}
	public static function restoreProtocole($uri) {
		if(self::startsWith($uri, "http:/") && !self::startsWith($uri, "http://"))
			$uri = str_replace("http:/", "http://", $uri);
		if(self::startsWith($uri, "https:/") && !self::startsWith($uri, "https://"))
			$uri = str_replace("https:/", "https://", $uri);
		return $uri;
	}
	private static function startsWith($haystack,$needle,$case=true)
	{
		if($case)
			return strpos($haystack, $needle, 0) === 0;

		return stripos($haystack, $needle, 0) === 0;
	}
	private static function encodeAsFacetFilter($value) {
		$value=preg_replace("/&#39;/", "%27", $value);
		$value=preg_replace("/\s/", "+", $value);
		return $value;
	}
	private static function encodeAsFileName($value) {
		$value= rawurlencode($value);
		return $value;
	}
	public static function extractIdFromRestUri($uri) {
		$matches=array();
		$pattern = '/^http(s)?:\/\/.*\/([a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12})$/';
		if(preg_match($pattern, $uri, $matches)>0)
			return $matches[2];
		else return Model::NO_ANSWER;
	}
	public static function isValidURL($url)
	{
		return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
	}

}

?>
