<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"
	xmlns:atom="http://www.w3.org/2005/Atom" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:apiscol="http://www.crdp.ac-versailles.fr/2012/apiscol"
	exclude-result-prefixes="#default apiscol atom">
	<xsl:param name="prefix" select="/" />
	<xsl:param name="query" />
	<xsl:param name="targetmdlink" />
	<xsl:output method="html" omit-xml-declaration="yes"
		encoding="UTF-8" indent="yes" />
	<xsl:strip-space elements="*" />

	<xsl:template match="/">
		<xsl:apply-templates select="atom:feed"></xsl:apply-templates>
	</xsl:template>
	<xsl:template match="atom:feed">
		<xsl:variable name="mdid">
			<xsl:call-template name="substring-after-last">
				<xsl:with-param name="string" select="$targetmdlink" />
				<xsl:with-param name="delimiter" select="'/'" />
			</xsl:call-template>
		</xsl:variable>
		
			<xsl:if test="/atom:feed/apiscol:spellcheck/apiscol:query_term">
				<h4 class="ui-state-highlight">
					Suggestions orthographiques
					<span class="research-suggest-legend">
						<xsl:element name="img">
							<xsl:attribute name="src">
								<xsl:value-of select="$prefix"></xsl:value-of>
								<xsl:text>/img/metadata.png</xsl:text>
						</xsl:attribute>
						</xsl:element>
						<xsl:text>issue des métadonnées</xsl:text>
					</span>

					<span class="research-suggest-legend">
						<xsl:element name="img">
							<xsl:attribute name="src">
								<xsl:value-of select="$prefix"></xsl:value-of>
								<xsl:text>/img/data.png</xsl:text>
						</xsl:attribute>
						</xsl:element>
						<xsl:text>issue des données</xsl:text>
					</span>

				</h4>
			</xsl:if>
			<xsl:for-each select="/atom:feed/apiscol:spellcheck/apiscol:query_term">
				<div class="spellcheck-suggest">
					<strong>Pour le terme : </strong>
					<span class="badge badge-info">
						<xsl:value-of select="@requested"></xsl:value-of>
					</span>
					<xsl:for-each select="apiscol:word">
						<xsl:element name="a">
							<xsl:attribute name="class">ui-state-focus</xsl:attribute>
							<xsl:attribute name="href">
							<xsl:value-of select="$prefix"></xsl:value-of>
							<xsl:text>/resources/detail/</xsl:text>
							<xsl:value-of select="$mdid"></xsl:value-of>
							<xsl:text>/search?query=</xsl:text>
							<xsl:value-of select="."></xsl:value-of>
							</xsl:attribute>
							<xsl:element name="img">
								<xsl:attribute name="src">
								<xsl:value-of select="$prefix"></xsl:value-of>
								<xsl:text>/img/</xsl:text>
									<xsl:choose>
										<xsl:when test="@source">data.png</xsl:when>
										<xsl:otherwise>metadata.png</xsl:otherwise>
									 </xsl:choose>
						</xsl:attribute>
							</xsl:element>


							<xsl:value-of select="."></xsl:value-of>
						</xsl:element>

						<xsl:if test="position() != last()">
							<xsl:text>, </xsl:text>
						</xsl:if>
					</xsl:for-each>
				</div>
			</xsl:for-each>
			<xsl:if test="/atom:feed/apiscol:spellcheck/apiscol:queries/apiscol:query">
				<xsl:for-each select="/atom:feed/apiscol:spellcheck/apiscol:queries">
					<div class="spellcheck-suggest">
						<strong>Pour l'expression entière : </strong>
						<span class="badge badge-info">
							<xsl:value-of select="$query"></xsl:value-of>
						</span>
						<xsl:for-each select="apiscol:query">
							<xsl:element name="a">
								<xsl:attribute name="class">ui-state-focus</xsl:attribute>
								<xsl:attribute name="href">
							<xsl:value-of select="$prefix"></xsl:value-of>
							<xsl:text>/resources/detail/</xsl:text>
							<xsl:value-of select="$mdid"></xsl:value-of>
							<xsl:text>/search?query=</xsl:text>
							<xsl:value-of select="."></xsl:value-of>
							</xsl:attribute>
								<xsl:value-of select="."></xsl:value-of>
							</xsl:element>

							<xsl:if test="position() != last()">
								<xsl:text>, </xsl:text>
							</xsl:if>
						</xsl:for-each>
					</div>
				</xsl:for-each>
			</xsl:if>
			<xsl:choose>
				<xsl:when
					test="atom:entry/atom:link[@rel='self'][@type='text/html'][@href=$targetmdlink]">
					<xsl:apply-templates select="atom:entry"></xsl:apply-templates>
				</xsl:when>
				<xsl:otherwise>
					<h3 class="badge badge-error">
						<xsl:text>La ressource n'a pas été trouvée</xsl:text>
					</h3>
				</xsl:otherwise>
			</xsl:choose>

	</xsl:template>
	<xsl:template match="atom:entry">
		<xsl:variable name="mdlink">
			<xsl:value-of select="atom:link[@rel='self'][@type='text/html']/@href"></xsl:value-of>
		</xsl:variable>

		<xsl:if test="$mdlink=$targetmdlink">

			<h3 class="badge badge-success">
				<xsl:text>La ressource a été trouvée</xsl:text>
			</h3>
			<xsl:variable name="urn">
				<xsl:value-of select="atom:id"></xsl:value-of>
			</xsl:variable>
			<h4 class="ui-state-highlight">Correspondances dans les métadonnées :</h4>
			<xsl:choose>
				<xsl:when
					test="/atom:feed/apiscol:hits/apiscol:hit[@metadataId=$urn]/apiscol:matches/apiscol:match[not(@source)]">
					<ul>
						<xsl:for-each
							select="/atom:feed/apiscol:hits/apiscol:hit[@metadataId=$urn]/apiscol:matches/apiscol:match[not(@source)]">
							<li>
								<xsl:value-of select="." disable-output-escaping="yes"></xsl:value-of>
							</li>
						</xsl:for-each>
					</ul>
				</xsl:when>
				<xsl:otherwise>
					<p class="badge">Aucune extraction trouvée</p>
				</xsl:otherwise>
			</xsl:choose>
			<h4 class="ui-state-highlight">Correspondances dans les données :</h4>
			<xsl:choose>
				<xsl:when
					test="/atom:feed/apiscol:hits/apiscol:hit[@metadataId=$urn]/apiscol:matches/apiscol:match[@source='data']">
					<ul>
						<xsl:for-each
							select="/atom:feed/apiscol:hits/apiscol:hit[@metadataId=$urn]/apiscol:matches/apiscol:match[@source='data']">
							<li>
								<xsl:value-of select="." disable-output-escaping="yes"></xsl:value-of>
							</li>
						</xsl:for-each>
					</ul>
				</xsl:when>
				<xsl:otherwise>
					<p class="badge">Aucune extraction trouvée</p>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
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
