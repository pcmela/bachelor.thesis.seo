<?php
/** Convenient API for asynchronous HTTP connections using CURL in PHP 5+
* @link http://github.com/vrana/curl-async
* @author Jakub Vrana, http://www.vrana.cz/
* @copyright 2010 Jakub Vrana
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
*/
namespace mixed;

class CurlAsync{
	/** @var float number of seconds to wait in retrieving data */
	public $timeout = 1.0;
	protected $multi;
	protected $curl = array();
	protected $done = array();
	
	/** Initialize CURL
	*/
	function __construct() {
		$this->multi = curl_multi_init();
	}
	
	/** Close CURL
	*/
	function __destruct() {
		curl_multi_close($this->multi);
	}
	
	/** Execute request or get its response
	* @param string request identifier
	* @param array array(string $url) for executing request, array() for getting response
	* @return mixed
	*/
	function __call($name, array $args) {
		if ($args) { // execute request
			list($url) = $args;
			$curl = curl_init($url);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHeaders());
                        curl_setopt($curl, CURLOPT_HEADER, 0);

                        curl_setopt($curl, CURLOPT_USERAGENT, $this->getUserAgent());

                        curl_setopt($curl, CURLOPT_ENCODING, $this->getCompression());

			$this->curl[$name] = $curl;
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$return = curl_multi_add_handle($this->multi, $curl);
			while (curl_multi_exec($this->multi, $running) == CURLM_CALL_MULTI_PERFORM) {
			}
			return $return;
		}
		
		// get response
		if (!isset($this->curl[$name])) { // wrong identifier
			return false;
		}
		$curl = $this->curl[$name];
		
		while (!isset($this->done[(int) $curl])) {
			curl_multi_select($this->multi, $this->timeout);
			while (curl_multi_exec($this->multi, $running) == CURLM_CALL_MULTI_PERFORM) {
			}
			while ($info = curl_multi_info_read($this->multi)) {
				if ($info["msg"] == CURLMSG_DONE) {
					$this->done[(int) $info["handle"]] = true;
				}
			}
		}
		
		return curl_multi_getcontent($curl);
	}

        private function getHeaders(){
            $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
            $headers[] = 'Connection: Keep-Alive';
            $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
            //$user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';

            return $headers;
        }

        private function getUserAgent(){
            return 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';
        }

        private function getCompression(){
            return 'gzip';
        }

}
