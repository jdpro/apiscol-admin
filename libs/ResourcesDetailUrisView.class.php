<?php
class ResourcesDetailUrisView extends AbstractResourceDetailView {


	protected function addContent() {
		parent::addContent();
		$this->render = str_replace("[PANEL]", HTMLLoader::load('resources-detail-uris'), $this->render);
		$linksArea='<br/>Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		if($this->mainController->userIsAllowedToRead()) {
			$linksArea=$this->getLinksArea();
		}
		$this->render=str_replace("[LINKS]", $linksArea, $this->render);
	}

	private function getLinksArea() {
		if(!$this->model->getMetadata()->isBuilt())
			return '';
		$area='<div class="uris-bloc ui-widget-content"><h3>Métadonnées : ';
		$area.='<span class="server-origin"><img src="'.$this->prefix.'/img/services.png"/><span class="badge badge-info">'.$this->model->getServerAdress("meta").'</span></span>';
		$area.='</h3><span class="badge">'.$this->model->getMetadata()->getURN().'</span>';
		$area.=$this->getMetadataAtomLinkArea();
		$area.=$this->getMetadataScoLomFrLinkArea();
		$area.=$this->getMetadataSnippetLinkArea();
		$area.='</div>';
		$isPackage=$this->model->getMetadata()->isPackage();
		$dao=$isPackage?$this->model->getPack():$this->model->getContent();
		$uuid=$dao->isBuilt()?$uuid=$dao->getURN():Model::NO_ANSWER;

		if($uuid==Model::NO_ANSWER) {
			$area.='<div class="uris-bloc ui-widget-content"><h3>Données : <span class="badge">Pas de données associées</span>';
			$area.='<span class="server-origin"><img src="'.$this->prefix.'/img/services.png"/><span class="badge badge-info">'.$this->model->getServerAdress("content").'</span></span>';
			$area.='</div>';
		} else {
			$area.='<div class="uris-bloc ui-widget-content"><h3>Données : ';
			if($isPackage)
				$serverKey="pack";
			else $serverKey="content";
			$serverAdress=$this->model->getServerAdress($serverKey);
			$area.='<span class="server-origin"><img src="'.$this->prefix.'/img/services.png"/><span class="badge badge-info">'.$serverAdress.'</span></span>';
			$area.='</h3><span class="badge">'.$uuid.'</span>';
			$area.=$this->getContentAtomLinkArea($dao);
			if(!$isPackage)
				$area.=$this->getFileLinkArea();
			if($this->model->getMetadata()->isPackage())
			{
				$area.=$this->getManifestArea();
			} else
			{
				$area.=$this->getContentThumbsArea();
				$area.=$this->getPreviewArea();
			}
			$area.='</div>';
		}
		$area.='<div class="uris-bloc ui-widget-content"><h3 class="ui-helper-clearfix">Miniature :';
		$area.='<span class="server-origin"><img src="'.$this->prefix.'/img/services.png"/><span class="badge badge-info">'.$this->model->getServerAdress("thumbs").'</span></span>';
		$area.='</h3>';
		$area.=$this->getPresentThumbArea();
		$area.=$this->getThumbsSuggestionsArea();
		$area.='</div>';
		return $area;
	}
	private function getMetadataAtomLinkArea() {
		$area='<details><summary>Web-Service : '.$this->model->getMetadata()->getLink().'<a href="'.$this->model->getMetadata()->getLink().'?format=xml&desc=true"><img src="'.$this->prefix.'/img/extlink.png"/></a></summary>';
		$area.='<div><pre class="prettyprint"><code class="language-xml">'.htmlentities($this->model->getMetadata()->getDocumentAsString()).'</code></pre></div></details>';
		return $area;
	}
	private function getMetadataScoLomFrLinkArea() {
		if(is_null($this->model->getLomMetadata()) || !$this->model->getLomMetadata()->isBuilt())
			return 'données scoLOMfr indisponibles';
		$area='<details><summary>scoLOMfr : '.$this->model->getMetadata()->getScoLomFrLink().'<a href="'.$this->model->getMetadata()->getScoLomFrLink().'"><img src="'.$this->prefix.'/img/extlink.png"/></a></summary>';
		$area.='<div><pre class="prettyprint"><code class="language-xml">'.htmlentities($this->model->getLomMetadata()->getDocumentAsString()).'</code></pre></div></details>';
		return $area;
	}
	private function getMetadataSnippetLinkArea() {
		$area='<details><summary>Snippets : '.$this->model->getMetadata()->getSnippetLink().'<a href="'.$this->model->getMetadata()->getSnippetLink().'"><img src="'.$this->prefix.'/img/extlink.png"/></a></summary>';
		$area.='<div><pre class="prettyprint"><code class="language-xml">'.htmlentities($this->model->getSnippetXML()).'</code></pre></div></details>';
		return $area;
	}
	private function getContentAtomLinkArea($dao) {
		$area='<details><summary>Web-Service : '.$this->model->getMetadata()->getContentLink().'<a href="'.$this->model->getMetadata()->getContentLink().'?format=xml&desc=true"><img src="'.$this->prefix.'/img/extlink.png"/></a></summary>';
		$area.='<div><pre class="prettyprint"><code class="language-xml">'.htmlentities($dao->getDocumentAsString()).'</code></pre></div></details>';

		return $area;
	}
	private function getFileLinkArea() {
		if($this->model->getContent()->isUrl())
			$area='<p><span class="puce">&#x25A3;</span> Lien : '.$this->model->getContent()->getDownloadLink().'<a href="'.$this->model->getContent()->getDownloadLink().'"><img src="'.$this->prefix.'/img/extlink.png"/></a></p>';
		else {
			$area='<details><summary>Téléchargement : '.$this->model->getContent()->getDownloadLink().'<a href="'.$this->model->getContent()->getDownloadLink().'"><img src="'.$this->prefix.'/img/extlink.png"/></a></summary>';
			$area.='<div><h4>Fichiers :</h4>';
			$files=$this->model->getContent()->getFiles();
			$area.="<ul>";
			foreach ($files as $key => $value) {
				$area.='<li>'.$key.'<a href="'.$value.'"><img src="'.$this->prefix.'/img/extlink.png"/></a></li>';
			}
			$area.="</ul>";
			$area.='<h4>Archive : </h4><ul><li>'.$this->model->getContent()->getArchiveLink().'<a href="'.$this->model->getContent()->getArchiveLink().'"><img src="'.$this->prefix.'/img/extlink.png"/></a></li></ul>';
			$area.='</div></details>';
		}


		return $area;
	}
	private function getContentThumbsArea() {
		if(!$this->model->getContentThumb()->isBuilt())
			return '<p class="badge-error">Suggestions de miniatures indisponibles<p>';
		$area='<details><summary>Miniatures (suggestions) : '.$this->model->getContent()->getThumbLink();
		$area.='<a href="'.$this->model->getContent()->getThumbLink().'?format=xml&desc=true"><img src="'.$this->prefix.'/img/extlink.png"/></a></summary>';
		$area.='<div><pre class="prettyprint"><code class="language-xml">'.htmlentities($this->model->getContentThumb()->getDocumentAsString()).'</code></pre></div></details>';

		return $area;

	}
	private function getManifestArea() {
		if(!$this->model->getPack()->isBuilt())
			return '<p class="badge-error">Manifeste IMS-LD indisponible<p>';
		$area='<details><summary>Manifeste (learning-design) : '.$this->model->getPack()->getManifestLink();
		$area.='<a href="'.$this->model->getPack()->getManifestLink().'?format=xml&desc=true"><img src="'.$this->prefix.'/img/extlink.png"/></a></summary>';
		$area.='<div><pre class="prettyprint"><code class="language-xml">'.htmlentities($this->model->getIMSCPManifest()->getDocumentAsString()).'</code></pre></div></details>';
	
		return $area;
	
	}
	private function getPreviewArea() {
		$area='<p><span class="puce">&#x25A3;</span> Prévisualisation : '.$this->model->getContent()->getPreviewLink().'<a href="'.$this->model->getContent()->getPreviewLink().'"><img src="'.$this->prefix.'/img/extlink.png"/></a></p>';
		return $area;
	}
	private function getPresentThumbArea() {
		$thumbLink ="Donnée indisponible";
		if(!$this->model->getThumbsSuggestions()->isBuilt())
			return $thumbLink;
		try {
			$thumbLink=$this->model->getThumbsSuggestions()->getPresentThumbLink();
		} catch (HttpRequestException $e) {
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Les données relatives aux miniatures semblent indisponibles.", $e->getContent());
		}

		$area='<p><span class="puce">&#x25A3;</span> Actuelle : '.$thumbLink.'<a href="'.$thumbLink.'"><img src="'.$this->prefix.'/img/extlink.png"/></a></p>';
		return $area;
	}
	private function getThumbsSuggestionsArea() {
		$thumbLink ="Donnée indisponible";
		if(!$this->model->getThumbsSuggestions()->isBuilt())
			return $thumbLink;
		$suggestionsXmlRepresentations="Donnée indisponible";
		try {
			$thumbSuggestionLink=$this->model->getThumbsSuggestionsLink();
			$suggestionsXmlRepresentations=$this->model->getThumbsSuggestions()->getDocumentAsString();
		} catch (HttpRequestException $e) {
			$this->mainController->setInError(true);
			$this->mainController->setErrorMessage("Le service de suggestion des miniatures semble indisponible.", $e->getContent());
		}
		$area='<details><summary>Web-Service (suggestions) : '.$thumbSuggestionLink.'<a href="'.$thumbSuggestionLink.'&format=xml">';
		$area.='<img src="'.$this->prefix.'/img/extlink.png"/></a></summary>';
		$area.='<div><pre class="prettyprint">';
		$area.='<code class="language-xml">'.htmlentities($suggestionsXmlRepresentations).'</code>';
		$area.='</pre></div></details>';

		return $area;
	}

}
?>