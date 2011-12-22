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
			$old_url = preg_split('/\//', trim($url, '/'), -1, PREG_SPLIT_NO_EMPTY);

			$path = '/';
			$last_parent = null;


			// resolve index
			if( $old_url == null || empty($old_url) || !is_array($old_url) ){

				// get the index page
				$query = "
					SELECT p.`id`, p.`{$target_handle}`, p.`parent`
					FROM `tbl_pages` as p
					INNER JOIN `tbl_pages_types` as pt ON pt.`page_id` = p.`id`
					WHERE pt.`type` = 'index'
					LIMIT 1";

				// try to get the index page
				$bit = $this->_getPageHandle($query, $last_parent, $target_handle);

				if( $bit === false ){
					return (string) '/' . implode('/', $old_url) . '/';
				}
				else{
					$path = $bit.'/';
				}
			}

			// resolve other pages
			else{
				$page_mode = true;

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
								$path .= $value.'/';
								$page_mode = false;
							}
							else{
								$path .= $bit.'/';
							}
						}
						else{
							$path .= $value.'/';
						}
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
