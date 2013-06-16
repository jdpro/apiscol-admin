<?php
class Translater {

	private static $pattern = '/\{([^{}]+)\}/';
	private static $texts;
	private static $lang;
	private static $lang_PAR_DEFAUT="fr";
	
	public static function translate($chaine) {
		if (!isset(self :: $lang))
			self :: $lang=self::$lang_PAR_DEFAUT;
		if (is_null(self :: $texts))
			self :: loadTexts();
		preg_match_all(self :: $pattern, $chaine, $cles);
		foreach ($cles[0] as $cle) {
			$cleTraduction = preg_replace('/[\{\}]/', '', $cle);
			$traduction = self::$texts->getItemValue($cleTraduction);
			$chaine = str_replace($cle, $traduction, $chaine);
		}
		return $chaine;
	}
	public static function assignLanguage($lang) {
		self::$lang=$lang;
	}
	public static function getLanguage() {
		return isset(self :: $lang)?self::$lang:self::$lang_PAR_DEFAUT;
	}
	private static function loadTexts() {
		self :: $texts = new XMLEngine('langs/website.xml', self::$lang);
	}
}
?>