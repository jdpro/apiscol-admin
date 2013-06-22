<?php
$key=$_GET["data"];
echo getDomainData($key);
function getDomainData($key) {
	echo file_get_contents("cache/".$key.".txt");
};