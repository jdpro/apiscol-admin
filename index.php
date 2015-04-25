<?php
/*
 * Created on 24 janv. 2011 To change the template for this generated file go to Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once 'inc/autoload.php';
require_once 'inc/parameters.php';
require_once 'inc/assertions.php';
$prefix = str_replace ( "/index.php", "", $_SERVER ["SCRIPT_NAME"] );
if (! Security::cleanPost ()) {
	if (isset ( Security::$_CLEAN ["mode"] ) && Security::$_CLEAN ["mode"] == "async") {
		header ( "Content-Type:text/xml" );
		echo MainController::xmlErrorMessage ( "Les données que vous avez envoyées ont été rejetées par le contrôle de sécurité.", '', "Vous avez essayé quelque chose qui n'est pas autorisé." );
	} else
		header ( 'Location: ' . $prefix . '/warning' );
} else {
	session_start ();
	$controller = createOrWakeUpController ( $iniFilePath, $prefix );
	if (isset ( Security::$_CLEAN ['lang'] )) {
		$_SESSION ['lang'] = Security::$_CLEAN ['lang'];
	} else if (! isset ( $_SESSION ['lang'] )) {
		$_SESSION ['lang'] = 'fr';
	}
	Translater::assignLanguage ( $_SESSION ['lang'] );
	$texts = new XMLEngine ( $langsFilePath, $_SESSION ['lang'] );
	if (isset ( Security::$_CLEAN ["mode"] ) && Security::$_CLEAN ["mode"] == "async") {
		$controller->handleAsyncRequest ();
	} else {
		$controller->handleSyncRequest ();
		$view = new GlobalView ( $prefix, $controller );
		Security::display ( Translater::translate ( $view->toHTML () ) );
	}
}
function createOrWakeUpController($iniFilePath, $prefix) {
	if (! isset ( $_SESSION ['main_controller'] ))
		$_SESSION ['main_controller'] = new MainController ( $iniFilePath, $prefix );
	return $_SESSION ['main_controller'];
}

?>