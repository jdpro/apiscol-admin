<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"
	xmlns:atom="http://www.w3.org/2005/Atom" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:apiscol="http://www.crdp.ac-versailles.fr/2012/apiscol"
	exclude-result-prefixes="#default">
	<xsl:param name="prefix" select="/" />
	<xsl:param name="url" />
	<xsl:param name="random" />
	<xsl:output method="html" omit-xml-declaration="yes"
		encoding="UTF-8" indent="yes" />
	<xsl:template match="/">
		<div class="present-thumb ui-widget-content">
			<span>
				Miniature actuelle
			</span>
			<xsl:apply-templates select="//apiscol:thumb[@mdid]"></xsl:apply-templates>
		</div>
		<h4>Choisissez parmi les suggestions de miniatures</h4>
		<div class="carousel-container ui-helper-clearfix">
			<a href="#" id="ui-carousel-prev"></a>
			<div id="carousel">
				<xsl:apply-templates select="//apiscol:thumb[not(@mdid)]"></xsl:apply-templates>
			</div>
			<a href="#" id="ui-carousel-next"></a>
		</div>

	</xsl:template>
	<xsl:template match="//apiscol:thumb[not(@mdid)]">
		<form class="choose-thumb" method="POST">
			<xsl:attribute name="action"><xsl:value-of select="$url"></xsl:value-of>
		</xsl:attribute>
			<xsl:element name="img">
				<xsl:attribute name="src">
					<xsl:value-of select="atom:link/@href"></xsl:value-of>
					</xsl:attribute>
			</xsl:element>
			<input type="hidden" name="choose-thumb">
				<xsl:attribute name="value">
					<xsl:value-of select="atom:link/@href"></xsl:value-of>
					</xsl:attribute>
			</input>
			<input type="submit" />
		</form>
	</xsl:template>
	<xsl:template match="//apiscol:thumb[@mdid]">
		<xsl:variable name="href" select="apiscol:link/@href"></xsl:variable>
		<xsl:variable name="error-message" select="'Pas de miniature actuellement'"></xsl:variable>
		<xsl:choose>
			<xsl:when test="string-length($href)=0">
				<xsl:value-of select="$error-message"></xsl:value-of>
			</xsl:when>
			<xsl:when test="not($href)">
				<xsl:value-of select="$error-message"></xsl:value-of>
			</xsl:when>
			<xsl:otherwise>
				<xsl:element name="img">
					<xsl:attribute name="src">
					<xsl:value-of select="$href"></xsl:value-of>?no-cache=<xsl:value-of
						select="$random"></xsl:value-of>
					</xsl:attribute>
				</xsl:element>
			</xsl:otherwise>
		</xsl:choose>

	</xsl:template>


</xsl:stylesheet>
