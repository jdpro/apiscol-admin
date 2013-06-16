<?php
class Security {

	public static $_CLEAN;
	public static $_BUFFER;
	private static $INPUT_MAX_LENGTH = 20000;
	private static $STRINGS_MAX_LENGTH = 2000;

	private static $WHITE_LIST = array (
			"warning" => "self",
			"lang" => "lang",
			"page" => "page",
			"action" => "action",
			"dynamic-filters"=>"string",
			"static-filters"=>"string",
			"clear-filters"=>"string",
			"active-tab"=>"string",
			"west-pane"=>"integer",
			"north-pane"=>"integer",
			"south-pane"=>"integer",
			"delete-resource"=>"uuid",
			"refresh-resource"=>"uuid",
			"start"=>"integer",
			"rows"=>"integer",
			"metadata-id"=>"uuid",
			"query"=>"string",
			"choose-thumb"=>"uri",
			"custom-thumb"=>"image",
			"mode"=>"mode",
			"display-mode"=>"display-mode",
			"display-device"=>"display-device",
			"login"=>"string",
			"password"=>"string",
			"disconnect"=>"self",
			"import-metadata"=>"xml-file",
			"resource-type"=>"resource-type",
			"panel"=>"panel",
			"file-for-resource"=>"file",
			"is_archive"=>"checkbox",
			"file-list"=>"self",
			"file-action"=>"file-action",
			"fname"=>"string",
			"file-transfer-report"=>"uri",
			"url-parsing-report"=>"uri",
			"refresh-process-report"=>"uri",
			"url"=>"uri",
			"resid"=>"uuid",
			"etag"=>"string",
			"autocomplete"=>"bool",
			"add-content"=>"resource-type",
			"refresh-resource"=>"refresh-target",
			"update-metadata"=>"self",
			"general-title"=>"string",
			"general-description"=>"string",
			"general-keyword"=>"string-array",
			"general-generalResourceType"=>"string-array",
			"educational-learningResourceType"=>"string-array",
			"educational-place"=>"string-array",
			"educational-educationalMethod"=>"string-array",
			"educational-activity"=>"string-array",
			"educational-intendedEndUserRole"=>"string-array",
			"educational-difficulty"=>"string",
			"classifications" => "json"

	);
	private static $DEFAULTS = array (
			"page" => "home"
	);
	//page -> action -> panel
	private static $siteMap = array(
			"home" => array(),
			"resources"=> array(
					"list"=> array(),
					"folders"=> array(),
					"detail"=> array(
							"display",
							"uris",
							"edit",
							"edit",
							"refresh",
							"stats",
							"search"
					)),
			"add"=> array(
					"new"=> array(),
					"import"=> array()
			),
			"alerts"=> array(),
			"services"=> array()
	);


