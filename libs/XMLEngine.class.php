<?php
/***********************************************************
Nom du fichier : XMLEngine.php
Auteur : Flauder Vincent
Description : Fichier de classe XMLEngine permettant de manipuler
 les fichiers XML conforme.
************************************************************/
class XMLEngine
{
	private $domFile;
	private $currentLanguage;
	private $currentFile;
	private $fileLock;	

	public function __construct( $filename, $language )
	{
		$this->currentLanguage = $language;
		$this->currentFile = $filename;
		$this->domFile = new DOMDocument('1.0', 'utf-8');
		$this->domFile->preserveWhiteSpace = false; 
		$this->fileLock = fopen( str_replace( '.xml', 'xml.checker', $this->currentFile ),'a+'); 
		$this->changeFile( $filename );
	}

	public function getCurrentFile()
	{
		return $this->currentFile;	
	}

	public function getItemValue( $itemName )
	{
		//On recherche l'élément selon sont IDentifiant.
		$item = $this->domFile->getElementById( $itemName );
		
		if ( $item != NULL )
		{
			//On récupere la liste des éléments "translation" que l'on parcours...
			foreach ( ($item->getElementsByTagName( 'translation' )) as $itemTest )
			{
				//...afin de trouver celui dont l'attribut lang est égal à celui de l'objet.
				if ( $itemTest->getAttribute( 'lang' ) == $this->currentLanguage )
				{
					//Puis on retourne le texte donné.
					return  $itemTest->nodeValue ;
				}
			}
			
		}
		else
		{
			return "NULL";
		}
	}
 
	public function getItemNodeList()
	{
		$i = 0;
		//On parcours tous les éléments "xmldata"
		foreach ( ($this->domFile->getElementsByTagName( 'xmldata' )) as $item )
		{
			//Puis on sauve chacun des identifiants dans un tableau.
			$returnTab[$i++] = $item->getAttribute( 'id' );
		}
		
		return $returnTab;
	}
	

	public function changeFile( $newFile )
	{
		$this->domFile->load($newFile);
		$this->domFile->validate();
		$this->currentFile = $newFile;
	}

	public function changeLanguage( $newLanguage )
	{
		$this->currentLanguage = $newLanguage;		
	}

	public function addXmlElement( $itemID, $lang, $itemDefaultValue )
	{
		//On créer l'objet "xmldata"
		$xmlItem = $this->domFile->createElement( 'xmldata', '' );
		
		
		//Puis l'objet "translation"
		$translateItem = $this->domFile->createElement( 'translation', utf8_encode($itemDefaultValue) );
		$translateItem->setAttribute( 'lang', $lang );
		
		//On associe l'élément "translate" dans "xmldata".
		$xmlItem->appendChild( $translateItem );
		
		if ( $this->domFile->getElementById( $itemID ) == NULL )
		{
			//On rajoute seulement l'attribut ID
			$xmlItem->setAttribute( 'id', utf8_encode($itemID) );
			//On attache le nouvel élément au noeud principal
			$root = $this->domFile->getElementsByTagName( 'language' );
			$root->item(0)->appendChild( $xmlItem );
			
			//Sauvegarde du fichier.
			$this->saveFile();
			return true;//La création de l'objet s'est bien déroulée.
		}
		else
		{
			//Renvoi faux si l'élément existe déjà.
			return false;
		}
	}
 
	/**
	 * Cette méthode permet de modifier un élément $itemID dans la langue $lang
	 * avec le nouveau contenu $newContent.
	 */
	public function modifyXmlElement( $itemID, $lang, $newContent )
	{
		//On recherche l'élément selon sont IDentifiant.
		$item = $this->domFile->getElementById( $itemID );
		
		if ( $item != NULL )
		{
			//On récupere la liste des éléments "translation" que l'on parcours...
			foreach ( ($item->getElementsByTagName( 'translation' )) as $itemTest )
			{
				//...afin de trouver celui dont l'attribut lang est égal à celui de l'objet.
				if ( $itemTest->getAttribute( 'lang' ) == $lang )
				{
					$itemTest->nodeValue = utf8_encode($newContent);
					$this->saveFile();
					return true; //L'opération s'est bien déroulée.					
				}
			}
		}
		return false;//L'élément n'a pas été trouvé.
	}
	
	/**
	 * Méthode supprimant un élément translation dans la langue $lang, et ce dans 
	 * l'élément xmldata dont l'ID est $itemID.
	 */
	public function removeXmlElement( $itemID, $lang )
	{
		/*
		 * On doit parcourir toutes les balises et les rechercher manuellement
		 * pour pouvoir au final supprimer le noeud, ceci à cause de la fonction
		 * getElementById qui renvoi un objet  
		 */
		$nodeList = $this->domFile->getElementsByTagName( 'xmldata' );
				
		for ( $i=0; $i<$nodeList->length; $i++ )
		{
			//Recherche selon l'identifiant
			if ( utf8_decode($nodeList->item($i)->getAttribute( 'id' )) == $itemID )
			{				
				$transNode = $nodeList->item($i)->getElementsByTagName( 'translation' );
				
				//Parcours des éléments 'translation'
				for ( $j=0; $j<$transNode->length; $j++ )
				{
					//Recherche selon la langue
					if ( $transNode->item($j)->getAttribute( 'lang' ) == $lang )
					{
						$goodNode = $transNode->item($j);
						//Suppresion du noeud.
						$oldNode = $nodeList->item($i)->removeChild( $goodNode );
						$this->saveFile();
						return true;//L'opération s'est bien déroulée.
					}
				}
			}
		}
		return false;//L'élément n'a pas été trouver/supprimer.
	}
	
