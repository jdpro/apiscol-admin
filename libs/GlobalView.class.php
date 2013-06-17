<?php
class GlobalView {

	private $render;
	private $css;
	private $scripts;
	private $languageSelector;
	private $menu;
	private $content;
	private $prefix;
	private $mainController;

	private static $scriptPaths = array(
			'jquery' => '//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.js',
			'jquery-ui'=> '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js',
			'layout' => '#/js/layout/jquery.layout-latest.js',
			'rcarousel' => '#/js/rcarousel/jquery.ui.rcarousel.js',
			'popup' => '#/js/popup/jquery.ui.popup.js',
			'jsonp' => '#/js/jsonp/jquery.jsonp-2.4.0.min.js',
			'details' => '#/js/details/jquery.details.min.js',
			'prettify' => '#/js/prettify/run_prettify.js',
			'cookie' => '#/js/cookie/jquery.cookie.js',
			'tagit' => '#/js/tagit/tag-it.min.js',
			'dynatree' => '#/js/dynatree/jquery.dynatree.js',
			'scolomfr_client' => '#/js/scolomfr/client_scolomfr.js',
			'custom-file-input'=> '#/js/custom-file-input/jquery-custom-file-input.js',
			'form'=> '#/js/form/jquery.form.js',
			'vcard'=> '#/js/vcard/vcard.js',
			'init' => '#/js/init.js',
			'init_home' => '#/js/init_home.js',
			'init_scolomfr' => '#/js/init_scolomfr.js',
			'init_resources_list' => '#/js/init_resources_list.js',
			'init_resources_detail_view' => '#/js/init_resources_detail_view.js',
			'init_resources_detail_uris' => '#/js/init_resources_detail_uris.js',
			'init_resources_detail_refresh' => '#/js/init_resources_detail_refresh.js',
			'init_resources_detail_edit' => '#/js/init_resources_detail_edit.js',
			'init_resources_detail_stats' => '#/js/init_resources_detail_stats.js',
			'init_resources_detail_search' => '#/js/init_resources_detail_search.js',
			'init_add_metadata' => '#/js/init_add_metadata.js',
			'init_add_package' => '#/js/init_add_package.js',
			'apiscol' => 'http://apiscol.crdp-versailles.fr/cdn/0.0.1/js/jquery.apiscol.js'
	);
	private static $cssPaths = array(
			'main' => '#/css/styles.css',
			'normalize' => '#/css/normalize.css',
			'jquery-ui' => '#/css/custom-theme/jquery-ui-1.10.3.custom.min.css',
			'rcarousel' => '#/js/rcarousel/rcarousel.css',
			'dynatree' => '#/js/dynatree/ui.dynatree.css',
			'tagit' => '#/js/tagit/jquery.tagit.css',
			'vocabnomen' => '#/css/vocabnomen.css'
	);

