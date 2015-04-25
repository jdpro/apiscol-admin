<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"
	xmlns:atom="http://www.w3.org/2005/Atom" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:apiscol="http://www.crdp.ac-versailles.fr/2012/apiscol"
	exclude-result-prefixes="#default apiscol atom">
	<xsl:param name="prefix" select="/" />
	<xsl:param name="currentPage" select="0" />
	<xsl:param name="rowsPerPage" />
	<xsl:param name="query" />
	<xsl:param name="write_permission" select="false()" />
	<xsl:output method="html" omit-xml-declaration="yes"
		encoding="UTF-8" indent="yes" />
	<xsl:strip-space elements="*" />

	<xsl:template match="/">
		<xsl:apply-templates select="atom:feed"></xsl:apply-templates>
	</xsl:template>
	<xsl:template match="atom:feed">
		<div class="pane ui-layout-center">
			<div class="inner-layout">
				<div class="ui-layout-center">
					<table>
						<thead>
							<tr>
								<th>
									<input type="checkbox" class="css-checkbox" id="select-all-resources" />
									<label class="css-label" for="select-all-resources">{RESOURCES-LIST-TITLE}
									</label>
								</th>
								<th>
								</th>
								<th>
									<span class="refresh-selection-button"></span>
								</th>
								<th>
									<span class="delete-selection-button"></span>
								</th>
							</tr>
						</thead>
						<tbody>
							<xsl:apply-templates select="atom:entry"></xsl:apply-templates>
						</tbody>
					</table>
				</div>
				<div class="ui-layout-south">
					<xsl:call-template name="pagination">
						<xsl:with-param name="length" select="/atom:feed/apiscol:length" />
						<xsl:with-param name="step" select="0" />
					</xsl:call-template>
				</div>
			</div>
		</div>
		<div class="pane ui-layout-west">
			<div class="query-container">
				<form method="GET">
					<xsl:attribute name="action">					
					<xsl:value-of select="$prefix"></xsl:value-of>/resources/list</xsl:attribute>
					<input type="text" name="query" id="query">
						<xsl:attribute name="placeholder">{RESOURCES-LIST-RESEARCH}</xsl:attribute>
						<xsl:attribute name="data-suggest">
					<xsl:value-of select="$prefix"></xsl:value-of>/query-suggest
					</xsl:attribute>
						<xsl:attribute name="value">
					<xsl:value-of select="$query"></xsl:value-of>
					</xsl:attribute>

					</input>
				</form>
			</div>
			<div id="facets-container">
				<div id="facets" class="basic">
					<xsl:apply-templates select="apiscol:facets/apiscol:dynamic-facets"></xsl:apply-templates>
					<xsl:apply-templates select="apiscol:facets/apiscol:static-facets"></xsl:apply-templates>

				</div>
			</div>
		</div>
		<div class="pane ui-layout-north">
			[CONTROLS]
			<button id="rows-per-page-open">
				<xsl:value-of select="$rowsPerPage"></xsl:value-of>
				{RESOURCES-LIST-RESULTS-PER-PAGE}
			</button>
			<ul id="rows-per-page">
				<xsl:call-template name="number-of-rows">
					<xsl:with-param name="rows">
						10
					</xsl:with-param>
				</xsl:call-template>
				<xsl:call-template name="number-of-rows">
					<xsl:with-param name="rows">
						20
					</xsl:with-param>
				</xsl:call-template>
				<xsl:call-template name="number-of-rows">
					<xsl:with-param name="rows">
						50
					</xsl:with-param>
				</xsl:call-template>
				<xsl:call-template name="number-of-rows">
					<xsl:with-param name="rows">
						100
					</xsl:with-param>
				</xsl:call-template>

			</ul>
		</div>
	</xsl:template>
	<xsl:template match="atom:entry">
		<xsl:variable name="mdid">
			<xsl:call-template name="substring-after-last">
				<xsl:with-param name="string" select="atom:id" />
				<xsl:with-param name="delimiter" select="':'" />
			</xsl:call-template>
		</xsl:variable>
		<tr>
			<td>
				<input type="checkbox" class="css-checkbox">
					<xsl:attribute name="id"><xsl:value-of
						select="$mdid"></xsl:value-of></xsl:attribute>
				</input>

				<label class="css-label">
					<xsl:attribute name="for"><xsl:value-of
						select="$mdid"></xsl:value-of></xsl:attribute>
					<a>
						<xsl:attribute name="href">
					
					<xsl:value-of select="$prefix"></xsl:value-of>/resources/detail/
					<xsl:call-template name="substring-after-last">
  						<xsl:with-param name="string"
							select="atom:link[@rel='self'][@type='text/html']/@href" />
  						<xsl:with-param name="delimiter" select="'/'" />
					</xsl:call-template>/display
				</xsl:attribute>
						<xsl:value-of select="atom:title"></xsl:value-of>
					</a>
				</label>
			</td>
			<td>
				<xsl:variable name="category">
					<xsl:value-of select="atom:category/@term"></xsl:value-of>
				</xsl:variable>
				<xsl:element name="img">
					<xsl:attribute name="src">
					<xsl:value-of select="$prefix"></xsl:value-of>
					<xsl:text>/img/</xsl:text>
					<xsl:choose>
					<xsl:when test="$category='learning object'">
					<xsl:text>aggregation_level_learning_object.png</xsl:text>
					</xsl:when>
					<xsl:when test="$category='lesson'">
					<xsl:text>aggregation_level_lesson.png</xsl:text>
					</xsl:when>
					<xsl:otherwise><xsl:text>aggregation_level_unkhnown.png</xsl:text></xsl:otherwise>
					</xsl:choose>
					</xsl:attribute>
					<xsl:attribute name="title">
					<xsl:choose>
					<xsl:when test="$category='learning object'">
					<xsl:text>{RESOURCES-LIST-AGGREGATION-LEVEL-LEARNING-OBJECT}</xsl:text>
					</xsl:when>
					<xsl:when test="$category='lesson'">
					<xsl:text>{RESOURCES-LIST-AGGREGATION-LEVEL-LESSON}</xsl:text>
					</xsl:when>
					<xsl:otherwise>
					<xsl:text>{RESOURCES-LIST-AGGREGATION-LEVEL-UNKNOWN}</xsl:text></xsl:otherwise>
					</xsl:choose>
					</xsl:attribute>
				</xsl:element>
			</td>
			<td>
				<xsl:element name="a">
					<xsl:attribute name="class">
				resource-download-link
				</xsl:attribute>
					<xsl:attribute name="href">
					<xsl:value-of select="atom:content/@src"></xsl:value-of>
				</xsl:attribute>
					Accéder
				</xsl:element>

			</td>
			<xsl:choose>
				<xsl:when test="$write_permission">
					<td>
						<xsl:element name="form">
							<xsl:attribute name="class">refresh-control</xsl:attribute>
							<xsl:attribute name="method">POST</xsl:attribute>
							<xsl:attribute name="action">					
					<xsl:value-of select="$prefix"></xsl:value-of>/resources/list					
				</xsl:attribute>
							<input type="hidden" name="refresh-resource">
								<xsl:attribute name="value">
							<xsl:value-of select="$mdid"></xsl:value-of>
							</xsl:attribute>
							</input>
							<input type="submit" value="mettre à jour" />
						</xsl:element>
					</td>
					<td>
						<xsl:element name="form">
							<xsl:attribute name="class">delete-control</xsl:attribute>
							<xsl:attribute name="method">POST</xsl:attribute>
							<xsl:attribute name="action">					
					<xsl:value-of select="$prefix"></xsl:value-of>/resources/list
					
				</xsl:attribute>
							<input type="hidden" name="delete-resource">
								<xsl:attribute name="value">
							<xsl:value-of select="$mdid"></xsl:value-of>
							</xsl:attribute>
							</input>
							<input type="submit" value="supprimer" />
						</xsl:element>

					</td>
				</xsl:when>

			</xsl:choose>

		</tr>
	</xsl:template>
	<xsl:template name="number-of-rows">
		<xsl:param name="rows"></xsl:param>
		<li>
			<a class="rows-per-page">
				<xsl:attribute name="href">					
					<xsl:value-of select="$prefix"></xsl:value-of><xsl:text>/resources/list/start/0/rows/</xsl:text><xsl:value-of
					select="normalize-space($rows)"></xsl:value-of></xsl:attribute>
				<xsl:value-of select="$rows"></xsl:value-of>
				{RESOURCES-LIST-RESULTS}
			</a>
		</li>
	</xsl:template>
	<xsl:template match="apiscol:facets/apiscol:dynamic-facets">
		<xsl:apply-templates select="apiscol:taxon">
			<xsl:with-param name="purpose" select="@name"></xsl:with-param>
		</xsl:apply-templates>
	</xsl:template>
	<xsl:template match="apiscol:taxon">
		<xsl:param name="purpose"></xsl:param>
		<xsl:variable name="identifier">
			<xsl:value-of select="@identifier"></xsl:value-of>
		</xsl:variable>
		<xsl:variable name="name">
			<xsl:value-of select="../@name"></xsl:value-of>
		</xsl:variable>
		<h3>
			<a href="#">
				<xsl:value-of select="concat($purpose,' : ',$identifier  )"></xsl:value-of>
			</a>
		</h3>
		<div>
			<ul>
				<xsl:for-each select="apiscol:entry">
					<xsl:call-template name="taxon-entry">
						<xsl:with-param name="identifier" select="$identifier"></xsl:with-param>
						<xsl:with-param name="name" select="$name"></xsl:with-param>
					</xsl:call-template>
				</xsl:for-each>
			</ul>
		</div>
	</xsl:template>
	<xsl:template name="taxon-entry">
		<xsl:param name="identifier"></xsl:param>
		<xsl:param name="name"></xsl:param>
		<xsl:variable name="href">
			<xsl:value-of select="$prefix"></xsl:value-of>
			<xsl:text>/resources/list/dynamic-filter/[</xsl:text>
			<xsl:value-of select="$name"></xsl:value-of>
			<xsl:text>::</xsl:text>
			<xsl:value-of select="$identifier"></xsl:value-of>
			<xsl:text>::</xsl:text>
			<xsl:value-of select="@identifier"></xsl:value-of>
			<xsl:text>::</xsl:text>
			<xsl:value-of select="@label"></xsl:value-of>
			<xsl:text>]</xsl:text>
		</xsl:variable>
		<li>
			<xsl:element name="a">
				<xsl:attribute name="href">
				   <xsl:call-template name="string-replace-all">
				    <xsl:with-param name="text" select="$href" />
				    <xsl:with-param name="replace" select="'?'" />
				    <xsl:with-param name="by" select="'%3f'" />
				  </xsl:call-template>				
				</xsl:attribute>
				<xsl:value-of select="@label"></xsl:value-of>
				(
				<xsl:value-of select="@count"></xsl:value-of>
				)
			</xsl:element>
			<ul>
				<xsl:for-each select="apiscol:entry">
					<xsl:call-template name="taxon-entry">
						<xsl:with-param name="identifier" select="$identifier"></xsl:with-param>
						<xsl:with-param name="name" select="$name"></xsl:with-param>
					</xsl:call-template>
				</xsl:for-each>
			</ul>
		</li>

	</xsl:template>
	<xsl:template match="apiscol:facets/apiscol:static-facets">
		<xsl:variable name="name">
			<xsl:value-of select="@name"></xsl:value-of>
		</xsl:variable>
		<xsl:if test="apiscol:facet">
			<h3>
				<a href="#">
					<xsl:value-of select="$name"></xsl:value-of>
				</a>
			</h3>
			<div>
				<ul>
					<xsl:apply-templates select="apiscol:facet">
						<xsl:with-param name="name" select="$name"></xsl:with-param>
					</xsl:apply-templates>
				</ul>
			</div>
		</xsl:if>


	</xsl:template>
	<xsl:template match="apiscol:facet">
		<xsl:param name="name"></xsl:param>
		<li>
			<xsl:element name="a">
				<xsl:attribute name="href">
				<xsl:value-of select="$prefix"></xsl:value-of>
				<xsl:text>/resources/list/static-filter/[</xsl:text>
				<xsl:value-of select="$name"></xsl:value-of>
					<xsl:text>::</xsl:text>
					<xsl:value-of select="."></xsl:value-of>
					<xsl:text>]</xsl:text>
				</xsl:attribute>
				<xsl:value-of select="."></xsl:value-of>
				<xsl:text>(</xsl:text>
				<xsl:value-of select="@count"></xsl:value-of>
				<xsl:text>)</xsl:text>
			</xsl:element>
		</li>
	</xsl:template>
	<xsl:template name="pagination">
		<xsl:param name="step"></xsl:param>
		<xsl:param name="length"></xsl:param>
		<xsl:choose>
			<xsl:when test="$step=$currentPage">
				<xsl:element name="span">
					<xsl:attribute name="class">
			<xsl:value-of select="'pagination'"></xsl:value-of>
			<xsl:value-of select="' current-page'"></xsl:value-of>
			</xsl:attribute>
					<xsl:value-of select="$step+1"></xsl:value-of>
				</xsl:element>
			</xsl:when>
			<xsl:otherwise>
				<xsl:element name="a">
					<xsl:attribute name="class">
			<xsl:value-of select="'pagination'"></xsl:value-of>
			</xsl:attribute>

					<xsl:attribute name="href">
			<xsl:value-of select="$prefix"></xsl:value-of>/resources/list/start/<xsl:value-of
						select="$step*$rowsPerPage"></xsl:value-of>/rows/<xsl:value-of
						select="$rowsPerPage"></xsl:value-of>
		</xsl:attribute>
					<xsl:value-of select="$step+1"></xsl:value-of>
				</xsl:element>
			</xsl:otherwise>
		</xsl:choose>


		&#0160;
		<xsl:choose>
			<xsl:when test="(($step+1)*$rowsPerPage)>=$length"></xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="pagination">
					<xsl:with-param name="length" select="$length" />
					<xsl:with-param name="step" select="$step+1" />
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="substring-after-last">
		<xsl:param name="string" />
		<xsl:param name="delimiter" />
		<xsl:choose>
			<xsl:when test="contains($string, $delimiter)">
				<xsl:call-template name="substring-after-last">
					<xsl:with-param name="string"
						select="substring-after($string, $delimiter)" />
					<xsl:with-param name="delimiter" select="$delimiter" />
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$string" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="string-replace-all">
		<xsl:param name="text" />
		<xsl:param name="replace" />
		<xsl:param name="by" />
		<xsl:choose>
			<xsl:when test="contains($text, $replace)">
				<xsl:value-of select="substring-before($text,$replace)" />
				<xsl:value-of select="$by" />
				<xsl:call-template name="string-replace-all">
					<xsl:with-param name="text"
						select="substring-after($text,$replace)" />
					<xsl:with-param name="replace" select="$replace" />
					<xsl:with-param name="by" select="$by" />
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$text" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


</xsl:stylesheet>
