<?php
class LanguageSelector {

	private static $LANGUES = array (
			'fr',
			'en-gb'
	);
	private $rendu;

	function LanguageSelector() {
		$this->rendu = '<form method="POST" class="pad_langs" action="'.$_SERVER['REQUEST_URI'].'">';
		foreach (self :: $LANGUES as $langue) {
			$this->ajouterLangue($langue);
		}
		$this->rendu .= '<input type="submit" value="modifier la langue"/>';
		$this->rendu .= '</form>';
	}

	function ajouterLangue($cleLangue) {
		$active=$cleLangue==Translater::getLanguage();
		$this->rendu .= '<input type="radio" name="lang" id="langue_' . $cleLangue . '" value="' . $cleLangue . '"';
		if ($active)
			$this->rendu .=' checked="cheched" ';
		$this->rendu .= '/>';
		$this->rendu .= '<label for="langue_' . $cleLangue . '"><img class="item_pad_lang';
		if ($active)
			$this->rendu .=' active ';
		$this->rendu .= '" src="[PREFIX]/img/langues/' . $cleLangue . '.png" alt="' . $cleLangue . '" title="' . $cleLangue . '" /></label>';
	}

	function toHTML() {

		return $this->rendu;
	}
}
?>