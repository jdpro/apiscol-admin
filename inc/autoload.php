<?php
function __autoload($className) {
	if (preg_match('/^I/', $className))
	require_once 'libs/' . $className . '.iface.php';
    else require_once 'libs/' . $className . '.class.php';
}
?>