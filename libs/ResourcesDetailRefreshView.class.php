<?php
class ResourcesDetailRefreshView extends AbstractResourceDetailView {


	protected function addContent() {
		parent::addContent();
		$this->render = str_replace("[PANEL]", HTMLLoader::load('resources-detail-refresh'), $this->render);
		$refreshPreviewArea='<br/>Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		$refreshArchiveArea='';
		$refreshResourceIndexArea='';
		$refreshMetaIndexArea='<br/>Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		$technicalInfoSyncArea='';
		if($this->mainController->userIsAllowedToWrite()) {
			$refreshPreviewArea=$this->getRefreshPreviewArea();
			if(!is_null($this->model->getContent()) && $this->model->getContent()->isBuilt() &&  !$this->model->getContent()->isUrl())
				$refreshArchiveArea=$this->getRefreshArchiveArea();
			$refreshResourceIndexArea=$this->getRefreshResourceIndexArea();
			$refreshMetaIndexArea=$this->getRefreshMetaIndexArea();
			$technicalInfoSyncArea=$this->getTechnicalInfoSyncArea();
		}
		$this->render=str_replace("[REFRESH-PREVIEW]", $refreshPreviewArea, $this->render);
		$this->render=str_replace("[REFRESH-ARCHIVE]", $refreshArchiveArea, $this->render);
		$this->render=str_replace("[REFRESH-RESOURCE-INDEX]", $refreshResourceIndexArea, $this->render);
		$this->render=str_replace("[REFRESH-META-INDEX]", $refreshMetaIndexArea, $this->render);
		$this->render=str_replace("[TECHNICAL-INFO-SYNC]", $technicalInfoSyncArea, $this->render);
	}

	private function getRefreshPreviewArea() {
		$area='<div class="refresh-control ui-helper-clearfix"><form action="[PREFIX]/resources/detail/[MDID]/refresh" method="POST"	id="refresh-resource-preview"><input type="hidden" name="refresh-resource" value="preview" /> <input type="submit" value="rafraichir" />Régénérer la prévisualisation</form>	<div class="display-result"></div></div>';
		return $area;
	}
	private function getRefreshArchiveArea() {
		$area='<div class="refresh-control ui-helper-clearfix"><form action="[PREFIX]/resources/detail/[MDID]/refresh" method="POST"	id="refresh-resource-archive"><input type="hidden" name="refresh-resource" value="archive" /> <input type="submit" value="rafraichir" />Reconstruire l\'archive</form>	<div class="display-result"></div></div>';
		return $area;
	}
	private function getRefreshResourceIndexArea() {
		$area='<div class="refresh-control ui-helper-clearfix"><form action="[PREFIX]/resources/detail/[MDID]/refresh" method="POST"	id="refresh-resource-index"><input type="hidden" name="refresh-resource" value="content-index" /> <input type="submit" value="rafraichir" />Réindexer dans le moteur de recherche</form>	<div class="display-result"></div></div>';
		return $area;
	}
	private function getRefreshMetaIndexArea() {
		$area='<div class="refresh-control ui-helper-clearfix"><form action="[PREFIX]/resources/detail/[MDID]/refresh" method="POST"	id="refresh-meta-index"><input type="hidden" name="refresh-resource" value="metadata-index" /> <input type="submit" value="rafraichir" />Réindexer dans le moteur de recherche</form>	<div class="display-result"></div></div>';
		return $area;
	}
	private function getTechnicalInfoSyncArea() {
		$area='<div class="refresh-control ui-helper-clearfix"><form action="[PREFIX]/resources/detail/[MDID]/refresh" method="POST"	id="sync-tech-infos"><input type="hidden" name="refresh-resource" value="sync-tech-infos" /> <input type="submit" value="rafraichir" />Brassage des informations techniques</form>	<div class="display-result"></div></div>';
		return $area;
	}


}
?>