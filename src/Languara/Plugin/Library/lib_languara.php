<?php

class Lib_Languara
{
	public $error_message;
	
	public $zip;
	
	public $language_location;
    
    public $env = 'development';
	
	public $endpoints = array();
	
	public $conf = array();
	
	public $arr_project_locales = array();
	public $arr_resource_groups = array();
	public $arr_resources       = array();
	public $arr_translations	= array();
	public $external_request_id	= null;
    
    public function check_auth($external_request_id, $client_signature)
    {
        if ($client_signature != base64_ENCODE(hash_hmac('sha256', $external_request_id, $this->conf['project_api_secret'], true))) return false;
        
        $this->external_request_id = $external_request_id;
        
        return true;
    }

	public function upload_local_translations()
	{
		// sanity checks
		if (! is_dir($this->language_location))
		{
            throw new \Exception('ERROR: Language directory not found!'. $this->language_location);
			return false;
		}
        
        // get local translations
        if (php_sapi_name() == "cli") {
            print $this->color_text("Retrieving local data!", 'NOTICE'). PHP_EOL;
            sleep(2);
        }
		$arr_translations = $this->retrive_local_translations();		
        
        // get project locales
        if (php_sapi_name() == "cli") {
            print $this->color_text("Pushing locales and translations to the Languara servers!", 'NOTICE'). PHP_EOL;
            sleep(2);
        }
        
        $endpoint_postfix = '';
		if ($this->env == 'development') $endpoint_postfix = '_local';
        
		$this->fetch_endpoint_data('upload_translations'. $endpoint_postfix, $arr_translations, 'post', true);
	}
	
    protected function retrive_local_translations()
    {
        // get local locales, resource groups, and translations
		$dir_iterator = new \DirectoryIterator($this->language_location);		
		$arr_locales = array();
		
		foreach ($dir_iterator as $dir)
		{			
			// skip the system files for navigation and language_backup directory
			if($dir->getFilename() != '.' && $dir->getFilename() != '..' && $dir->getFilename() != 'language_backup')
			{
				if ($dir->isDir())
				{
					$arr_locales[$dir->getFilename()] = array();
					
					$arr_locales[$dir->getFilename()] = $this->retrive_resource_groups_and_translations($dir->getRealPath());
				}
			}
		}
        
        return $arr_locales;
    }
    
	protected function retrive_resource_groups_and_translations ($dir_path)
	{
		$dir_iterator = new \DirectoryIterator($dir_path);	
		$arr_resource_groups = array();
		
		foreach ($dir_iterator as $file)
		{            
			// skip system files
			if ($file->getFilename() == '.' || $file->getFilename() == '..' || $file->getFilename() == 'language_backup') continue;
			if ($this->conf['storage_engine'] == 'php_assoc_array') include ($file->getRealPath());
			if ($this->conf['storage_engine'] == 'php_array') $lang = include ($file->getRealPath());
            
            // if file empty
			if (! isset($lang)) continue;
            
            // remove the suffix of the file
            if ($this->conf['file_suffix'])
            {
                $dir_name_filtered = strrev(preg_replace('/'. strrev($this->conf['file_suffix']) .'/', '', strrev($file->getBasename('.php')), 1));
            }
            
			// add resource groups as keys
			$arr_resource_groups[$dir_name_filtered] = $lang;
		}
		
		return $arr_resource_groups;
	}
	
