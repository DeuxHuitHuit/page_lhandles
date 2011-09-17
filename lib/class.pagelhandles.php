<?php

	if(!defined('__IN_SYMPHONY__')) die('<h2>Error</h2><p>You cannot directly access this file</p>');

	require_once(TOOLKIT . '/class.datasourcemanager.php');
	require_once(EXTENSIONS . '/language_redirect/lib/class.languageredirect.php');

	class PageLHandles
	{

		//holds current language codes separated by underscore '_'. eg: [en_us], [ro]
		private static $_language_codes_;

		//ascending line of curent page, including the page
		private static $_page_ascending_line;


		public function __construct() {
			self::$_page_ascending_line = array();

			$language_codes = LanguageRedirect::instance()->getSupportedLanguageCodes();

			$this->_replaceDashes($language_codes);
			self::$_language_codes_ = $language_codes;
		}


		public static function getLanguageCodes_() {
			return (array) self::$_language_codes_;
		}

		public static function getPageAscendingLine() {
			return (string) self::$_page_ascending_line;
		}



		/**
		 * 
		 * Appends the Localisation Titles and Handles to the backend Page form.
		 *
		 * @param XMLElement $form
		 * @param Integer $pageID
		 */
		public function appendPageFormContent(&$form, $pageID) {

			$all_languages = LanguageRedirect::instance()->getAllLanguages();
			$language_codes = LanguageRedirect::instance()->getSupportedLanguageCodes();

			$fieldset = new XMLElement('fieldset');
			$fieldset->setAttribute('class', 'settings');
			$fieldset->appendChild(new XMLElement('legend', __('Page LHandles')));

			$group = new XMLElement('div');
			$group->setAttribute('class', 'group');

			$column = new XMLElement('div');
			$column->setAttribute('class', 'page_lhandles');


			/* Tabs */

			$ul = new XMLElement('ul');
			$ul->setAttribute('class', 'tabs');

			foreach ( $language_codes as $language ) {
				$class = $language . ($language == $language_codes[0] ? ' active' : '');
				$li = new XMLElement( 'li', ($all_languages[$language] ? $all_languages[$language] : __('Unknown Lang').' : '.$language) );
				$li->setAttribute('class', $class);

				$ul->appendChild($li);
			}

			$column->appendChild($ul);


			$qselect = '';
			foreach ( self::$_language_codes_ as $language ) {
				$qselect .= "page_lhandles_t_".$language.",";
				$qselect .= "page_lhandles_h_".$language.",";
			}
			$qselect = trim($qselect, ',');

			$values = Symphony::Database()->fetch(" SELECT {$qselect} FROM `tbl_pages` WHERE id = '{$pageID}' LIMIT 1" );


			/* Localised Title */

			foreach ( $language_codes as $key => $language ) {
				$panel = Widget::Label(__('Localised Title'));
				$panel->setAttribute('class', 'tab-panel tab-'.$language);

				$input = Widget::Input("fields[page_lhandles_t_".self::$_language_codes_[$key]."]", $values[0][ 'page_lhandles_t_'.self::$_language_codes_[$key] ]);
				$input->setAttribute('length', '30');

				$panel->appendChild($input);
				$column->appendChild($panel);
			}


			/* Localised URL Handle */

			foreach ( $language_codes as $key => $language ) {
				$panel = Widget::Label(__('Localised URL Handle'));
				$panel->setAttribute('class', 'tab-panel tab-'.$language);

				$input = Widget::Input("fields[page_lhandles_h_".self::$_language_codes_[$key]."]", $values[0][ 'page_lhandles_h_'.self::$_language_codes_[$key] ]);
				$input->setAttribute('length', '30');

				$panel->appendChild($input);
				$column->appendChild($panel);
			}


			$group->appendChild($column);
			$fieldset->appendChild($group);
			$form->prependChild($fieldset);
		}

		/**
		 * 
		 * Process the accessed URL in browser and translate the localised page handles to Symphony handles.
		 *
		 * @param array $old_url
		 *  Contains the URL with localised handles.
		 *  Array must be created with the string split on each /
		 * @return string
		 *  The new URL string containing Symphony handles.
		 */
		public function processUrl($old_url) {

			// if no language is set, return current URL
			if (strlen(LanguageRedirect::instance()->getLanguageCode()) < 1) {
				return (string) '/' . implode('/', $old_url) . '/';
			}

			// reset variables
			self::$_page_ascending_line = array();

			$path = '/';
			$page_mode = true;
			$last_parent = null;

			$query_select = 'p.`id`, p.`handle`, p.`parent`';
			foreach ( self::$_language_codes_ as $language ) {
				$query_select .= ', p.`page_lhandles_h_'.$language.'`';
				$query_select .= ', p.`page_lhandles_t_'.$language.'`';
			}

			// fix issue #7, special case for index page without any parameters set
			if ($old_url == null || empty($old_url) || !is_array($old_url)) {

				// get the index page
				$query = "SELECT {$query_select}
							FROM `tbl_pages` as p
								INNER JOIN `tbl_pages_types` as pt
									ON pt.`page_id` = p.`id`
								WHERE pt.`type` = 'index' LIMIT 1";

				// try to get the index page
				$path = $this->_tryGetPagePathPiece($query, '/', $page_mode, $last_parent);

			} else {

				// process each url piece
				foreach ( $old_url as $value ) {

					if ( !empty($value) ) {
						$lhandle = 'page_lhandles_h_' . LanguageRedirect::instance()->getLanguageCode();
						$query = "SELECT {$query_select} FROM `tbl_pages` as p WHERE `{$lhandle}` = '{$value}'";

						// avoid name conflict in sub-sub-pages. Use parent as reference
						// /php/support/ vs. /jQuery/support/
						if ($last_parent != null) {
							$query .= " AND p.`parent` = {$last_parent}";

						} else {
							// specify that parent must be null to avoid confusion
							// /en/calendar -> calendar page must not have a parent
							// /en/some-folder/calendar -> calendar page must have some-folder as parent
							$query .= ' AND p.`parent` IS NULL';
						}

						// limit to only one result, since we won't check any other one
						$query .= ' LIMIT 1';

						// try to get the page path piece
						$path .= $this->_tryGetPagePathPiece($query, $value, $page_mode, $last_parent);
					}
				}
			}

			return (string) $path;
		}

		/**
		 * 
		 * For all Pages fills the new added columns with the values from first language in lang array.
		 * 
		 * @param array $to_check_languages
		 *   languages to be checked for
		 */
		public function fillNamesAndHandlesForPages($to_check_languages = null) {
			if ( empty($to_check_languages) ) {
				$to_check_languages = self::$_language_codes_;
	
				if ( empty($to_check_languages) ) {
					//means there are no language codes in Configuration file
					return (boolean) true;
				}
			}
			
			$languages = LanguageRedirect::instance()->getSupportedLanguageCodes();
			$reference_lang_code = $languages[0];
			
			$query_IDs = 'SELECT `id` FROM `tbl_pages`';
			$pages_IDs = Symphony::$Database->fetchCol('id',$query_IDs);
			
			$query_fields = "";
			
			foreach ($to_check_languages as $language) {
				$query_fields .= "\n `page_lhandles_t_{$language}`,";
				$query_fields .= "\n `page_lhandles_h_{$language}`,";
			}
			
			$query_fields .= "\n `handle`,";
			$query_fields .= "\n `title`";
			
			foreach ( $pages_IDs as $pageID ) {
				$query_page_data = "SELECT {$query_fields} FROM `tbl_pages` WHERE `id` = '{$pageID}'";
				
				$page_data = Symphony::$Database->fetch($query_page_data);
				
				$plh_title = $page_data[0]["title"];
				$plh_handle = $page_data[0]["handle"];
				
				$new_page_data = array();
				$query_update_fields = '';
				
				foreach ( $page_data[0] as $key => $value ) {
					if ( empty($value) ) {
						$isTitle = strpos($key, '_t_');
						
						$langCode = substr($key, 16);
						if ( $langCode == $reference_lang_code ) {
							$langCode = '';
						}
						
						if ( empty($isTitle) ) {
							$newValue = $langCode . $plh_handle; }
						else {
							$newValue = strtoupper($langCode) . $plh_title; }
						
						$query_update_fields .= "\n `{$key}` = '{$newValue}',";
					}
				}
				
				if ( !empty($query_update_fields) ) {
					$query_update_fields = trim($query_update_fields, ',');
					$query_page_update = "UPDATE tbl_pages SET  {$query_update_fields} WHERE `id` = '{$pageID}';";
					
					Symphony::$Database->query($query_page_update);
				}
			}
			
			return (boolean) true;
		}
		
		/**
		 * 
		 * Adds columns to 'tbl_pages' table, depending on language codes from Language Redirect.
		 *
		 * @param array $new_codes
		 *  The language codes array to be inserted.
		 * @param integer $installation
		 *  Set if called from the install() function in extension.driver.php.
		 * @return boolean
		 *  True on success
		 */
		public function addColumnsToPageTable($new_codes = null, $installation = null) {
			if ( empty($new_codes) ) {
				$new_codes = self::$_language_codes_;

				if ( empty($new_codes) ) {
					//means there are no language codes in Configuration file
					return (boolean) true;
				}
			}

			$query_fields = "";

			//if called from install(), then just add all the fields
			if ( $installation == 1 ) {
				$this->_replaceDashes($new_codes);

				foreach ($new_codes as $language) {
					$query_fields .= "\nADD `page_lhandles_t_{$language}` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,";
					$query_fields .= "\nADD `page_lhandles_h_{$language}` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,";
				}
			}

			//else add only the language codes that are not present in `tbl_pages`
			else {
				$tbl_pages = Symphony::$Database->fetch('DESCRIBE `tbl_pages`');
				$fields_count = count($tbl_pages);
				for ($i = 0; $i < $fields_count; $i++) {
					$fields[] = $tbl_pages[$i]['Field'];
				}

				foreach ($new_codes as $language) {
					if ( !in_array("page_lhandles_t_".$language, $fields) ) {
						$query_fields .= "\nADD `page_lhandles_t_{$language}` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,";
						$query_fields .= "\nADD `page_lhandles_h_{$language}` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,";
					}
				}
			}

			if ( !empty($query_fields) ) {
				$query_fields = trim($query_fields, ',');
				$query = "ALTER TABLE `tbl_pages` ".$query_fields;

				return (boolean) Symphony::Database()->query($query);
			}

			return (boolean) true;
		}

		/**
		 * 
		 * Changes the source of navigation datasources.
		 *
		 * @param string $mode
		 *  Flag for datasource status.
		 */
		public function editAllNavDssTo($mode) {
			$DSManager = new DatasourceManager(Symphony::Engine());
			$datasources = array_keys( $DSManager->listAll() );

			if(is_array($datasources) && !empty($datasources)){

				foreach ($datasources as $value) {
					$filename = WORKSPACE . '/data-sources/data.' . $value . '.php';

					if ( file_exists($filename) ) {
						$datasource = file_get_contents($filename);

						if ( $this->_isDsTypeNavigation($datasource) ) {
							$newDS = call_user_func( array($this, "_setNavDsTo{$mode}"), $datasource );
							
							if ( $datasource != $newDS ) {
								$fileHandle = fopen($filename, 'w');
								fwrite($fileHandle, $newDS);
							}
						}
					}
				}
			}
		}
		
		/**
		 * 
		 * Changes the source of the datasource to PLH or SYMPHONY
		 *
		 * @param string $mode
		 *  Flag for datasource status.
		 * @param string $contents
		 *  A string containing original contents.
		 * @return string
		 *  The new contents of the file.
		 */
		public function editNavDsTo($mode, $contents) {

			if ( $this->_isDsTypeNavigation($contents) ) {
				return call_user_func( array($this, "_setNavDsTo{$mode}"), $contents );
			}
			
			return $contents;
		}



		/**
		 *
		 * Executes the given query and returns the new
		 *
		 * @param string $query
		 * @param string $old_value
		 * @param boolean $page_mode
		 * @param integer $last_parent
		 * @return string The translated url path
		 */
		private function _tryGetPagePathPiece($query, $old_value, &$page_mode, &$last_parent) {
			$page = null;
			$path = null;

			// run the query only if we are in page mode
			if ($page_mode) {

				try {
					$page = Symphony::Database()->fetch($query);
				} catch (DatabaseException $e) {
					if ( $e->getDatabaseErrorCode() == 1054 ) {
						//table column "$lhandle" doesn't exist. redirect to 404.
						FrontendPageNotFoundExceptionHandler::render($e);
					} else {
						// always re-trow non-handled exception
						throw $e;
					}
				}
			}
			else {
				
			}

			// Check if the value of an URL param matches the handle of a page.
			// Also, check that the current page has the right parent to
			// prevent translating parameters (fixes issue #8)
			if ( !empty($page) && $page_mode && $last_parent == $page[0]['parent']) {
				self::$_page_ascending_line[] = $page[0];
				$path = $page[0]['handle'] . '/';
				$last_parent = $page[0]['id'];

			} else {
				// we have reached the end of the page url
				// concat the other params without translating them
				$page_mode = false;
				$path = $old_value . '/';
			};

			return (string) $path;
		}
		
		/**
		 * 
		 * Replaces all dashes '-' with underscores '_'.
		 *
		 * @param array $language_codes
		 *  The target Language Codes array passed by reference.
		 */
		private function _replaceDashes(&$language_codes) {
			foreach ($language_codes as $key => $language) {
				$language_codes[$key] = str_replace('-', '_', $language);
			}
		}

		private function _isDsTypeNavigation($contents) {
			$navigationType = "return 'navigation';";
			$navigationTypePos = strpos($contents, $navigationType);

			return (boolean)!empty($navigationTypePos);
		}

		/**
		 * 
		 * Replace standard navigation template with PLH navigation template
		 *
		 * @param string $contents
		 *  Old datasource file contents.
		 * @return string
		 *  New datasource file contents.
		 */
		private function _setNavDsToPLH($contents){

			$was_edited = strpos($contents , "//PLH-COMM//");

			if ( empty($was_edited) ) {
			
				$old_template = "include(TOOLKIT . '/data-sources/datasource.navigation.php');";
				$pos = strpos($contents , $old_template);
	
				if ( !empty($pos) ) {
					$new_template = "include(EXTENSIONS . '/page_lhandles/lib/datasource.navigation.php');//PLH-COMM//";
					return (string) substr_replace($contents, $new_template, $pos, 0);
				}
				else {
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

			if ( !empty($was_edited) ) {

				$plh_template = "include(EXTENSIONS . '/page_lhandles/lib/datasource.navigation.php');//PLH-COMM//";
				$pos = strpos($contents , $plh_template);

				if ( !empty($pos) ) {
					return (string) str_replace($plh_template, '', $contents);
				}
				else {
					/* include(TOOLKIT . '/data-sources/datasource.navigation.php'); was not found
					 * or
					 * this DS was already changed
					 */
				}
			}

			return (string) $contents;
		}

	}
	
