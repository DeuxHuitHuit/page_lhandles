<?php

	if(!defined('__IN_SYMPHONY__')) die('<h2>Error</h2><p>You cannot directly access this file</p>');

	
	
	require_once(TOOLKIT . '/class.datasourcemanager.php');

	
	
	final class PLHDatasourceManager
	{
		
		/**
		 * Changes the source of navigation datasources.
		 *
		 * @param string $mode - flag for datasource status.
		 */
		public function editAllNavDssTo($mode) {
			$dsm = new DatasourceManager(Symphony::Engine());
			$datasources = array_keys( $dsm->listAll() );

			if( is_array($datasources) && !empty($datasources) ){

				foreach( $datasources as $value ){
					$filename = WORKSPACE . '/data-sources/data.' . $value . '.php';

					if ( is_file($filename) && is_writable($filename) ) {
						$old_content = file_get_contents($filename);

						if( $this->_isDsTypeNavigation($old_content) ){
							
							if( method_exists($this, "_setNavDsTo{$mode}") ){
								$new_content = call_user_func( array($this, "_setNavDsTo{$mode}"), $old_content );
								
								General::writeFile($filename, $new_content);
							}
						}
					}
				}
			}
		}
		
		/**
		 * Changes the source of the datasource to PLH or SYMPHONY
		 *
		 * @param string $mode - flag for datasource status.
		 * @param string $contents - holding original contents.
		 * @return string - new contents
		 */
		public function editNavDsTo($mode, $contents) {

			if ( $this->_isDsTypeNavigation($contents) ) {
				return call_user_func( array($this, "_setNavDsTo{$mode}"), $contents );
			}
			
			return $contents;
		}



		private function _isDsTypeNavigation($contents) {
			return (boolean) (preg_match("return 'navigation';", $contents) == 1);
		}

		/**
		 * Replace standard navigation template with PLH navigation template
		 *
		 * @param string $contents- old datasource file contents.
		 * 
		 * @return string - new datasource file contents.
		 */
		private function _setNavDsToPLH($contents){
			$was_edited = strpos($contents , "//PLH-COMM//");

			if( empty($was_edited) ){
			
				$old_template = "include(TOOLKIT . '/data-sources/datasource.navigation.php');";
				$pos = strpos($contents , $old_template);
	
				if( !empty($pos) ){
					$new_template = "include(EXTENSIONS . '/page_lhandles/lib/datasource.navigation.php');//PLH-COMM//";
					return (string) substr_replace($contents, $new_template, $pos, 0);
				}
				else{
					/* include(TOOLKIT . '/data-sources/datasource.navigation.php'); was not found */
					die('PageLHandles : While trying to change the source of the navigation Datasource, I failed because I couldn\'t find the necessary string in DS please check it.');
				}
			}
			
			return (string) $contents;
		}

		/**
		 * Replace PLH navigation template with standard navigation template.
		 *
		 * @param string $contents
		 *  Old datasource file contents.
		 * @return string
		 *  New datasource file contents.
		 */
		private function _setNavDsToSYMPHONY($contents){
			$was_edited = strpos($contents , "//PLH-COMM//");

			if( !empty($was_edited) ){

				$plh_template = "include(EXTENSIONS . '/page_lhandles/lib/datasource.navigation.php');//PLH-COMM//";
				$pos = strpos($contents , $plh_template);

				if( !empty($pos) ){
					return (string) str_replace($plh_template, '', $contents);
				}
				else{
					/* include(TOOLKIT . '/data-sources/datasource.navigation.php'); was not found
					 * or
					 * this DS was already changed
					 */
				}
			}

			return (string) $contents;
		}

	}
