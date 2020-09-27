<?php
error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();

class Gemini {

	function __construct() {
		$this->data_dir = "hosts/";
		$this->default_host_dir = "default/";
	}

	function parse_request($request) {
		$data = parse_url($request);
		return $data;
	}

	function get_valid_hosts() {
		$dirs = array_map('basename', glob($this->data_dir.'*', GLOB_ONLYDIR));
		return $dirs;
	}

	function get_dir($parsed_url) {
	}
	
	function get_host_url($host) {
		if(empty($host))
			return 'default';
		
	}

	function get_status_code($filepath) {
		if(is_file($filepath) and file_exists($filepath))
			return "20";
		if(!file_exists($filepath))
			return "51";
		return "50";
	}

	function get_mime_type($filepath) {
		return "text/gemini";	
	}

	/**
	* Gets the full file path (assumes directory structure based on host)
	*
	* This function determines where the requested file is stored
	* based on the hostname supplied in the request from the client.
	* If no host is supplied, the default directory is assumed.
	*
	* @param array $url An array returned by the parse_request() method
	*
	* @return string
	*/
	function get_filepath($url) {
		$hostname = "";
		if(!is_array($url))
			return false;
		if(!empty($parsed_url['host']))
			$hostname = $parsed_url['host'];

		$valid_hosts = $this->get_valid_hosts();
		if(!in_array($hostname, $valid_hosts))
			$hostname = "default";

		// Kristall Browser is adding "__" to the end of the filenames
		// wtf am I missing?
		$url['path'] = str_replace("__", "", $url['path']);

		return $this->data_dir.$hostname.$url['path'];
	}
}

?>
