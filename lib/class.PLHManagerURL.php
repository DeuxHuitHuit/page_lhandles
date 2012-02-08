<?php

	if(!defined('__IN_SYMPHONY__')) die('<h2>Error</h2><p>You cannot directly access this file</p>');
	
	
	
	require_once(EXTENSIONS . '/frontend_localisation/lib/class.FLang.php');
	
	
	
	final class PLHManagerURL implements Singleton
	{
		private static $instance;
		
		public static function instance(){
			if (!self::$instance instanceof PLHManagerURL) {
				self::$instance = new PLHManagerURL();
			}
			
			return self::$instance;
		}
		
		
		
		/**
		 * Converts given URL from Symphony Page handles to $language_code Page handles.
		 *
		 * @param string $url - URL to convert
		 * @param string $language_code (optional) - language code. If empty, defaults to reference Frontend language
		 *
		 * @return string - localised URL if $language_code was found else original URL
		 */
		public function sym2lang($url, $language_code = null){
			if( empty($language_code) ){
				$language_code = FLang::instance()->referenceLanguage();
			
				// if no language is set, return current URL
				if( empty($language_code) ){
					return $url;
				}
			}
			
			$ref_handle = 'handle';
			$target_handle = 'plh_h-'.$language_code;
			
			return $this->_processURL($url, $ref_handle, $target_handle);
		}
		
		/**
		 * Converts given URL from $language_code Page handles to Symphony Page handles.
		 *
		 * @param string $url - URL to convert
		 * @param string $language_code (optional) - language code. If empty, defaults to current Frontend language
		 *
		 * @return string - symphony URL if $language_code was found else original URL
		 */
		public function lang2sym($url, $language_code = null){
			if( empty($language_code) ){
				$language_code = FLang::instance()->ld()->languageCode();
			
				// if no language is set, return current URL
				if( empty($language_code) ){
					return $url;
				}
			}
			
			$ref_handle = 'plh_h-'.$language_code;
			$target_handle = 'handle';
			
			return $this->_processURL($url, $ref_handle, $target_handle);
		}
		
		
		
	/*-------------------------------------------------------------------------
		Utilities:
	-------------------------------------------------------------------------*/
		
		/**
		 * Process given URL. Finds target_handles from reference_handles.
		 *
		 * @param string $url
		 * @param string $ref_handle
		 * @param string $target_handle
		 *
		 * @return string - processed URL
		 */
		private function _processURL($url, $ref_handle, $target_handle){
			/*
			 * This is here in case raw URLs are processed. (eg: URLs comming from Multilingual Entry URL).
			 * Normally, a sanitized $url will come here (from Symphony)
			 */
			$url_query = '';
			$url_hash = '';
			
			// find the Query
			$url_query_pos = strpos($url, '?');
			
			if( $url_query_pos !== false ){
				$url_query = substr($url, $url_query_pos);
				$url = substr($url, 0, $url_query_pos);
			}
			
			// else find the Hash
			else{
				$url_hash_pos = strpos($url, '#');
				
				if( $url_hash_pos !== false ){
					$url_hash = substr($url, $url_hash_pos);
					$url = substr($url, 0, $url_hash_pos);
				}
			}
			
			
			$old_url = preg_split('/\//', trim($url, '/'), -1, PREG_SPLIT_NO_EMPTY);

			$path = '';
			$last_parent = null;


			// resolve index
			if( $old_url == null || empty($old_url) || !is_array($old_url) ){

				// get the index page info
				$query = "
					SELECT p.`id`, p.`{$target_handle}`, p.`parent`
					FROM `tbl_pages` as p
					INNER JOIN `tbl_pages_types` as pt ON pt.`page_id` = p.`id`
					WHERE pt.`type` = 'index'
					LIMIT 1";

				// try to resolve the index page
				$bit = $this->_getPageHandle($query, $last_parent, $target_handle);

				if( $bit === false ){
					$path = $url;
				}
				else{
					$path = '/'.$bit;
				}
			}

			// resolve other pages
			else{
				$op_mode = Symphony::Configuration()->get('op_mode', PLH_GROUP);
				$method = '_process'.ucfirst($op_mode);
				
				if( method_exists($this, $method) ){
					$path = call_user_method($method, $this, $old_url, $ref_handle, $target_handle);
				}
				else{
					$path = trim($url, '/');
				}
			}
			
			return (string) $path .'/'. $url_query . $url_hash;
		}
		
		/**
		 * Processes the URL with relax settings. Used for URL Router compatibility
		 * Doesn't respect Symphony Page parents structure.
		 *
		 * @param array $old_url
		 * @param string $ref_handle
		 * @param string $target_handle
		 */
		private function _processRelax($old_url, $ref_handle, $target_handle){
			$path = '';
			
			foreach( $old_url as $value ){
				if( !empty($value) ){
					$last_parent = null;
					
					$query = sprintf(
							"SELECT `id`, `%s`, `parent` FROM `tbl_pages` WHERE `%s` = '%s' LIMIT 1",
							$target_handle, $ref_handle, $value
					);
			
					$bit = $this->_getPageHandle($query, $last_parent, $target_handle);
					
					$path .= '/'.($bit === false ? $value : $bit );
				}
			}
			
			return $path;
		}
		
		/**
		 * Processes the URL with strict settings.
		 * Respects Symphony Page parents structure.
		 *
		 * @param array $old_url
		 * @param string $ref_handle
		 * @param string $target_handle
		 */
		private function _processStrict($old_url, $ref_handle, $target_handle){
			$path = '';
			$page_mode = true;
			$last_parent = null;
			
			foreach( $old_url as $value ){
				if( !empty($value) ){
			
					$query = sprintf("
							SELECT `id`, `%s`, `parent` FROM `tbl_pages` WHERE `%s` = '%s' AND `parent` %s LIMIT 1",
							$target_handle,
							$ref_handle,
							$value,
							($last_parent != null ? sprintf("= %s", $last_parent) : "IS NULL")
					);
			
					if( $page_mode ){
						$bit = $this->_getPageHandle($query, $last_parent, $target_handle);
			
						if( $bit === false ){
							$path .= '/'.$value;
							$page_mode = false;
						}
						else{
							$path .= '/'.$bit;
						}
					}
					else{
						$path .= '/'.$value;
					}
				}
			}
			
			return $path;
		}
		
		/**
		 * Executes the given query and returns target_handle or false if no match
		 *
		 * @param string $query
		 * @param integer $last_parent (reference)
		 * @param string $target_handle - $target_handle desired
		 *
		 * @return mixed - Translated handle or false if no handle found.
		 */
		private function _getPageHandle($query, &$last_parent, $target_handle) {
			try {
				$page = Symphony::Database()->fetch($query);
			}
			catch (DatabaseException $e) {
				//table column "$lhandle" doesn't exist. redirect to 404.
				if ( $e->getDatabaseErrorCode() == 1054 ) {
					FrontendPageNotFoundExceptionHandler::render($e);
				}
				// re-trow non-handled exception
				else {
					throw $e;
				}
			}
		
			// page handle exists, store it
			if( !empty($page) && ($last_parent == $page[0]['parent']) ){
				$last_parent = $page[0]['id'];
				
				return $page[0][$target_handle];
			}
		
			return false;
		}
	
	}