	public static function cleanPost() {

		self :: $_BUFFER = array_merge($_POST, $_GET, $_FILES);
		foreach (self :: $_BUFFER as $key => $value) {
			if (!isset (self :: $WHITE_LIST[$key]))
				return false;
			$type = self :: $WHITE_LIST[$key];
			$valid = false;
			if(!is_array($value))
				if (strlen($value) > self :: $INPUT_MAX_LENGTH)
				return false;
			switch ($type) {
				case "integer" :
					$value = filter_var($value, FILTER_VALIDATE_INT);
					//TODO améliorer ça;
					if ($value >= 0)
						$valid = true;
					break;
				case "email" :
					self :: $_BUFFER[$key] = filter_var($value, FILTER_VALIDATE_EMAIL);
					$valid = strlen(self :: $_BUFFER[$key]) > 0;
					break;
				case "self" :
					$valid = $key == $value;
					break;
				case "uuid" :
					$valid = preg_match('/^[0-9a-z]{8}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{12}$/', $value)==1;
					break;
				case "xml" :
					$valid = self :: xmlControl($value);
					break;

				case "string-array" :
					$valid=is_array($value);
					if($valid) {
						foreach ($value as $key2=>$value2) {
							self :: $_BUFFER[$key][$key2]=filter_var($value2, FILTER_SANITIZE_STRING);
							$valid = $valid && strlen(self :: $_BUFFER[$key][$key2]) > 0 && strlen(self :: $_BUFFER[$key][$key2]) < self :: $STRINGS_MAX_LENGTH;
							if(!$valid)
								break;
						}
					}
					break;
				case "string" :
					self :: $_BUFFER[$key] = filter_var($value, FILTER_SANITIZE_STRING);
					$valid = strlen(self :: $_BUFFER[$key]) < self :: $STRINGS_MAX_LENGTH;
					break;
				case "lang" :
					$valid = in_array($value, array (
					"fr",
					"en-gb"
					));
					break;
				case "bool" :
					$valid = in_array($value, array (
					"true",
					"false"
					));
					self :: $_BUFFER[$key] = $valid=="true";
					break;
				case "checkbox" :
					$valid = in_array($value, array (
					"on"
					));
					break;
				case "file-action" :
					$valid = in_array($value, array (
					"do-main", "delete"
					));
					break;
				case "page" :
					$valid = array_key_exists($value, self::$siteMap)==1;
					break;
				case "action" :
					$valid = isset(self::$_BUFFER["page"]) && array_key_exists($value, self::$siteMap[self::$_BUFFER["page"]])==1;
					break;
				case "panel" :
					$valid = isset(self::$_BUFFER["page"])
					&& array_key_exists(self::$_BUFFER["page"], self::$siteMap)==1
					&& isset(self::$_BUFFER["action"])
					&& is_array(self::$siteMap[self::$_BUFFER["page"]])==1
					&& array_key_exists(self::$_BUFFER["action"], self::$siteMap[self::$_BUFFER["page"]])==1
					&& in_array($value, self::$siteMap[self::$_BUFFER["page"]][self::$_BUFFER["action"]])==1;
					break;
				case "resource-type" :
					$valid = in_array($value, array (
					"asset",
					"url"
					));
					break;
				case "uri" :
					self :: $_BUFFER[$key] = self :: validURL($value);
					$valid = (self :: $_BUFFER[$key] != false);
					$valid=true;
					break;
				case "image" :
					if(!is_array($value))
						$valid=false;
					else {
						self :: $_BUFFER[$key] =$value;
						$valid=true;
						$valid = $valid && in_array(self ::$_BUFFER[$key]['type'], array (
								"image/jpeg",
								"image/png",
								"image/gif",
								"image/tiff"
						));
						$valid = $valid || (self ::$_BUFFER[$key]['error']!='0');
					}


					break;
				case "file" :
					if(!is_array($value))
						$valid=false;
					else {
						self :: $_BUFFER[$key] =$value;
						$valid=true;
						// 						$valid = $valid && in_array(self ::$_BUFFER[$key]['type'], array (
						// 								"image/jpeg",
						// 								"image/png",
						// 								"image/gif",
						// 								"image/tiff"
						// 						));
						$valid = $valid || (self ::$_BUFFER[$key]['error']!='0');
					}


					break;
				case "xml-file" :
					if(!is_array($value))
						$valid=false;
					else {
						self :: $_BUFFER[$key] =$value;
						$valid=true;
						$valid = $valid && in_array(self ::$_BUFFER[$key]['type'], array (
								"text/xml"
						));
						$valid = $valid || (self ::$_BUFFER[$key]['error']!='0');
					}


					break;
				case "mode" :
					$valid = in_array($value, array (
					"sync",
					"async"
					));
					break;
				case "display-mode" :
					$valid = in_array($value, array (
					"full",
					"base"
					));
					break;
				case "display-device" :
					$valid = in_array($value, array (
					"auto",
					"screen",
					"mobile"
					));
					break;
				case "content-type" :
					$valid = in_array($value, array (
					"local",
					"remote"
					));
					break;
				case "refresh-target" :
					$valid = in_array($value, array (
					"preview",
					"archive",
					"content-index",
					"metadata-index",
					"sync-tech-infos"
					));
					break;
				case "json" :
					$valid = true;
					self :: $_BUFFER[$key]=json_decode($value, true);
					break;

			}
			if ($valid === true)
			{
				self :: $_CLEAN[$key] = self :: $_BUFFER[$key];

			}
			else
			{

				return false;
			}


		}
		foreach (self::$DEFAULTS as $key => $value) {
			if(!isset(self :: $_CLEAN[$key]))
				self :: $_CLEAN[$key]=$value;
		}
		unset($_POST);
		unset($_GET);
		unset($_FILES);
		unset($_REQUEST);
		return true;
	}
	private static function xmlControl($string) {
		@ $xml = simplexml_load_string($string);
		return !is_null($xml);
	}
	private static function validURL($url)
	{
		//TODO reactiver
		//if (preg_match('/^http(s)?:\/\/[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url))
		return $url;
		//else return false;
	}
	public static function display($rendu) {
		echo $rendu;
	}
}
?>