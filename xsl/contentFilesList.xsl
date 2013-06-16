<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"
	xmlns:atom="http://www.w3.org/2005/Atom" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:apiscol="http://www.crdp.ac-versailles.fr/2012/apiscol"
	exclude-result-prefixes="#default">
	<xsl:param name="prefix" select="/" />
	<xsl:param name="url" select="/" />
	<xsl:output method="html" omit-xml-declaration="yes"
		encoding="UTF-8" indent="yes" />
	<xsl:template match="/">

		<ul class="files-list">
			<xsl:attribute name="data-src">
		<xsl:value-of select="$url"></xsl:value-of>/file-list/async
		</xsl:attribute>
			<xsl:apply-templates
				select="/atom:entry/atom:content/apiscol:files/apiscol:file">

				<xsl:with-param name="main">
					<xsl:value-of select="/atom:entry/atom:content/apiscol:files/@main"></xsl:value-of>
				</xsl:with-param>
			</xsl:apply-templates>
		</ul>

	</xsl:template>

	<xsl:template match="/atom:entry/atom:content/apiscol:files/apiscol:file">
		<xsl:param name="main"></xsl:param>
		<li>
			<xsl:choose>
				<xsl:when test="atom:title=$main">
					<img>
						<xsl:attribute name="src">
				<xsl:value-of select="$prefix"></xsl:value-of>/img/main.png
				</xsl:attribute>
					</img>
				</xsl:when>
				<xsl:otherwise>
					<span class="do-main-file">
						<form method="POST">
							<xsl:attribute name="action">
							<xsl:value-of select="$url"></xsl:value-of>/edit
							</xsl:attribute>
							<input type="hidden" name="file-action" value="do-main" />
							<input type="hidden" name="fname">
								<xsl:attribute name="value">
									<xsl:value-of select="atom:title"></xsl:value-of>
								</xsl:attribute>
							</input>
							<input type="submit" value="principal" />
						</form>
					</span>
				</xsl:otherwise>
			</xsl:choose>
			<a>
				<xsl:attribute name="href">
					<xsl:value-of select="atom:link/@href"></xsl:value-of>
				</xsl:attribute>
				télécharger
			</a>

			<span class="delete-file">
				<form method="POST">
					<xsl:attribute name="action">
							<xsl:value-of select="$url"></xsl:value-of>/edit
							</xsl:attribute>
					<input type="hidden" name="file-action" value="delete" />
					<input type="hidden" name="fname">
						<xsl:attribute name="value">
									<xsl:value-of select="atom:title"></xsl:value-of>
								</xsl:attribute>
					</input>
					<input type="submit" value="supprimer" />
				</form>
			</span>
			<span class="file-title">
				<xsl:value-of select="atom:title"></xsl:value-of>
			</span>
		</li>
	</xsl:template>

</xsl:stylesheet>
