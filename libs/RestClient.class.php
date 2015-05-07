<?php
class RestClient {
	private $_url;
	private $editUrl;
	public function __construct($editUrl) {
		$this->editUrl = $editUrl;
	}
	public function setUrl($pUrl) {
		$this->_url = $pUrl;
		return $this;
	}
	public function __sleep() {
		return array (
				'editUrl' 
		);
	}
	public function get($pParams = array()) {
		$response = $this->_launch ( $this->_makeUrl ( $pParams ), $this->_createContext ( 'GET', null, "Accept: application/xml\r\n" . "Cache-Control: no-cache\r\n" ) );
		if ($response == false) {
			$error = error_get_last ();
			$this->processErrorMessage ( $error ["message"] );
		}
		return $response;
	}
	private function processErrorMessage($errorMessage) {
		if (strstr ( $errorMessage, "HTTP/1.1 404" ))
			throw new HttpNotFoundException ( $errorMessage );
	}
	public function post($pPostParams = array(), $pGetParams = array(), $accept = null, $contentType = null, $ifMatch = null) {
		$headers = "";
		if ($accept != null)
			$headers .= "Accept: " . $accept . "\r\n";
		if ($contentType != null)
			$headers .= "Content-type: " . $contentType . "\r\n";
		if ($ifMatch != null)
			$headers .= "If-Match: " . $ifMatch . "\r\n";
		$response = $this->_launch ( $this->_makeUrl ( $pGetParams ), $this->_createContext ( 'POST', $pPostParams, $headers ) );
		return $response;
	}
	public function postMultipartWithFile($pPostParams = array(), $name, $file, $accept = null, $ifMatch = null) {
		$headers = "";
		if ($accept != null)
			$headers .= "Accept: " . $accept . "\r\n";
		if ($ifMatch != null)
			$headers .= "If-Match: " . $ifMatch . "\r\n";
		$data = "";
		$boundary = "---------------------" . substr ( md5 ( rand ( 0, 32000 ) ), 0, 10 );
		$headers .= "Content-Type: multipart/form-data; boundary=" . $boundary . "\r\n";
		foreach ( $pPostParams as $key => $val ) {
			$data .= "--$boundary\n";
			$data .= "Content-Disposition: form-data; name=\"" . $key . "\"\n\n" . $val . "\n";
		}
		$data .= "--$boundary\n";
		$fileContents = file_get_contents ( $file ['tmp_name'] );
		$type = $file ['type'];
		$fname = $file ['name'];
		$data .= "Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$fname}\"\n";
		$data .= "Content-Type: {$type}\n";
		$data .= "Content-Transfer-Encoding: binary\n\n";
		$data .= $fileContents . "\n";
		$data .= "--$boundary--\n";
		$params = array (
				'http' => array (
						'method' => 'POST',
						'header' => $headers,
						'content' => $data,
						'ignore_errors' => true 
				) 
		);
		$response = $this->_launch ( $this->_makeUrl ( array () ), stream_context_create ( $params ) );
		return $response;
	}
	public function putMultipartWithXML($pPostParams = array(), $name, $fileContents, $accept = null, $ifMatch = null) {
		$headers = "";
		if ($accept != null)
			$headers .= "Accept: " . $accept . "\r\n";
		if ($ifMatch != null)
			$headers .= "If-Match: " . $ifMatch . "\r\n";
		$data = "";
		$boundary = "---------------------" . substr ( md5 ( rand ( 0, 32000 ) ), 0, 10 );
		$headers .= "Content-Type: multipart/form-data; boundary=" . $boundary . "\r\n";
		foreach ( $pPostParams as $key => $val ) {
			$data .= "--$boundary\n";
			$data .= "Content-Disposition: form-data; name=\"" . $key . "\"\n\n" . $val . "\n";
		}
		$data .= "--$boundary\n";
		$type = "application/xml";
		$fname = "xml";
		$data .= "Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$fname}\"\n";
		$data .= "Content-Type: {$type}\n";
		$data .= "Content-Transfer-Encoding: binary\n\n";
		$data .= $fileContents . "\n";
		$data .= "--$boundary--\n";
		$params = array (
				'http' => array (
						'method' => 'PUT',
						'header' => $headers,
						'content' => $data,
						'ignore_errors' => true 
				) 
		);
		$response = $this->_launch ( $this->_makeUrl ( array () ), stream_context_create ( $params ) );
		return $response;
	}
	public function put($pContent = array(), $pGetParams = array(), $accept = null, $contentType = null, $ifMatch = null) {
		$headers = "";
		if ($accept != null)
			$headers .= "Accept: " . $accept . "\r\n";
		if ($contentType != null)
			$headers .= "Content-type: " . $contentType . "\r\n";
		if ($ifMatch != null)
			$headers .= "If-Match: " . $ifMatch . "\r\n";
		$response = $this->_launch ( $this->_makeUrl ( $pGetParams ), $this->_createContext ( 'PUT', $pContent, $headers ) );
		return $response;
	}
	public function delete($pContent = array(), $pGetParams = array(), $accept = null, $contentType = null, $ifMatch = null) {
		$headers = "";
		if ($accept != null)
			$headers .= "Accept: " . $accept . "\r\n";
		if ($contentType != null)
			$headers .= "Content-type: " . $contentType . "\r\n";
		if ($ifMatch != null)
			$headers .= "If-Match: " . $ifMatch . "\r\n";
		$response = $this->_launch ( $this->_makeUrl ( $pGetParams ), $this->_createContext ( 'DELETE', $pContent, $headers ) );
		return $response;
	}
	protected function _createContext($pMethod, $pContent = null, $headers = "") {
		$opts = array (
				'http' => array (
						'method' => $pMethod,
						'header' => $headers,
						'ignore_errors' => true 
				) 
		);
		if ($pContent !== null) {
			if (is_array ( $pContent ) && array_count_values ( $pContent ) > 0) {
				$pContent = http_build_query ( $pContent );
			}
			$opts ['http'] ['content'] = $pContent;
		}
		return stream_context_create ( $opts );
	}
	protected function _makeUrl($pParams) {
		if (is_null ( $pParams ) || ! is_array ( $pParams ) || array_count_values ( $pParams ) == 0)
			return $this->_url;
		else
			return $this->_url . (strpos ( $this->_url, '?' ) === FALSE ? '?' : '') . RequestUtils::join ( $pParams );
	}
	protected function _launch($pUrl, $context) {
		$stream = @fopen ( $pUrl, 'r', false, $context );
		if (isset ( $http_response_header ))
			$header = $http_response_header;
		else
			throw new BadUrlRequestException ( $pUrl );
		if (false === $stream)
			throw new ConnexionFailureException ( $pUrl, $http_response_header );
		$content = stream_get_contents ( $stream );
		$this->checkHeader ( $header, $content, $pUrl );
		$header = stream_get_meta_data ( $stream );
		fclose ( $stream );
		return array (
				'error' => FALSE,
				'content' => $content,
				'header' => $header 
		);
	}
	protected function checkHeader($header, $content, $pUrl) {
		if ($this->responseCodeIs ( "200", $header ))
			return;
			// TODO factoriser
		if ($this->responseCodeIs ( "400", $header ))
			throw new HttpRequestException ( '400', $header, $content, $pUrl );
		if ($this->responseCodeIs ( "403", $header ))
			throw new HttpRequestException ( '403', $header, $content, $pUrl );
		if ($this->responseCodeIs ( "402", $header ))
			throw new HttpRequestException ( '402', $header, $content, $pUrl );
		if ($this->responseCodeIs ( "404", $header ))
			throw new HttpRequestException ( '404', $header, $content, $pUrl );
		if ($this->responseCodeIs ( "500", $header )) {
			throw new HttpRequestException ( '500', $header, $content, $pUrl );
		}
		if ($this->responseCodeIs ( "422", $header ))
			throw new HttpRequestException ( '422', $header, $content, $pUrl );
		if ($this->responseCodeIs ( "412", $header ))
			throw new HttpRequestException ( '412', $header, $content, $pUrl );
	}
	private function responseCodeIs($number, $header) {
		return strstr ( $header [0], $number );
	}
}
?>