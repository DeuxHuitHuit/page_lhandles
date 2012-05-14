<?php

	if( !defined('__IN_SYMPHONY__') ) die('<h2>Error</h2><p>You cannot directly access this file</p>');



	require_once(TOOLKIT.'/class.datasourcemanager.php');



	final class PLHDatasourceManager
	{

		/**
		 * Changes the source of navigation datasources.
		 *
		 * @static
		 *
		 * @param string $mode - flag for datasource status.
		 */
		public static function editAllNavDssTo($mode){
			$datasources = array_keys(DatasourceManager::listAll());

			if( is_array($datasources) && !empty($datasources) ){

				foreach( $datasources as $value ){
					$filename = WORKSPACE.'/data-sources/data.'.$value.'.php';

					if( is_file($filename) && is_writable($filename) ){
						$old_content = file_get_contents($filename);

						if( self::_isDsTypeNavigation($old_content) ){

							if( method_exists(get_class(), "_setNavDsTo{$mode}") ){
								$new_content = call_user_func(array(self, "_setNavDsTo{$mode}"), $old_content);

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
		 * @param string $mode     - flag for datasource status.
		 * @param string $contents - holding original contents.
		 *
		 * @static
		 *
		 * @return string - new contents
		 */
		public function editNavDsTo($mode, $contents){

			if( self::_isDsTypeNavigation($contents) ){

				if( method_exists(get_class(), "_setNavDsTo{$mode}") ){
					return call_user_func(array(self, "_setNavDsTo{$mode}"), $contents);
				}
			}

			return $contents;
		}



		private static function _isDsTypeNavigation($contents){
			return (boolean)(preg_match("/return 'navigation';/", $contents) === 1);
		}

		/**
		 * Replace standard navigation template with PLH navigation template
		 *
		 * @param string $contents- old datasource file contents.
		 *
		 * @static
		 *
		 * @return string - new datasource file contents.
		 */
		private static function _setNavDsToPLH($contents){
			$was_edited = strpos($contents, "extends MultilingualNavigationDatasource{//");

			if( $was_edited === false ){

				$old_template = "extends NavigationDatasource{";
				$pos = strpos($contents, $old_template);

				if( $pos !== false ){
					$new_template = "extends MultilingualNavigationDatasource{//";
					return (string)substr_replace($contents, $new_template, $pos, 0);
				}
				else{
					/* "extends NavigationDatasource" was not found */
					die('PageLHandles : While trying to change the source of the navigation Datasource, I failed because I couldn\'t find the necessary string in DS please check it.');
				}
			}

			return (string)$contents;
		}

		/**
		 * Replace PLH navigation template with standard navigation template.
		 *
		 * @param string $contents Old datasource file contents.
		 *
		 * @static
		 *
		 * @return string New datasource file contents.
		 */
		private static function _setNavDsToSYMPHONY($contents){
			$was_edited = strpos($contents, "extends MultilingualNavigationDatasource{//");

			if( $was_edited !== false ){

				$plh_template = "extends MultilingualNavigationDatasource{//";
				$pos = strpos($contents, $plh_template);

				if( $pos !== false ){
					return (string)str_replace($plh_template, '', $contents);
				}
				else{
					/* "extends MultilingualNavigationDatasource" was not found
					 * or
					 * this DS was already changed
					 */
				}
			}

			return (string)$contents;
		}

	}
