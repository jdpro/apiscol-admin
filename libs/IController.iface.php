<?php
interface IController {

	public function completeScripts();
	public function defineView();
	public function processSyncRequest();
	public function processAsyncRequest();
	public function getView() ;
}
?>