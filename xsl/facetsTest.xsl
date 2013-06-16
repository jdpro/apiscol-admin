<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"
	xmlns:atom="http://www.w3.org/2005/Atom" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:apiscol="http://www.crdp.ac-versailles.fr/2012/apiscol"
	exclude-result-prefixes="#default apiscol atom">
	<xsl:param name="prefix" select="/" />
	<xsl:output method="html" omit-xml-declaration="yes"
		encoding="UTF-8" indent="yes" />
	<xsl:strip-space elements="*" />

	<xsl:template match="/">
		<div class="facets-results">
			<xsl:apply-templates select="atom:feed"></xsl:apply-templates>
		</div>
	</xsl:template>
	<xsl:template match="atom:feed">
		<xsl:apply-templates select="apiscol:facets/apiscol:static-facets"></xsl:apply-templates>
		<xsl:apply-templates select="apiscol:facets/apiscol:dynamic-facets"></xsl:apply-templates>
	</xsl:template>
	<xsl:template match="apiscol:facets/apiscol:static-facets">
		<xsl:if test="apiscol:facet">
			<ul>
				<li>
					<h4>
						<xsl:value-of select="@name"></xsl:value-of>
					</h4>
					<ul>
						<xsl:for-each select="apiscol:facet">
							<li>
								<xsl:value-of select="."></xsl:value-of>
							</li>
						</xsl:for-each>
					</ul>
				</li>
			</ul>
		</xsl:if>
	</xsl:template>
	<xsl:template match="apiscol:facets/apiscol:dynamic-facets">
		<xsl:if test="apiscol:taxon">
			<ul>
				<xsl:apply-templates select="apiscol:taxon"></xsl:apply-templates>
			</ul>
		</xsl:if>

	</xsl:template>
	<xsl:template match="apiscol:taxon">
		<li>
			<xsl:variable name="identifier">
				<xsl:value-of select="@identifier"></xsl:value-of>
			</xsl:variable>
			<xsl:variable name="name">
				<xsl:value-of select="../@name"></xsl:value-of>
			</xsl:variable>
			<h4>
				<xsl:value-of select="$identifier"></xsl:value-of>
			</h4>
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


			<xsl:value-of select="@label"></xsl:value-of>


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





</xsl:stylesheet>
