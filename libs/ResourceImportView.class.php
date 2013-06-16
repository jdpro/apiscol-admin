<?php
class ResourceImportView extends AbstractView implements IView {


	public function ResourceImportView($model, $prefix, $mainController) {
		parent :: __construct($model, $prefix, $mainController);

	}
	public function build() {
		$this->createHiddenInputs();
		$this->addContent();
	}


	private function addContent() {
		$this->render .= HTMLLoader::load('add-metadata');
		$importMetadataArea='<br/>Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		$writeMetadataArea='<br/>Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		$importMetadataResultArea='';
		$urlRegistrationResultArea='';
		if($this->mainController->userIsAllowedToWrite()) {
			$writeMetadataArea="à venir";
			//$writeMetadataArea=$this->getScolomfrForm();
			$importMetadataArea=$this->getImportMetadataArea();
			$importMetadataResultArea=$this->getImportOfMetadataResultArea();
			$urlRegistrationResultArea=$this->getUrlRegistrationResultArea();
		}
		$this->render=str_replace("[WRITE-METADATA]", $writeMetadataArea, $this->render);
		$this->render=str_replace("[IMPORT-METADATA]", $importMetadataArea, $this->render);
		$this->render=str_replace("[IMPORT-METADATA-RESULT]", $importMetadataResultArea, $this->render);
		$this->render=str_replace("[URL-REGISTRATION-RESULT]", $urlRegistrationResultArea, $this->render);
	}
	private function getImportMetadataArea() {
		$action=$this->prefix.'/add/metadata';
		$area='<div class="add-metadata-input-container"><form id="import-metadata" enctype="multipart/form-data"	action="'.$action.'" method="POST"><input id="metadata_upload" type="file" name="import-metadata" /><input id="metadata_submit" type="submit" value="Créez une nouvelle entrée en important un fichier scoLOMfr" />'.
				'</form>	<div class="progress">	<div class="bar"></div><div class="percent">0%</div></div></div>';
		return $area;
	}
	public function getImportOfMetadataResultArea() {
		$area='';
		if($this->mainController->isInError())
		{
			$errors=$this->mainController->getErrorMessage();
			$area.='<div><div class="badge badge-error">Echec de l\'import</div>';
			$area.='<div class="ui-widget-content"><h3>'.$errors['private'].'</h3>';
			$area.='<p>'.$errors['public'].'</p>';
			$area.='</div>';
			return $area;
		}
		$result=$this->model->getResultOfMetadataImport();
		if(is_null($result))
			return '';

		$area.='<div><div class="badge badge-info">Import réussi</div>';
		$area.='<div class="ui-widget-content"><h3>'.$result->query("/atom:entry/atom:title")->item(0)->textContent.'</h3>';
		$area.='<p>'.$result->query("/atom:entry/atom:summary")->item(0)->textContent.'</p>';
		$uri=$result->query('/atom:entry/atom:link[@rel="self"][@type="text/html"]/@href')->item(0)->value;
		$area.='<p class="ui-state-highlight ui-corner-all"><strong>URI : </strong>'.$uri.'</p>';
		$locations=$this->model->getLocationsFoundInMetadata();
		$technicalLocation=$locations["technical.location"];
		$identifierEntry=$locations["general.identifier.entry"];
		if(!is_null($technicalLocation) && !is_null($identifierEntry) && $technicalLocation==$identifierEntry )
			$area.=$this->getContentLinkSuggestionArea($technicalLocation);
		else {
			if(!is_null($technicalLocation) && strlen($technicalLocation)>0)
				$area.=$this->getContentLinkSuggestionArea($technicalLocation, 'general.identifier.entry');
			if(!is_null($identifierEntry) && strlen($identifierEntry)>0)
				$area.=$this->getContentLinkSuggestionArea($identifierEntry, 'technical.location');
		}
		$area.='</div>';
		$adminLink=$this->prefix.'/resources/detail/'.RequestUtils::extractIdFromRestUri($uri).'/edit';
		$area.='<p class="imported-metadata-admin-uri"><a href="'.$adminLink.'">Consulter et modifier dans ApiScol Admin</a></p>';
		$area.='<input type="hidden" id="icon-dir" value="'.$this->prefix.'/img/" /></div>';
		return $area;
	}
	public function getUrlRegistrationResultArea() {
		$result=$this->model->getResultOfUrlRegistration();
		if(is_null($result))
			return '';
		return $result->saveXML();
	}

	private function getContentLinkSuggestionArea($link, $tag=null) {
		if(!RequestUtils::isValidURL($link))
			return '';
		$mdid=RequestUtils::extractIdFromRestUri($this->model->getIdOfImportedMetadata());
		if(is_null($tag))
			$area='<div class="content-suggestion-wrapper ui-state-focus"><h3>Ce contenu a été trouvé dans les métadonnées</h3>';
		else $area='<div class="content-suggestion-wrapper ui-state-focus"><h3>Ce contenu a été trouvé dans la balise : '.$tag.'</h3>';
		$area.='<div>'.$link.'</div>';
		$area.='<form action="'.$this->prefix.'/add/metadata'.'" method="POST" class="register-url">';
		$area.='<input type="hidden" name="url" value="'.$link.'"/>';
		$area.='<input type="hidden" name="metadata-id" value="'.$mdid.'"/>';
		$area.='<input type="submit" value="Enregistrer ce contenu et l\'associer à ces métadonnées"/>';
		$area.='</form>';
		$area.='</div>';
		return $area;
	}


}