	public function download_and_process()
	{
		// sanity checks
		if (! is_dir($this->language_location))
		{
            throw new \Exception('ERROR: Language directory not found, check the location of the language directory in the config file and try again!');
			return false;
		}
		
		if (! is_writable($this->language_location))
		{
            throw new \Exception('ERROR: Language directory is not writable, make sure the proper permission are set!');
			return false;
		}
        
		// back up local data
        if (php_sapi_name() == "cli") {
            print $this->color_text("Backing up local data!", 'NOTICE'). PHP_EOL;
            sleep(2);
        }
        
		// create back dir if it doesn't exist
		$this->create_dir($this->language_location, 'language_backup');
		
		// create zip file
		$zip_name = date('Y-m-d-H:i:s') .'.zip';
		
		$this->zip = new \ZipArchive;
		$this->zip->open($this->language_location .'language_backup/'. $zip_name, \ZipArchive::CREATE);		
		
		$this->add_folder_to_zip($this->language_location);
		
		$this->zip->close();
		
		// remove local translations
        if (php_sapi_name() == "cli") {
            print $this->color_text("Removing local files!", 'NOTICE'). PHP_EOL;
            sleep(2);
        }
		$this->remove_local_translations($this->language_location);
		
		$endpoint_postfix = '';
		if ($this->env == 'development') $endpoint_postfix = '_local';
		
        // get project locales
        if (php_sapi_name() == "cli") {
            print $this->color_text("Retrieving all languages from the server!", 'NOTICE'). PHP_EOL;
            sleep(2);
        }
		$this->arr_project_locales = $this->fetch_endpoint_data('project_locale'. $endpoint_postfix, null, 'get', true);

		if (!$this->arr_project_locales)
		{
            throw new \Exception('Project has no languages, add some languages on Languara and then try pulling your content!');
			return false;
		}
        
        // get project resource groups
        if (php_sapi_name() == "cli") {
            print $this->color_text("Retrieving all resource groups from the server!", 'NOTICE'). PHP_EOL;
            sleep(2);
        }
		$this->arr_resource_groups = $this->fetch_endpoint_data('resource_group'. $endpoint_postfix, null, 'get', true);
        
        // get project translations
        if (php_sapi_name() == "cli") {
            print $this->color_text("Retrieving all translations from the server!", 'NOTICE'). PHP_EOL;
            sleep(2);
        }
		$this->arr_translations	= $this->fetch_endpoint_data('translation'. $endpoint_postfix, null, 'get', true);
		
        // get project translations
        if (php_sapi_name() == "cli") {
            print $this->color_text("Adding content to local files!", 'NOTICE'). PHP_EOL;
            sleep(2);
        }
		$this->add_translations_to_files();
		
		return true;
	}
    
    protected function add_translations_to_files()
    {
        // process locale
		foreach ($this->arr_project_locales as $project_locale) 
		{
			$this->create_dir($this->language_location, strtolower($project_locale->iso_639_1));
			
			// process translations
			foreach ($this->arr_resource_groups as $resource_group)
			{
                
				$resource_group_file_contents = $this->get_file_header();
				foreach ($this->arr_translations as $translation) 
				{					
					if ($translation->resource_group_id == $resource_group->resource_group_id && $project_locale->locale_id == $translation->locale_id)
					{						
						$resource_group_file_contents .= $this->get_file_content($translation->resource_cd, $translation->translation_txt);
					}
				}
                
                $resource_group_file_contents .= $this->get_file_footer();
                
                $file_path = strtolower($this->language_location . $project_locale->iso_639_1 .'/'. $this->conf['file_prefix'] . $resource_group->resource_group_name . $this->conf['file_suffix'] .'.php');
				file_put_contents($file_path, $resource_group_file_contents);
                chmod($file_path, 0777);
			}
		}	
    }
	