	/**
	 * Méthode supprimant un élément xmldata avec l'ID $itemID.
	 */
	public function completelyRemoveXmlElement( $itemID )
	{
		/*
		 * On doit parcourir toutes les balises et les rechercher manuellement
		 * pour pouvoir au final supprimer le noeud, ceci à cause de la fonction
		 * getElementById qui renvoi un objet  
		 */
		$nodeList = $this->domFile->getElementsByTagName( 'xmldata' );
				
		for ( $i=0; $i<$nodeList->length; $i++ )
		{
			//Recherche selon l'identifiant
			if ( utf8_decode($nodeList->item($i)->getAttribute( 'id' )) == $itemID )
			{				
				$goodNode = $nodeList->item($i);
				
				//Suppresion du noeud.
				$oldNode = $this->domFile->getElementsByTagName( 'language' )->item(0)->removeChild( $goodNode );
				$this->saveFile();
				return true;//L'opération s'est bien déroulée.
			}
		}
		return false;//La suppression n'a pas eu lieue.
	}
	
	/**
	 * M�thode permettant de savoir si un élément est vide ou non.
	 */
	public function isEmptyXml( $itemID, $lang )
	{
		//On recherche l'élément selon sont IDentifiant.
		$item = $this->domFile->getElementById( $itemID );
		
		if ( $item != NULL )
		{
			//On récupere la liste des éléments "translation" que l'on parcours...
			foreach ( ($item->getElementsByTagName( 'translation' )) as $itemTest )
			{
				//...afin de trouver celui dont l'attribut lang est égal à celui de l'objet.
				if ( $itemTest->getAttribute( 'lang' ) == $lang )
				{
					//On vérifie si la valeur de l'élément est vide.
					if ( $itemTest->nodeValue == "" )
					{
						return TRUE;
					}
					else
					{
						return FALSE;
					}
				}
			}
		}
		else
		{
			return false;
		}		
	}
	
	/*
	 * Méthode ajoutant des balises translation dans la langue $lang si elles
	 * n'existent pas.
	 */
	public function addLanguageToFile( $newLanguage )
	{
		$allData = $this->domFile->getElementsByTagName( 'xmldata' );
		$control = true;//Variable de controle pour insérérer ou non un nouvel élément.
		
		//On parcours tous les éléments pour vérifier s'il existe un élément de même langue
		for ( $i=0; $i<$allData->length; $i++ )
		{
			$transData = $allData->item($i)->getElementsByTagName( 'translation' );
			for ( $j=0; $j<$transData->length; $j++ )
			{
				if ( $transData->item($j)->getAttribute( 'lang' ) == $newLanguage )
				{
					$control = false;
				}				
			}
			
			if ( $control )
			{
				$newTrans = $this->domFile->createElement( 'translation', '' );
				$newTrans->setAttribute( 'lang', $newLanguage );
				$allData->item($i)->appendChild( $newTrans );
			}
			else
			{
				$control = true;
			}
		}
		$this->saveFile();//Sauvegarde du fichier
		//Cette fonction ne renvoi aucune valeur
	}
	
	/*
	 * Méthode vérifiant que tous les éléments xmldata possèdent des éléments
	 * translation dans la langue $lang, et qu'ils soient remplis.
	 */
	public function checkIntegrity( $lang )
	{
		$this->addLanguageToFile( $lang );//On rajoute la langue au cas où.
		$nodeList = $this->domFile->getElementsByTagName( 'xmldata' );
		$nodeCount = $nodeList->length;
		$nodeCounter = 0;
		
		for ( $i=0; $i<$nodeCount; $i++ )
		{			
			//...et vérifier que les éléments sont bien remplis
			if ( !$this->isEmptyXml( $nodeList->item($i)->getAttribute( 'id' ), $lang ) )
			{
				//On incrémente le compteur de variables validées.
				$nodeCounter++;
			}
		}
		
		if ( $nodeCounter == $nodeCount )
		{
			//Aucun élément vide
			return true;
		}
		else
		{
			//Si un seul élément est vide, on renvoi faux
			return false;
		}
	}
	
	/** On utilise ici la méthode magique __get qu'offre PHP afin de simplifier grandement
	 *  la sortie des données. Cette méthode sera appellée lorsque vous demanderez d'accéder
	 *  à un attribut de la classe . Elle retournera alors dans ce cas, la valeur textuelle
	 *  de la balise.
	 */
	public function __get( $itemName )
	{
		return $this->getItemValue( $itemName );
	}
 
/************************************************************************************
								METHODES PRIVEES
************************************************************************************/
 
	//Méthode privée permettant de sauvegarder le fichier XML de manière sécurisée.
	private function saveFile()
	{
		
		//On pose un verrou d'écriture pour éviter des écritures multiples pouvant créer des erreurs.
		while ( flock( $this->fileLock, LOCK_EX ) == FALSE )
		{
			//Ne rien faire en attendant que le verrou soit posé.
		}
				
		//On rajoute le mot BACKUP a la fin des fichiers pour la sauvegarde préalable.
		$backupfile = str_replace( '.xml', 'xml.backup', $this->currentFile );
		
		//Vérification avant sauvegarde pour éviter une erreur pouvant supprimer l'intégralité du fichier.		
		if ( $this->domFile->save($backupfile) != FALSE )
		{
			//Sauvegarde du fichier XML.		
			$this->domFile->save($this->currentFile);
		}
		else
		{
			die('Erreur lors de la sauvegarde du fichier XML '.$this->currentFile);
		}
		
		
		//On libére le verrou du fichier. 
		if ( !flock( $this->fileLock, LOCK_UN ) )
		{		
			die('Erreur lors de la sauvegarde du fichier XML '.$this->currentFile.'Liberation');
		}
	}
}?>