<?php
error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();

class Gemini {

	function __construct($config) {
		if(empty($config['certificate_file']))
			die("Missing certificate file.  Edit config.php");
		$this->ip = "0";
		$this->port = "1965";
		$this->data_dir = "hosts/";
		$this->default_host_dir = "default/";
		$this->default_index_file = "index.gemini";
		$this->logging = 1;
		$this->log_file = "logs/gemini-php.log";
		$this->log_sep = "\t";
		$settings = array('ip', 'port', 'data_dir', 'default_host_dir', 'default_index_file',
				'certificate_file', 'certificate_passphrase');
		foreach($settings as $setting_key) {
			if(!empty($config[$setting_key]))
				$this->$setting_key = $config[$setting_key];
		}
		// append the required filepath slashes if they're missing
		if(substr($this->data_dir, -1) != "/")
			$this->data_dir .= "/";
		if(substr($this->default_host_dir, -1) != "/")
			$this->default_host_dir .= "/";
		if($this->logging) {
			if(!file_exists($this->log_file)) {
				$this->log_to_file("Log created", null, null, null, null);
			}
			if(!is_writable($this->log_file)) {
				die("{$this->log_file} is not writable.");
			}
		}
		if(!is_readable($this->certificate_file))
			die("Certificate file {$this->certificate_file} not readable.");
	}

	function parse_request($request) {
		$data = parse_url($request);
		return $data;
	}

	function get_valid_hosts() {
		$dirs = array_map('basename', glob($this->data_dir.'*', GLOB_ONLYDIR));
		return $dirs;
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
		$type = mime_content_type($filepath);
		// we need a way to detect gemini file types, which PHP doesn't
		// so.. if it ends with gemini (or if it has no extension), assume
		$path_parts = pathinfo($filepath);
		if(empty($path_parts['extension']) or $path_parts['extension'] == "gemini")
			$type = "text/gemini";
		return $type;
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
		if($url['path'] == "" or $url['path'] == "/")
			$url['path'] = "/".$this->default_index_file;

		return $this->data_dir.$hostname.$url['path'];
	}

	function log_to_file($ip, $status_code, $meta, $filepath, $filesize) {
		$ts = date("Y-m-d H:i:s", strtotime('now'));
		$this->log_sep;
		$str = $ts.$this->log_sep.$ip.$this->log_sep.$status_code.$this->log_sep.
		$meta.$this->log_sep.$filepath.$this->log_sep.$filesize."\n";
		file_put_contents($this->log_file, $str, FILE_APPEND);
	}
}

?>