	private function fetch_endpoint_data($endpoint_name, $parameters = null, $method = 'get', $json_decode_ind = false) 
    {
		if (! isset($this->endpoints[$endpoint_name]))
		{
            throw new \Exception('Endpoint '. $endpoint_name .' not found. Check your local configuration and make sure the endpoints array is correctly defined or just download a new config file from the server.');
			return false;
		}
		
		if ($method == 'get')
		{
			$url = $this->prepare_request_url($endpoint_name, $parameters);
			$response = $this->curl_get($url);
		}
		else
		{			
			$request_vars = array('local_data' => $parameters);
			
			$url = $this->prepare_request_url($endpoint_name, $request_vars);
			
			$request_vars = http_build_query($request_vars);
            
			$response = $this->curl_post($url, $request_vars);
		}
        
//		print "\n\nfetch_endpoint_data($endpoint_name)\n";
//		print "\naccessing endpoint URL: $url\n". PHP_EOL;
//		print "CLIENT: GOT RESPONSE\n". $response ."\n";
		$error = false;
		if ($json_decode_ind)
        {            
            $result = json_decode($response);
            $error_message_suffix = '';
            
            // throw an error if the server doesn't return anything
            if (! is_object($result))
            {
                throw new \Exception('ERROR: There was a error processing your request, please try again. If that does not work contact the administrators for more information!');
            }
            
            // get the error messages if there are any
            $messages = isset($result->meta) ? $result->meta : array();
            $error = array_key_exists('errors', $messages);
            
            // if the server returns 1, then add a message sufix to clarify the error
            if (isset($result->data) && ! is_object($result->data) && $result->data == 1)
            {
                $error_message_suffix = PHP_EOL .'Make sure your local config file is configured correctly. If you are not sure how to do that generate a new plugin files on Languara and replace the ones you have on your server at the moment!';
            }
            
            // if the request faild throw an exception
            if ($error) throw new \Exception('Languara\'s servers responded with an error: '. PHP_EOL. current(current($messages->errors)) . $error_message_suffix);
            
            // if no errors, we need to extract the data
            if (is_object($result))
            {
                $result = current($result);
            }
            
        } 
        else 
        {
            $result = $response;
        }
        
        return $result;
	}
	
	private function prepare_request_url($endpoint_name, $arr_params=null) 
    {
		$url = $this->endpoints[$endpoint_name]
				."?project_api_key=".$this->conf['project_api_key']
				."&project_id=".$this->conf['project_id'];
        
        if ($this->conf['project_deployment_id'])    
        {
            $url .= '&project_deployment_id='. $this->conf['project_deployment_id'];
        }
		
		// if request id present add it
		if ($this->external_request_id)
		{
			$url .= '&external_request_id='. $this->external_request_id;
		}
		
		$request_data_serialized = "";
		if (count($arr_params))
		{
			$request_data_serialized = http_build_query($arr_params);
		}

		// Create the request signature base and encode using the secret
		//
		$signature_base	= $url.$request_data_serialized;
		$signature		= base64_ENCODE(hash_hmac('sha256', $signature_base, $this->conf['project_api_secret'], true));
        
		// append request signature to request
		//
		$url .= '&_rs=' . $signature;
//		print "CLIENT: api secret: {$this->conf['app_api_secret']}\n";
//		print "CLIENT: signature base: $signature_base\n";
//		print "CLIENT: generated signature: $signature\n";
//		print "opening $endpoint_name: ";
		return $url;
	}

	private function curl_get($url, $callback=false) {
		return $this->curl_doRequest('GET',$url,'NULL',$callback);
	}

	private function curl_post($url, $vars, $callback=false) {
		return $this->curl_doRequest('POST',$url,$vars,$callback);
	}
	
	private function curl_doRequest($method, $url, $vars, $callback=false) 
	{	
		if (!function_exists('curl_init')) throw new \Exception('CURL is not enabled for translation service, please enable curl and try again');
		 
		// configure curl request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, 'languara_api_client');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        		
		if ($method == 'POST') {            
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
		}
		
        // Don't check ssl peers for now
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
		$data = curl_exec($ch);
        $error = curl_error($ch);
		curl_close($ch);
        
		if ($data) {
			if ($callback)
			{
				return call_user_func($callback, $data);
			} 
            else 
            {
				return $data;
			}
		} else {            
            throw new \Exception($error);
		}
	}
	
