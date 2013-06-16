<?php

class HTMLLoader {

	public static function load($fileName) {
		if($fileName=='scolomfr')
			$content = file_get_contents('scolomfr/maquette.html');
		else $content = file_get_contents('templates/'.$fileName.'.html');
		return $content;
	}

}

?>