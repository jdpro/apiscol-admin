<?php
class HttpRequestException extends Exception
{
	private $url;
	private $headers;
	private $content;

	public function __construct($code, $headers, $content, $url) {
		$this->url=$url;
		$this->headers=$headers;
		$this->content=$content;
		//debug_print_backtrace();
		parent::__construct(implode("//", $headers), $code);
	}

	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message} / {$this->url}\n\n";
	}
	public function getHeaders() {
		return $this->headers;
	}

	public function getContent() {

		if (@simplexml_load_string($this->content)) {
			$dom = new DOMDocument();
			$dom->loadXML($this->content);
			return $dom->getElementsByTagName("message")->item(0)->textContent;
		} else return strip_tags($this->content);
	}
}
?>