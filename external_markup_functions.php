<?php
/* =====================
This file must be included in the template functions.php.
Example: include_once (TEMPLATEPATH . '/external_markup_functions.php');
Copy the file external_markup_functions.php into your template folder.
/* =====================

/* Define retrival of options and the Blog ID */
define("BLOG_ID", $blog_id);
define("CACHE_TIME", get_option("em_external_src_cache"));
define("EXTERNAL_URL",get_option("em_external_src"));

/* -------------------------------------------------------------------------------------
	Define the different cached html files names and path.
	The path is default set to /themes/TEMPLATENAME/em_cache/
	The folder /themes/TEMPLATENAME/em_cache/ must be writeable (chmod 777)
	
	Default namesetting on the cached files is: blog_BLOG_ID_XXX.html
	The BLOG_ID is used for creating unique files, important when using Wordpress MU
---------------------------------------------------------------------------------------- */
define("EM_EXAMPLE", TEMPLATEPATH."/em_cache/blog_".BLOG_ID."_EXAMPLE.html");

/* 
----------------------------------------------------------------------------------------
	Function em_showContent is called from the templates. 
	It checks if the cached file already exists or it's to old (based on CACHE_TIME).
	$retrive_start = Start retriving from html comment
	$retrive_stop = Stop retriving before html comment
	$retrive_filename = Filename of cached htmlfile
----------------------------------------------------------------------------------------
*/
function em_showContent($retrive_start,$retrive_stop,$retrive_filename,$compress_file){
	if (!file_exists($retrive_filename) || (time()-filemtime($retrive_filename) >= CACHE_TIME)) {
    	em_getContent($retrive_start,$retrive_stop,$retrive_filename,$compress_file);
  	}
	include($retrive_filename);
}

/* 
----------------------------------------------------------------------------------------
	Function em_getContent is called from function em_showContent if needed.
	em_showContent retrives the external markup with CURL or fOPEN.
	If the content that is returned is NULL, no cached file is created.
	$compress_file = If the file will be compressed when writing it (use with caution)
----------------------------------------------------------------------------------------
*/
function em_getContent($retrive_start,$retrive_stop,$retrive_filename,$compress_file){
	$url = EXTERNAL_URL;
	$data = "";
	if (function_exists("curl_init")) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$markup = curl_exec($ch);
		$start = "<!-- ".$retrive_start." -->";
		$end = "<!-- ".$retrive_stop." -->";
		$data_bit = strstr($markup, $start);
		$array = explode($end, $data_bit);
		$length = strlen($retrive_start) + 9;
		$data = substr($array[0],$length);
		curl_close($ch);
	}else{
		if ($fp = fopen($url, 'r')) {
			$content = '';
			while ($line = fread($fp, 1024)) {
				$content .= $line;
			}
			$start = "<!-- ".$retrive_start." -->";
			$end = "<!-- ".$retrive_stop." -->";
			$data_bit = strstr($content, $start);
			$array = explode($end, $data_bit);
			$length = strlen($retrive_start) + 9;
			$data = substr($array[0],$length);
		} else {
			// Error when retriving data
		}
	}
	
	$data_length = strlen($data);

	if (!$data_length < 1) {
		$file_handler = fopen($retrive_filename, "w");
		if($compress_file == 1){
			fwrite($file_handler,em_compress_file($data));
		}else{
			fwrite($file_handler,$data);
		}
		fclose($file_handler);		
	}
}

/* function based on: http://davidwalsh.name/compress-xhtml-page-output-php-output-buffers */
function em_compress_file($data){
	$search = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s');
	$replace = array('>','<','\\1');
	$data = preg_replace($search, $replace, $data);
	return $data;
}
?>