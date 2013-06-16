<?php
class ResourcesDetailEditView extends AbstractResourceDetailView {


	protected function addContent() {
		parent::addContent();
		$this->render = str_replace("[PANEL]", HTMLLoader::load('resources-detail-edit'), $this->render);
		$updateContentArea='Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		$updateMetaArea='Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		if($this->mainController->userIsAllowedToWrite()) {
			if($this->model->getMetadata()->isBuilt())
				if($this->model->getMetadata()->isPackage())
				$updateContentArea='Fonctionnalité à venir pour les packages';
			else
				$updateContentArea=$this->getUpdateContentArea();
			else $updateContentArea='';
			if(!is_null($this->model->getMetadata()) && $this->model->getMetadata()->isBuilt()) {
				$url=$this->prefix.'/resources/detail/'.$this->model->getMetadata()->getId().'/edit';
				if(!is_null($this->model->getLomMetadata()) && $this->model->getLomMetadata()->isBuilt())
					$updateMetaArea=$this->getScolomfrForm($url);
				else $updateMetaArea="Données ScoLOMfr Indisponibles.";
			}

		}
		$this->render=str_replace("[UPDATE-CONTENT]", $updateContentArea, $this->render);
		$this->render=str_replace("[UPDATE-METADATA]", $updateMetaArea, $this->render);
	}

	private function getUpdateContentArea() {
		if(is_null($this->model->getContent()) || !$this->model->getContent()->isBuilt())
		{
			$area='<div class="ui-state-error ui-corner-all">Aucun contenu associé</div>';
			$area.='<form class="add-content" action="'.$this->prefix.'/resources/detail/'.$this->model->getMetadata()->getId().'/edit" method="POST">';
			$area.='<input type="submit" value="Associer un contenu local (fichiers)" />';
			$area.='<input type="hidden" name="add-content" value="asset" />';
			$area.='</form>';
			$area.='<form  class="add-content" action="'.$this->prefix.'/resources/detail/'.$this->model->getMetadata()->getId().'/edit" method="POST">';
			$area.='<input type="submit" value="Associer un contenu distant (lien)" />';
			$area.='<input type="hidden" name="add-content" value="url" />';
			$area.='</form>';
			return $area;
		}
		$isRemote=$this->model->getContent()->isUrl();

		$area='<form action="'.$this->prefix.'/resources/detail/'.$this->model->getMetadata()->getId().'/edit" method="POST">';
		$area.=' <div id="resource-type">';
		$area.='<input type="radio" value="asset" '.($isRemote?'':'checked="checked"').' name="resource-type" id="resource-type-asset" />';
		$area.='<label for="resource-type-asset">Ressource locale</label>';
		$area.='<input type="radio" value="url" name="resource-type" '.($isRemote?'checked="checked"':'').' id="resource-type-url" />';
		$area.='<label for="resource-type-url">Ressource distante</label>';
		$area.='<input type="submit" value="modifier" />';
		$area.='</div></form>';
		if($isRemote)
			$area.=$this->getUpdateRemoteContentArea();
		else $area.=$this->getUpdateLocalContentArea();
		return $area;
	}
	private function getUpdateRemoteContentArea() {
		$area='<form action="'.$this->prefix.'/resources/detail/'.$this->model->getMetadata()->getId().'/edit" method="POST" id="update_url">';
		$area.='<input type="text" placeholder="url" name="url" value="'.$this->model->getContent()->getDownloadLink().'" />';
		$area.='<a href="'.$this->model->getContent()->getDownloadLink().'" target="_blank" title="ouvrir le lien">Ouvrir</a>';
		$area.='<span class="url-submit"><input type="submit" value="mettre à jour" /></span>';
		
		$area.='</form>'.
				$this->getAsyncDisplayArea();
		return $area;
	}
	private function getUpdateLocalContentArea() {
		$area=$this->getFileListArea();
		$area.=$this->getFileUploadArea();
		return $area;
	}
	private function transformXMLContentRepresentation($contentXML) {
		$this->proc = $this->getXSLTProcessor('xsl/contentFilesList.xsl');
		$this->proc->setParameter('', 'prefix', $this->prefix);
		$this->proc->setParameter('', 'url', $this->prefix.'/resources/detail/'.$this->model->getMetadata()->getId());
		return $this->proc->transformToXML($contentXML);
	}
	public function getFileListArea() {
		$contentXML = $this->model->getContent()->getDocument();
		$area='';
		if(!is_null($contentXML))
			$area.= $this->transformXMLContentRepresentation($contentXML);
		return $area;
	}
	private function getFileUploadArea() {
		$action=$this->prefix.'/resources/detail/'.$this->model->getMetadata()->getId().'/edit';

		$area='<div class="file-input-container">'.
				'<form id="send_file" enctype="multipart/form-data"	action="'.$action.'" method="POST">'.
				'<input id="file_upload" type="file" name="file-for-resource" />'.
				'<input id="file_submit" type="submit" value="Ajoutez un fichier" />'.
				'<span class="checkbox-wrapper">'.
				'<input id="is_archive" type="checkbox" name="is_archive" class="css-checkbox" />'.
				'<label for="is_archive" class="css-label">Décompresser l\'archive sur le serveur</label>'.
				'</span>'.
				'<div class="bar"></div>'.
				'</form>'.
				$this->getAsyncDisplayArea();
		return $area;
	}
	private function getAsyncDisplayArea() {
		$iconDir=$this->prefix.'/img/';
		return '<div class="progress">'.
				'<div id="upload-result-status">'.
				'<img class="status-icon" data-src="'.$iconDir.'" src=""/>'.
				'<span>'.
				'</span>'.
				'</div>'.
				'<div id="upload-result-message"></div></div>';
	}
}
?>