<?php
class MainController {
	private $secundaryController;
	private $model;
	private $prefix;
	private $scripts;
	private $title;
	private $css;
	private $userPermissions;
	private $parameters;
	private $userAuthorizationStatus;
	private $userLogin;
	public function __construct($iniFilePath, $prefix) {
		$this->parameters = parse_ini_file ( $iniFilePath, true );
		$this->model = new Model ( $this->parameters );
		$this->prefix = $prefix;
		$this->userAuthorizationStatus = AuthorizationStatus::NOT_CONNECTED;
		$this->userLogin = '';
		$this->title = '';
		$this->updateUserPermissions ();
	}
	public function __sleep() {
		return array (
				'model',
				'prefix',
				'userAuthorizationStatus',
				'userLogin',
				'parameters' 
		);
	}
	public function handleSyncRequest() {
		$this->initializeScriptList ();
		$this->addCommonDisplayParameters ();
		$this->processSyncRequestParameters ();
		$this->defineSecundaryController ();
		$this->secundaryController->processSyncRequest ();
		$this->secundaryController->completeScripts ();
		$this->secundaryController->defineView ();
		$view = $this->secundaryController->getView ();
		if (! is_null ( $view ))
			$view->build ();
	}
	public function handleAsyncRequest() {
		if (isset ( Security::$_CLEAN ['autocomplete'] ) && Security::$_CLEAN ['autocomplete'] == true && isset ( Security::$_CLEAN ['query'] )) {
			echo $this->model->getQuerySuggestion ( Security::$_CLEAN ['query'] );
		} else {
			$this->defineSecundaryController ();
			header ( "Content-Type:text/xml" );
			echo $this->secundaryController->processAsyncRequest ();
		}
	}
	public function __wakeup() {
		$this->updateUserPermissions ();
	}
	private function initializeScriptList() {
		$this->scripts = array ();
		$this->css = array ();
	}
	private function addCommonDisplayParameters() {
		$this->model->setDisplayParameter ( 'waiter-url', $this->prefix . "/img/wait.gif" );
		$this->model->setDisplayParameter ( 'prefix', $this->prefix );
	}
	private function processSyncRequestParameters() {
		if (isset ( Security::$_CLEAN ['langue'] )) {
			$_SESSION ['langue'] = Security::$_CLEAN ['langue'];
			Translater::assignLanguage ( Security::$_CLEAN ['langue'] );
		} else if (isset ( $_SESSION ['langue'] ))
			Translater::assignLanguage ( $_SESSION ['langue'] );
		
		if (isset ( Security::$_CLEAN ['page'] )) {
			$_SESSION ['page'] = Security::$_CLEAN ['page'];
		} else if (! isset ( $_SESSION ['page'] ))
			$_SESSION ['page'] = "home";
		if (isset ( Security::$_CLEAN ['action'] )) {
			if (isset ( $_SESSION ['action'] ))
				$_SESSION ['action_precedente'] = $_SESSION ['action'];
			else
				$_SESSION ['action_precedente'] = Security::$_CLEAN ['action'];
			$_SESSION ['action'] = Security::$_CLEAN ['action'];
		} else if (! isset ( $_SESSION ['action'] ))
			$_SESSION ['action'] = "index";
		if (isset ( Security::$_CLEAN ['disconnect'] )) {
			$this->setAuthorizationStatus ( AuthorizationStatus::NOT_CONNECTED );
			$this->setUserLogin ( "" );
		} else if (isset ( Security::$_CLEAN ['login'] ) && isset ( Security::$_CLEAN ['password'] )) {
			$this->processAuthentication ( Security::$_CLEAN ['login'], Security::$_CLEAN ['password'] );
		}
	}
	public function processAuthentication($login, $password) {
		$status = AuthorizationControl::getAuthorizationStatus ( $login, $password, $this->parameters );
		$this->setAuthorizationStatus ( $status );
		if ($status == AuthorizationStatus::NOT_CONNECTED) {
			$this->setInError ( true );
			$this->setErrorMessage ( "L'authentification a échoué." );
		} else
			$this->setUserLogin ( $login );
	}
	private function defineSecundaryController() {
		switch (Security::$_CLEAN ['page']) {
			case 'home' :
				$this->secundaryController = new HomeController ( $this, $this->model, $this->prefix );
				break;
			case 'resources' :
				switch (Security::$_CLEAN ['action']) {
					case 'list' :
						$this->secundaryController = new ResourcesListController ( $this, $this->model, $this->prefix );
						break;
					case 'detail' :
						switch (Security::$_CLEAN ['panel']) {
							case 'display' :
								$this->secundaryController = new ResourcesDetailDisplayController ( $this, $this->model, $this->prefix );
								break;
							case 'uris' :
								$this->secundaryController = new ResourcesDetailUrisController ( $this, $this->model, $this->prefix );
								;
								break;
							case 'refresh' :
								$this->secundaryController = new ResourcesDetailRefreshController ( $this, $this->model, $this->prefix );
								;
								break;
							case 'edit' :
								$this->secundaryController = new ResourcesDetailEditController ( $this, $this->model, $this->prefix );
								;
								break;
							case 'search' :
								$this->secundaryController = new ResourcesDetailSearchController ( $this, $this->model, $this->prefix );
								break;
							case 'stats' :
								$this->secundaryController = new ResourcesDetailStatsController ( $this, $this->model, $this->prefix );
								break;
							default :
								$this->secundaryController = new ResourcesDetailDisplayController ( $this, $this->model, $this->prefix );
								break;
						}
				}
				
				break;
			case 'add' :
				switch (Security::$_CLEAN ['action']) {
					case 'import' :
						$this->secundaryController = new ResourceImportController ( $this, $this->model, $this->prefix );
						break;
					case 'new' :
						if ($this->userIsAllowedToWrite ())
							$this->secundaryController = new ResourceCreationController ( $this, $this->model, $this->prefix );
						else {
							$this->secundaryController = new HomeController ( $this, $this->model, $this->prefix );
							$this->setInError ( true );
							$this->setErrorMessage ( "Vos droits ne vous permettent pas d'ajouter des ressources." );
						}
						break;
				}
				break;
			default :
				$this->secundaryController = new HomeController ( $this, $this->model, $this->prefix );
				break;
		}
	}
	public function getCurrentView() {
		$view = $this->secundaryController->getView ();
		if ($view != null)
			return $view->toHTML ();
		return "La vue courante n'est pas définie)";
	}
	public function getScriptList() {
		return $this->scripts;
	}
	public function addScript($script) {
		array_push ( $this->scripts, $script );
	}
	public function getCssList() {
		return $this->css;
	}
	public function addCss($css) {
		array_push ( $this->css, $css );
	}
	private $inError;
	private $errorMessages;
	public function isInError() {
		return $this->inError;
	}
	public function getErrorMessage() {
		return $this->errorMessages;
	}
	public function setInError($inError) {
		$this->inError = $inError;
	}
	public function setErrorMessage($public, $private = '') {
		if ($this->errorMessages == null)
			$this->errorMessages = array (
					"public" => '',
					"private" => '' 
			);
		$this->errorMessages ["public"] .= ' ' . $public;
		if (strlen ( trim ( $private ) ) > 0)
			$this->errorMessages ["private"] .= ' ' . $public . ' ' . $private . '<hr/>';
	}
	public function getUserLogin() {
		return $this->userLogin;
	}
	public function getAuthorizationStatus() {
		return $this->userAuthorizationStatus;
	}
	private function setUserLogin($login) {
		$this->userLogin = $login;
	}
	public function setTitle($title) {
		$this->title = $title;
	}
	public function getTitle() {
		return $this->title;
	}
	private function setAuthorizationStatus($status) {
		$this->userAuthorizationStatus = $status;
		$this->updateUserPermissions ();
	}
	private function updateUserPermissions() {
		if (! isset ( $this->parameters ) || ! is_array ( $this->parameters ) || ! array_key_exists ( "authorizations", $this->parameters ))
			die ( "Les paramètres de configuration doivent définir des autorisations." );
		$this->userPermissions = $this->parameters ["authorizations"] [$this->userAuthorizationStatus];
	}
	public function userIsAllowedToRead() {
		return $this->userPermissionsContains ( 'READ' );
	}
	public function userIsAllowedToWrite() {
		return $this->userPermissionsContains ( 'WRITE' );
	}
	private function userPermissionsContains($char) {
		return strstr ( $this->userPermissions, $char ) !== FALSE;
	}
	public static function xmlErrorMessage($message, $code = null, $intro = null) {
		$xmlDoc = new DOMDocument ();
		$root = $xmlDoc->appendChild ( $xmlDoc->createElement ( "error" ) );
		$messageElem = $root->appendChild ( $xmlDoc->createElement ( "message" ) );
		$messageElem->appendChild ( $xmlDoc->createTextNode ( $message ) );
		if (! is_null ( $code )) {
			$codeElem = $root->appendChild ( $xmlDoc->createElement ( "code" ) );
			$codeElem->appendChild ( $xmlDoc->createTextNode ( $code ) );
		}
		
		if (! is_null ( $intro )) {
			$introElem = $root->appendChild ( $xmlDoc->createElement ( "intro" ) );
			$introElem->appendChild ( $xmlDoc->createTextNode ( $intro ) );
		}
		
		return $xmlDoc->saveXML ();
	}
}
?>