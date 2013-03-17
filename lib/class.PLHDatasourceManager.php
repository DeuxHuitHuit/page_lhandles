<?php

	if( !defined('__IN_SYMPHONY__') ) die('<h2>Error</h2><p>You cannot directly access this file</p>');



	require_once(TOOLKIT.'/class.datasourcemanager.php');



	final class PLHDatasourceManager
	{

		private static $plh_template = "extends MultilingualNavigationDatasource";
		private static $sym_template = "extends NavigationDatasource";

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

							if( method_exists(get_class(), "setNavDsTo{$mode}") ){
								$new_content = call_user_func(array(self, "setNavDsTo{$mode}"), $old_content);

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

				if( method_exists(get_class(), "setNavDsTo{$mode}") ){
					return call_user_func(array(self, "setNavDsTo{$mode}"), $contents);
				}
			}

			return $contents;
		}



		private static function _isDsTypeNavigation($contents){
			return (boolean) (preg_match("/return 'navigation';/", $contents) === 1);
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
		public static function setNavDsToPLH($contents){
			return preg_replace("/".self::$sym_template."/", self::$plh_template, $contents);
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
		public static function setNavDsToSYMPHONY($contents){
			return preg_replace("/".self::$plh_template."/", self::$sym_template, $contents);
		}

	}