	protected function remove_local_translations($translations_path)
	{	
		$dir_iterator = new \DirectoryIterator($translations_path);
		
		foreach ($dir_iterator as $dir)
		{			
			// skip the system files for navigation and language_backup directory
			if($dir->getFilename() != '.' && $dir->getFilename() != '..' && $dir->getFilename() != 'language_backup')
			{
				// if it's a dir call this method on that directory and when it's empty
				// remove the dir 
				if ($dir->isDir())
				{
					$this->remove_local_translations($dir->getRealPath());
					rmdir($dir->getRealPath());
				}
				else
				{
					// remove file
					unlink($dir->getRealPath());
				}
			}
		}
	}
	
	protected function add_folder_to_zip($dir_path, $zip_path = null)
	{				
		$dir_iterator = new \DirectoryIterator($dir_path);
		
		// iterrate through the elements in the current dir
		foreach ($dir_iterator as $dir)
		{			
			// if current element is file, add the file to the zip
			if ($dir->isFile())
			{				
				$this->zip->addFile($dir->getRealPath(), $zip_path . $dir->getFilename());
			}
			else if($dir->getFilename() != '.' && $dir->getFilename() != '..' && $dir->getFilename() != 'language_backup')
			{
				// if current element is dir, add the dir to the zip and call this method
				// on that dir
				$dir_name = ($zip_path) ? $zip_path . $dir->getBasename() : $dir->getBasename() ;
				
				$this->zip->addEmptyDir($dir_name);
				
				$this->add_folder_to_zip($dir_path . $dir->getFilename() .'/', $zip_path . $dir->getFilename() .'/');
			}
		}
	}
	
	/**
	 * If a dir doesn't exists, it creates it
	 * 
	 * @param string $dir_path
	 * @param string $dir_name
	 * @return boolean
	 */
	protected function create_dir ($dir_path, $dir_name)
	{
		$dir_path = $dir_path. $dir_name;
		
		if (!file_exists($dir_path) && !is_dir($dir_path)) 
		{
			mkdir($dir_path);
            chmod($dir_path, 0777);
		} 
		
		return true;
	}
    
    protected function get_file_header()
    {
        $header = null;
        
        if ($this->conf['storage_engine'] == 'php_assoc_array') $header = '<?php'. PHP_EOL;
        
        if ($this->conf['storage_engine'] == 'php_array') $header = '<?php return array('. PHP_EOL;
        
        if ($header === null) throw new \Exception ('ERROR: Storage engine is not supported!');
        
        return $header;
    }
    
    protected function get_file_footer()
    {
        $footer = null;
        
        if ($this->conf['storage_engine'] == 'php_assoc_array') $footer = '';
        
        if ($this->conf['storage_engine'] == 'php_array') $footer = ');';
        
        if ($footer === null) throw new \Exception ('ERROR: Storage engine is not supported!');
        
        return $footer;
    }
    
    protected function get_file_content($resource_cd, $translation_txt)
    {
        $content = null;
        
        if ($this->conf['storage_engine'] == 'php_assoc_array') $content = '$lang[\''. $resource_cd .'\'] = \''. str_replace("'", "\\'",$translation_txt) .'\';'. PHP_EOL;
        
        if ($this->conf['storage_engine'] == 'php_array') $content = '\''. $resource_cd .'\' => \''. str_replace("'", "\\'",$translation_txt) .'\','. PHP_EOL;
        
        if ($content === null) throw new \Exception ('ERROR: Storage engine is not supported!');
        
        return $content;
    }
    
    public function color_text($text, $status)
    {
        $out = "";
        switch ($status)
        {
            case "SUCCESS":
                $out = "[0;32m"; //Green background
                break;
            case "FAILURE":
                $out = "[0;31m"; //Red background
                break;
            case "NOTICE":
                $out = "[1;33m"; //Red background
                break;
            default:
            $out = "[0m"; //White background
        }
        
        return chr(27) . "$out" . "$text" . chr(27) . "[0m";
    }
}

/* End of file Lib_languara.php */