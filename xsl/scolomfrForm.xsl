<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:lom="http://ltsc.ieee.org/xsd/LOM"
	xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:lomfr="http://www.lom-fr.fr/xsd/LOMFR"
	xmlns:scolomfr="http://www.lom-fr.fr/xsd/SCOLOMFR" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:param name="prefix" select="/" />
	<xsl:param name="url" />
	<xsl:output method="html" omit-xml-declaration="yes"
		encoding="UTF-8" indent="yes" />
	<xsl:strip-space elements="*" />

	<xsl:template match="/">
		<form name="scolomfr" id="formulaire_scolomfr" method="post">
			<xsl:attribute name="action">
		<xsl:value-of select="$url"></xsl:value-of>
		</xsl:attribute>
			<div id="visualisation">
				<div class="cadre">
					<span class="cadre_icones_droite" name="cadre_icones"></span>
					<details>
						<summary class="cadre_label">Description documentaire de la
							ressource</summary>
						<div>
							<xsl:apply-templates select="/lom:lom/lom:general/lom:title"></xsl:apply-templates>
							<xsl:apply-templates select="/lom:lom/lom:general/lom:description"></xsl:apply-templates>
							<div name="element" class="element" id="keyword-container">
								<div class="elt_label">
									mot clé libre :
								</div>
								<div class="elt_champ_form">
									<ul>
										<xsl:apply-templates select="/lom:lom/lom:general/lom:keyword"></xsl:apply-templates>
									</ul>
								</div>

							</div>
							<div class="element ui-helper-clearfix  entry-list-container"
								id="generalResourceType-container">
								<xsl:call-template name="generalResourceType"></xsl:call-template>

							</div>
							<div name="element" class="element" id="contributors-container">
								<div class="elt_label">
									contributeurs :
								</div>
								<div class="elt_champ_form">
									<table>
										<xsl:apply-templates select="lom:lom/lom:lifeCycle/lom:contribute"></xsl:apply-templates>
										<tr>
											<xsl:variable name="role-vocabvalues"
												select="document('../scolomfr/xsd/scolomfr/scolomfrVocabValues.xsd')/xs:schema" />
											<xsl:variable name="lomfr-role-vocabvalues"
												select="document('../scolomfr/xsd/lomfr/lomfrVocabValues.xsd')/xs:schema" />
											<th>
												<select size="1">
													<xsl:for-each
														select="$lomfr-role-vocabvalues/*[@name='roleValues']/xs:restriction/xs:enumeration">
														<option>
															<xsl:attribute name="value">
																<xsl:value-of select="@value"></xsl:value-of>
															</xsl:attribute>
															<xsl:value-of select="@value"></xsl:value-of>
														</option>
													</xsl:for-each>
													<xsl:for-each
														select="$role-vocabvalues/*[@name='roleValues']/xs:restriction/xs:enumeration">
														<option>
															<xsl:attribute name="value">
															<xsl:value-of select="@value"></xsl:value-of>
															</xsl:attribute>
															<xsl:value-of select="@value"></xsl:value-of>
														</option>
													</xsl:for-each>
												</select>
											</th>
											<td>
											<input type="text" placeholder="Nom, prenom"></input>
											<input type="text" placeholder="Organisation"></input>
												<span class="register-entry">
													Ajouter
												</span>
											</td>
										</tr>
									</table>
								</div>

							</div>
						</div>
					</details>
				</div>
				<div class="cadre">
					<span class="cadre_icones_droite" name="cadre_icones"></span>
					<details>
						<summary class="cadre_label">Description pédagogique de la
							ressource</summary>
						<div>
							<div name="element" class="element" id="elt_droite_11">
								<div id="nb_11" class="elt_nb">0</div>
								<div class="elt_champ_form">
									<div id="ned_tree">

									</div>
									<div class="purpose" data-purpose="educational-level">
										<xsl:apply-templates
											select="/lom:lom/lom:classification[lom:purpose/lom:value='educational level']">
											<xsl:with-param name="source" select="'scolomfr-voc-022'"></xsl:with-param>
										</xsl:apply-templates>
									</div>

								</div>

							</div>
							<div class="element ui-helper-clearfix entry-list-container"
								id="intendedEndUserRole-container">
								<xsl:call-template name="intendedEndUserRole"></xsl:call-template>

							</div>
							<div class="element ui-helper-clearfix entry-list-container"
								id="learningResourceType-container">
								<xsl:call-template name="learningResourceType"></xsl:call-template>

							</div>

							<div name="element" class="element" id="elt_droite_11">
								<div id="nb_11" class="elt_nb">0</div>
								<div class="elt_champ_form">
									<div id="ens_tree">

									</div>
									<div class="purpose" data-purpose="domaine d'enseignement">
										<xsl:variable name="apos">
											<xsl:text>'</xsl:text>
										</xsl:variable>
										<xsl:apply-templates
											select="/lom:lom/lom:classification[lom:purpose/lom:value=concat('domaine d',$apos,'enseignement')]">
											<xsl:with-param name="source" select="'scolomfr-voc-015'"></xsl:with-param>
										</xsl:apply-templates>
									</div>

								</div>
							</div>

							<div name="element" class="element" id="elt_droite_11">
								<div id="nb_11" class="elt_nb">0</div>
								<div class="elt_champ_form">
									<div id="scc_tree">

									</div>
									<div class="purpose" data-purpose="competency">
										<xsl:apply-templates
											select="/lom:lom/lom:classification[lom:purpose/lom:value='competency']">
											<xsl:with-param name="source" select="'scolomfr-voc-016'"></xsl:with-param>
										</xsl:apply-templates>
									</div>

								</div>
							</div>
							<div class="element ui-helper-clearfix entry-list-container"
								id="place-container">
								<xsl:call-template name="place"></xsl:call-template>

							</div>
							<div class="element ui-helper-clearfix entry-list-container"
								id="educationalMethod-container">
								<xsl:call-template name="educationalMethod"></xsl:call-template>
							</div>
							<div class="element ui-helper-clearfix entry-list-container"
								id="activity-container">
								<xsl:call-template name="activity"></xsl:call-template>
							</div>
							<div class="element ui-helper-clearfix entry-list-container"
								id="difficulty-container">
								<xsl:call-template name="difficulty"></xsl:call-template>
							</div>
						</div>
					</details>
				</div>
			</div>
			<input type="hidden" value="update-metadata" name="update-metadata" />
			<input type="hidden" id="classifications" name="classifications" />
			<input type="submit" value="valider" />
		</form>

	</xsl:template>
	<xsl:template match="/lom:lom/lom:general/lom:title">
		<div name="element" class="element">
			<div class="elt_label_obligatoire">titre :</div>
			<div id="nb_2" class="elt_nb">0</div>

			<div id="elt_champ_form_2_0" class="elt_champ_form">
				<input name="general-title" id="general-title" type="text">
					<xsl:attribute name="value">
					 <xsl:value-of select="lom:string"></xsl:value-of>
					 </xsl:attribute>
					<xsl:attribute name="placeholder">
					 <xsl:value-of select="'Non renseigné'"></xsl:value-of>
					 </xsl:attribute>
				</input>
			</div>
		</div>
	</xsl:template>
	<xsl:template match="/lom:lom/lom:general/lom:description">
		<div name="element" class="element">
			<div class="elt_label_obligatoire">
				description :
			</div>
			<div class="elt_nb">0</div>
			<div class="elt_champ_form">
				<textarea name="general-description" id="general-description"
					cols="100" rows="7">
					<xsl:attribute name="placeholder">
					 <xsl:value-of select="'Non renseigné'"></xsl:value-of>
					 </xsl:attribute>
					<xsl:value-of select="lom:string"></xsl:value-of>
				</textarea>
			</div>
		</div>
	</xsl:template>
	<xsl:template match="/lom:lom/lom:general/lom:keyword">

		<li>
			<xsl:value-of select="lom:string"></xsl:value-of>
		</li>

	</xsl:template>
	<xsl:template match="/lom:lom/lom:classification">
		<xsl:param name="source"></xsl:param>
		<div class="source-data" data-source="{$source}">
			<xsl:apply-templates
				select="lom:taxonPath[lom:source/lom:string=$source]/lom:taxon">

			</xsl:apply-templates>
		</div>
	</xsl:template>
	<xsl:template match="lom:taxonPath/lom:taxon">
		<div class="entry-data">
			<xsl:attribute name="data-id"><xsl:value-of select="lom:id"></xsl:value-of></xsl:attribute>
			<xsl:value-of select="lom:entry/lom:string"></xsl:value-of>
		</div>
	</xsl:template>
	<xsl:template name="generalResourceType">
		<div class="elt_label">
			typologie générale de documents :
		</div>
		<xsl:variable name="grt-vocabvalues"
			select="document('../scolomfr/xsd/scolomfr/scolomfrVocabValues.xsd')/xs:schema" />
		<div>
			<select size="1">
				<xsl:for-each
					select="$grt-vocabvalues/*[@name='generalResourceTypeValues']/xs:restriction/xs:enumeration">
					<option>
						<xsl:attribute name="value">
				<xsl:value-of select="@value"></xsl:value-of>
				</xsl:attribute>
						<xsl:value-of select="@value"></xsl:value-of>
					</option>
				</xsl:for-each>
			</select>
			<span class="register-entry">
				Ajouter
			</span>
			<span class="ui-state-error ui-corner-all duplicate-alert">Attention : doublon</span>
		</div>
		<div class="entries-container">
			<div class="elt_champ_form">
				<ul>
					<xsl:for-each select="/lom:lom/lom:general/scolomfr:generalResourceType">
						<li>
							<xsl:value-of select="scolomfr:value"></xsl:value-of>
						</li>
					</xsl:for-each>
				</ul>
			</div>
		</div>

	</xsl:template>
	<xsl:template name="learningResourceType">
		<div class="elt_label">
			Type pédagogique de la ressource
		</div>
		<xsl:variable name="lrt-vocabvalues"
			select="document('../scolomfr/xsd/scolomfr/scolomfrVocabValues.xsd')/xs:schema" />
		<div>
			<select size="1">
				<xsl:for-each
					select="$lrt-vocabvalues/*[@name='learningResourceTypeValues']/xs:restriction/xs:enumeration">
					<option>
						<xsl:attribute name="value">
				<xsl:value-of select="@value"></xsl:value-of>
				</xsl:attribute>
						<xsl:value-of select="@value"></xsl:value-of>
					</option>
				</xsl:for-each>
			</select>
			<span class="register-entry">
				Ajouter
			</span>
			<span class="ui-state-error ui-corner-all duplicate-alert">Attention : doublon</span>
		</div>
		<div class="entries-container">
			<div class="elt_champ_form">
				<ul>
					<xsl:for-each select="/lom:lom/lom:educational/lom:learningResourceType">
						<li>
							<xsl:value-of select="lom:value"></xsl:value-of>
						</li>
					</xsl:for-each>
				</ul>
			</div>
		</div>

	</xsl:template>
	<xsl:template name="place">
		<div class="elt_label">
			Lieu
		</div>
		<xsl:variable name="place-vocabvalues"
			select="document('../scolomfr/xsd/scolomfr/scolomfrVocabValues.xsd')/xs:schema" />
		<div>
			<select size="1">
				<xsl:for-each
					select="$place-vocabvalues/*[@name='placeValues']/xs:restriction/xs:enumeration">
					<option>
						<xsl:attribute name="value">
				<xsl:value-of select="@value"></xsl:value-of>
				</xsl:attribute>
						<xsl:value-of select="@value"></xsl:value-of>
					</option>
				</xsl:for-each>
			</select>
			<span class="register-entry">
				Ajouter
			</span>
			<span class="ui-state-error ui-corner-all duplicate-alert">Attention : doublon</span>
		</div>
		<div class="entries-container">
			<div class="elt_champ_form">
				<ul>
					<xsl:for-each select="/lom:lom/lom:educational/scolomfr:place">
						<li>
							<xsl:value-of select="scolomfr:value"></xsl:value-of>
						</li>
					</xsl:for-each>
				</ul>
			</div>
		</div>

	</xsl:template>
	<xsl:template name="intendedEndUserRole">
		<div class="elt_label">
			Public cible
		</div>
		<xsl:variable name="intendedEndUserRole-vocabvalues"
			select="document('../scolomfr/xsd/common/vocabValues.xsd')/xs:schema" />
		<div>
			<select size="1">
				<xsl:for-each
					select="$intendedEndUserRole-vocabvalues/*[@name='intendedEndUserRoleValues']/xs:restriction/xs:enumeration">
					<option>
						<xsl:attribute name="value">
				<xsl:value-of select="@value"></xsl:value-of>
				</xsl:attribute>
						<xsl:value-of select="@value"></xsl:value-of>
					</option>
				</xsl:for-each>
			</select>
			<span class="register-entry">
				Ajouter
			</span>
			<span class="ui-state-error ui-corner-all duplicate-alert">Attention : doublon</span>
		</div>
		<div class="entries-container">
			<div class="elt_champ_form">
				<ul>
					<xsl:for-each select="/lom:lom/lom:educational/lom:intendedEndUserRole">
						<li>
							<xsl:value-of select="lom:value"></xsl:value-of>
						</li>
					</xsl:for-each>
				</ul>
			</div>
		</div>

	</xsl:template>
	<xsl:template name="educationalMethod">
		<div class="elt_label">
			Modalité pédagogique
		</div>
		<xsl:variable name="educationalMethod-vocabvalues"
			select="document('../scolomfr/xsd/scolomfr/scolomfrVocabValues.xsd')/xs:schema" />
		<div>
			<select size="1">
				<xsl:for-each
					select="$educationalMethod-vocabvalues/*[@name='educationalMethodValues']/xs:restriction/xs:enumeration">
					<option>
						<xsl:attribute name="value">
				<xsl:value-of select="@value"></xsl:value-of>
				</xsl:attribute>
						<xsl:value-of select="@value"></xsl:value-of>
					</option>
				</xsl:for-each>
			</select>
			<span class="register-entry">
				Ajouter
			</span>
			<span class="ui-state-error ui-corner-all duplicate-alert">Attention : doublon</span>
		</div>
		<div class="entries-container">
			<div class="elt_champ_form">
				<ul>
					<xsl:for-each select="/lom:lom/lom:educational/scolomfr:educationalMethod">
						<li>
							<xsl:value-of select="scolomfr:value"></xsl:value-of>
						</li>
					</xsl:for-each>
				</ul>
			</div>
		</div>

	</xsl:template>
	<xsl:template name="activity">
		<div class="elt_label">
			Activités induites
		</div>
		<xsl:variable name="activity-vocabvalues"
			select="document('../scolomfr/xsd/scolomfr/scolomfrVocabValues.xsd')/xs:schema" />
		<xsl:variable name="lomfr-activity-vocabvalues"
			select="document('../scolomfr/xsd/lomfr/lomfrVocabValues.xsd')/xs:schema" />
		<div>
			<select size="1">
				<xsl:for-each
					select="$lomfr-activity-vocabvalues/*[@name='activityValues']/xs:restriction/xs:enumeration">
					<option>
						<xsl:attribute name="value">
				<xsl:value-of select="@value"></xsl:value-of>
				</xsl:attribute>
						<xsl:value-of select="@value"></xsl:value-of>
					</option>
				</xsl:for-each>
				<xsl:for-each
					select="$activity-vocabvalues/*[@name='activityValues']/xs:restriction/xs:enumeration">
					<option>
						<xsl:attribute name="value">
				<xsl:value-of select="@value"></xsl:value-of>
				</xsl:attribute>
						<xsl:value-of select="@value"></xsl:value-of>
					</option>
				</xsl:for-each>
			</select>
			<span class="register-entry">
				Ajouter
			</span>
			<span class="ui-state-error ui-corner-all duplicate-alert">Attention : doublon</span>
		</div>
		<div class="entries-container">
			<div class="elt_champ_form">
				<ul>
					<xsl:for-each select="/lom:lom/lom:educational/lomfr:activity">
						<li>
							<xsl:value-of select="lomfr:value"></xsl:value-of>
						</li>
					</xsl:for-each>
				</ul>
			</div>
		</div>

	</xsl:template>
	<xsl:template name="difficulty">
		<div class="elt_label">
			Difficulté
		</div>
		<xsl:variable name="difficulty-vocabvalues"
			select="document('../scolomfr/xsd/common/vocabValues.xsd')/xs:schema" />
		<xsl:variable name="registredValue"
			select="/lom:lom/lom:educational/lom:difficulty/lom:value" />
		<div>
			<select size="1" name="educational-difficulty">
				<option value="none">

					Non renseigné</option>
				<xsl:for-each
					select="$difficulty-vocabvalues/*[@name='difficultyValues']/xs:restriction/xs:enumeration">

					<option>
						<xsl:attribute name="value">
				<xsl:value-of select="@value"></xsl:value-of>
				</xsl:attribute>
						<xsl:if test="@value=$registredValue">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="@value"></xsl:value-of>
					</option>
				</xsl:for-each>
			</select>
		</div>


	</xsl:template>
	<xsl:template match="lom:lom/lom:lifeCycle/lom:contribute">
		<xsl:param name="mode"></xsl:param>
		<xsl:variable name="role">

			<xsl:value-of select="lom:role/lom:value"></xsl:value-of>
		</xsl:variable>
		<xsl:variable name="date">
			<xsl:value-of select="lom:date/lom:dateTime"></xsl:value-of>
		</xsl:variable>

		<xsl:element name="tr" namespace="">
			<xsl:attribute name="class">
								<xsl:value-of select="'role'"></xsl:value-of>
							</xsl:attribute>
			<xsl:element name="th" namespace="">
				<xsl:attribute name="class">
								<xsl:value-of select="'role-label'"></xsl:value-of>
							</xsl:attribute>
				<xsl:value-of select="$role"></xsl:value-of>

			</xsl:element>
			<xsl:element name="td" namespace="">
				<xsl:attribute name="class">
								<xsl:value-of select="'vcard-string'"></xsl:value-of>
							</xsl:attribute>
				<xsl:value-of select="lom:entity"></xsl:value-of>


			</xsl:element>

		</xsl:element>

	</xsl:template>
</xsl:stylesheet>
