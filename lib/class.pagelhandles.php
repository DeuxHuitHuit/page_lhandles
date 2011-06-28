<?php

	if(!defined('__IN_SYMPHONY__')) die('<h2>Error</h2><p>You cannot directly access this file</p>');
	
	require_once(TOOLKIT . '/class.datasourcemanager.php');
	require_once(EXTENSIONS . '/language_redirect/lib/class.languager.php');
	
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
			return self::$_language_codes_;
		}
		
		public static function getPageAscendingLine() {
			return self::$_page_ascending_line;
		}
		
		
		
		/**
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
		 * Process the accessed URL in browser and translate the localised page handles to Symphony handles.
		 * 
		 * @param string $old_url
		 *  Contains the URL with localised handles.
		 * @return string
		 *  The new URL string containing Symphony handles.
		 */
		public function processUrl($old_url) {
	
			// if no language is set, return current URL
			if (strlen(LanguageRedirect::instance()->getLanguage()) < 1) {
				return $oldURL;
			}
			
			$path = '/';
			$bool_pages = true;
			
			$query_select = '`id`, `handle`';
			foreach ( self::$_language_codes_ as $language ) {
				$query_select .= ', `page_lhandles_h_'.$language.'`';
				$query_select .= ', `page_lhandles_t_'.$language.'`';
			}
	
			foreach ( $old_url as $value ) {
	
				if ( !empty($value) ) {
					$lhandle = 'page_lhandles_h_' . LanguageRedirect::instance()->getLanguage();
					$query = "SELECT {$query_select} FROM `tbl_pages` WHERE `{$lhandle}` = '{$value}'";
					
					try {
						$page = Symphony::Database()->fetch($query);
					} catch (DatabaseException $e) {
						if ( $e->getDatabaseErrorCode() == 1054 ) {
							//table column "$lhandle" doesn't exist. redirect to 404.
							FrontendPageNotFoundExceptionHandler::render($e);
						}
					}
						
					//check if the value of an URL param matches the handle of a page
					if ( !empty($page) && $bool_pages ) {
						self::$_page_ascending_line[] = $page[0];
						$path .= $page[0]['handle'] . '/';
					}
					else {
						$bool_pages = false;
						$path .= $value . '/';
					};
					
				}
			}
			
			return (string)$path;
		}
	
		/**
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
					return true;
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
					$fields[] = $fields[$i]['Field'];
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
				
				return (boolean)Symphony::Database()->query($query);
			}
				
			return true;
		}
		
		/**
		 * Changes the source of the included datasource template action.
		 * 
		 * @param string $mode
		 *  Flag for datasource status.
		 * @param string $contents
		 *  A string containing original contents.
		 * @return string
		 *  The new contents of the file.
		 */
		public function editDatasource($mode, &$contents = null) {
	
			if ( $mode == 'insert' && $contents != null ) {
				if ( $this->_isSourceNavigation($contents) ) {
					$contents = $this->_insertAtDatasource($contents);
				}
			}
			else {
				$DSManager = new DatasourceManager(Symphony::Engine());
				$datasources = array_keys( $DSManager->listAll() );
					
				if(is_array($datasources) && !empty($datasources)){
	
					foreach ($datasources as $value) {
						$filename = WORKSPACE . '/data-sources/data.' . $value . '.php';
							
						if ( file_exists($filename) ) {
							$datasource = file_get_contents($filename);
							
							if ( $this->_isSourceNavigation($datasource) ) {
								$newDS = call_user_func( array($this, "_{$mode}AtDatasource"), $datasource );
								
								if ( $datasource != $newDS ) {
									$fileHandle = fopen($filename, 'w');
									fwrite($fileHandle, $newDS);
								}
							}
						}
					}
				}
			}
		}
		
		
		
		/**
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
	
		private function _isSourceNavigation($contents) {
			$navigationSource = "return 'navigation';";
			$navigationSourcePos = strpos($contents, $navigationSource);
			
			return (boolean)!empty($navigationSourcePos);
		}
		
		/**
		 * Replace standard navigation template with PLH navigation template
		 * 
		 * @param string $contents
		 *  Old datasource file contents.
		 * @return string
		 *  New datasource file contents.
		 */
		private function _insertAtDatasource($contents){
			
			$old_template = "include(TOOLKIT . '/data-sources/datasource.navigation.php');";
			$pos = strpos($contents , $old_template);
	
			if ( !empty($pos) ) {
				$new_template = "include(EXTENSIONS . '/page_lhandles/lib/datasource.navigation.php');//PLH-COMM//";
				return substr_replace($contents, $new_template, $pos, 0);
			}
			else {
				/* include(TOOLKIT . '/data-sources/datasource.navigation.php'); was not found */
			}
			
			return $contents;
		}
		
		/**
		 * Replace PLH navigation template with standard navigation template.
		 * 
		 * @param string $contents
		 *  Old datasource file contents.
		 * @return string
		 *  New datasource file contents.
		 */
		private function _deleteAtDatasource($contents){

			$was_edited = strpos($contents , "//PLH-COMM//");

			if ( !empty($was_edited) ) {

				$plh_template = "include(EXTENSIONS . '/page_lhandles/lib/datasource.navigation.php');//PLH-COMM//";
				$pos = strpos($contents , $plh_template);

				if ( !empty($pos) ) {
					return str_replace($plh_template, '', $contents);
				}
				else {
					/* include(TOOLKIT . '/data-sources/datasource.navigation.php'); was not found
					 * or
					 * this DS was already changed
					 */
				}
			}

			return $contents;
		}
		
	}