	function GlobalView($prefix, $controller) {
		$this->prefix=$prefix;
		$this->mainController=$controller;
		$this->render = HTMLLoader::load('frame');
		$this->css = '';
		$this->scripts = '';
		$this->initalizeScripts();
		$this->initalizeStyles();
		$this->createLanguageSelector();
		$this->createMenu();

	}
	function initalizeScripts() {
		$this->addScript('jquery');
		$this->addScript('jquery-ui');
		$this->addScript('popup');
		foreach ( $this->mainController->getScriptList() as $script ) {
			$this->addScript($script);
		}
	}
	function initalizeStyles() {
		$this->addCss('normalize');
		$this->addCss('jquery-ui');
		$this->addCss('main');
		foreach ( $this->mainController->getCssList() as $css ) {
			$this->addCss($css);
		}
	}
	function createLanguageSelector() {
		$this->languageSelector= new LanguageSelector();
	}
	function addCss($cssName) {
		$this->css .= '<link rel="stylesheet" type="text/css" href="'.str_replace('#', $this->prefix, self::$cssPaths[$cssName]).'" />';
	}
	function addScript($scriptName) {
		$this->scripts .= '<script src="'.str_replace('#', $this->prefix, self::$scriptPaths[$scriptName]).'" type="text/javascript"></script>';
	}
	private function createMenu() {
		$this->menu= new Menu($this->prefix);
		$this->menu->addItem('HOME', "home",$_SESSION['page'], true);
		$this->menu->addItem('RESOURCES', "resources",$_SESSION['page'], false);
		$this->menu->addItem('ADD', "add", $_SESSION['page'], false);
		$this->menu->addItem('ALERTS', "alerts", $_SESSION['page'], false);
		$this->menu->addItem('SERVICES', "services", $_SESSION['page'], false);
		$this->menu->addItem('PREFERENCES', "preferences", $_SESSION['page'], false);
		$this->menu->addItem('UTILISATEURS', "utilisateurs", $_SESSION['page'], false);
		$this->menu->addItem('STATISTIQUES', "stats", $_SESSION['page'], false);
		$this->menu->addSubItem('resources', 'RESOURCES-MENU-LIST','list', $_SESSION['action']);
		$this->menu->addSubItem('resources', 'RESOURCES-MENU-FOLDER','folder', $_SESSION['action']);
		$this->menu->addSubItem('add', 'ADD-MENU-NEW','new', $_SESSION['action']);
		$this->menu->addSubItem('add', 'ADD-MENU-IMPORT','import', $_SESSION['action']);

	}

	function toHTML() {
		$this->render=str_replace('[STYLES]', $this->css, $this->render);
		$this->render=str_replace('[SCRIPTS]', $this->scripts, $this->render);
		$this->render=str_replace('[LANGUES]', $this->languageSelector->toHTML(), $this->render);
		$this->render=str_replace('[MENU]', $this->menu->toHTML(), $this->render);
		$this->render=str_replace('[CONTENT]', $this->mainController->getCurrentView(), $this->render);
		$this->render=str_replace('[PREFIX]', $this->prefix, $this->render);
		if(!$this->mainController->isInError())
			$title=$this->mainController->getTitle();
		else $title='';
		$this->render=str_replace('[TITLE]', $title, $this->render);
		$this->render=str_replace("[ERROR]", $this->getErrorArea(), $this->render);
		$this->render=str_replace("[CONNEXION]", $this->getAuthorizationArea() , $this->render);
		return $this->render;
	}
	public function getErrorArea() {
		if(!$this->mainController->isInError())
			return "";
		else {
			$errors=$this->mainController->getErrorMessage();
			return '<span class="ui-state-highlight error-message"><img src="'.$this->prefix.'/img/warning.png" alt="warning"/>'.$errors["public"].'</span><div class="private-error-message ui-state-highlight ui-corner-all">'.$errors["private"].'</div>';
		}
	}
	public function getAuthorizationArea() {
		if($this->mainController->getAuthorizationStatus()==AuthorizationStatus::NOT_CONNECTED)
			return '<span class="status connexion">{CONNEXION}</span>'
			.'<div class="connexion-box ui-state-highlight ui-corner-all">'
			.'<form action="'.$_SERVER['REQUEST_URI'].'" method="POST">'
			.'<input type="text" name="login" id="login" placeholder="Login" />'
			.'<input type="password" name="password" id="password" placeholder="{PASSWORD}" />'
			.'<input type="submit" value="{SUBMIT}" />'
			.'</form>'
			.'<a href="https://cas.crdp.ac-versailles.fr/cas/login?service=http%3A%2F%2Fpas/encore/implémenté">Connexion CAS</a>'
			.'</div>';
		else return '<span class="status disconnect"><span class="user-login">'.$this->mainController->getUserLogin().
		'</span><form action="'.$_SERVER['REQUEST_URI'].'" method="POST">'
		.'<input type="hidden" name="disconnect" id="disconnect" value="disconnect" />'
		.'<span class="disconnect-wrapper"><input type="submit" value="{DISCONNECT}"></span>'
		.'</form>'.'</span>';
	}
}

?>
