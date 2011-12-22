<?php
	
	if(!defined('__IN_SYMPHONY__')) die('<h2>Error</h2><p>You cannot directly access this file</p>');
	
	
	
	require_once('lib/class.PLHDatasourceManager.php');
	require_once('lib/class.PLHManagerURL.php');
	require_once(EXTENSIONS . '/frontend_localisation/lib/class.FLang.php');
	
	
	
	define_safe(PLH_NAME, 'Page LHandles');
	define_safe(PLH_GROUP, 'page_lhandles');
	
	
	
	class Extension_page_lhandles extends Extension {
		
		public function about() {
			return array(
					'name'			=> PLH_NAME,
					'version'		=> '2.2',
					'release-date'	=> '2011-12-22',
					'author'		=> array(
							array(
									'name'  => 'Vlad Ghita',
									'email' => 'vlad_micutul@yahoo.com'
							),
					),
					'description'	=> 'Allows localisation of Pages\' Titles and Handles.'
			);
		}
		
		/**
		 * PLH Datasource manager
		 *
		 * @var PLHDatasourceManager
		 */
		private $plh_dsm;
		
		/**
		 * Knows if the first time the URL has been processed or not.
		 *
		 * @var bollean
		 */
		private $first_pass;

		
		
		public function __construct($args) {
			$this->_Parent = $args['parent'];
			
			$this->plh_dsm = new PLHDatasourceManager();
			$this->first_pass = true;
		}

		
		
	/*------------------------------------------------------------------------------------------------
		Installation
	------------------------------------------------------------------------------------------------*/

		public function install(){
			$this->plh_dsm->editAllNavDssTo('PLH');

			return (boolean)$this->_addColumnsToPageTable();
		}
		 
		public function update($previous_version){
			if( version_compare($previous_version, '2.0', '<') ){
				$query_change = '';
				
				$fields = Symphony::Database()->fetch('DESCRIBE `tbl_pages`');
				$fields_count = count($fields);
	
				for( $i=0; $i<$fields_count; $i++ ){
					$old_name = $fields[$i]['Field'];
					$is_page_lhandle = strpos($old_name, 'page_lhandles');
	
					if( $is_page_lhandle !== false ){
						$new_name = 'plh_' . str_replace('_', '-', substr($old_name, 14));
						
						$query_change .= sprintf(
							' CHANGE `%s` `%s` VARCHAR ( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,',
							$old_name, $new_name
						);
					}
				}
	
				if( !empty($query_change) ){
					$query = "ALTER TABLE `tbl_pages` ".trim($query_change, ',');
					
					return Symphony::Database()->query($query);
				}
			}

			return true;
		}
		
		public function uninstall(){
			$this->plh_dsm->editAllNavDssTo('SYMPHONY');

			$query_fields = '';
			$fields = Symphony::Database()->fetch('DESCRIBE `tbl_pages`');
			$fields_count = count($fields);

			for ($i = 0; $i < $fields_count; $i++) {
				$field_name = $fields[$i]['Field'];
				$is_page_lhandle = strpos($field_name, 'plh');

				if ( $is_page_lhandle !== false )
					$query_fields.= "\nDROP `$field_name`,";
			}

			if ( !empty($query_fields) ) {
				$query = "ALTER TABLE `tbl_pages` ".trim($query_fields, ',');
				
				return (boolean)Symphony::Database()->query($query);
			}

			return true;
		}

		public function enable() {
			$this->plh_dsm->editAllNavDssTo('PLH');

			return true;
		}

		public function disable() {
			$this->plh_dsm->editAllNavDssTo('SYMPHONY');
			
			return true;
		}
		
		

		public function getSubscribedDelegates() {
			return array(
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => 'dInitaliseAdminPageHead'
				),
				
				array(
					'page' => '/blueprints/pages/',
					'delegate' => 'AppendPageContent',
					'callback' => 'dAppendPageContent'
				),

				array(
					'page' => '/frontend/',
					'delegate' => 'FrontendPrePageResolve',
					'callback' => 'dFrontendPrePageResolve'
				),

				array(
					'page' => '/system/preferences/',
					'delegate' => 'AddCustomPreferenceFieldsets',
					'callback' => 'dAddCustomPreferenceFieldsets'
				),
				
				array(
					'page' => '/system/preferences/',
					'delegate' => 'CustomActions',
					'callback' => 'dCustomActions'
				),
				
				array(
					'page' => '/system/preferences/',
					'delegate' => 'Save',
					'callback' => 'dSavePreferences'
				),

				array(
					'page' => '/backend/',
					'delegate' => 'AppendPageAlert',
					'callback' => 'dAppendPageAlert'
				),

				array(
					'page' => '/blueprints/datasources/',
					'delegate' => 'DatasourcePreCreate',
					'callback' => 'dDatasourceNavigation'
				),

				array(
					'page' => '/blueprints/datasources/',
					'delegate' => 'DatasourcePreEdit',
					'callback' => 'dDatasourceNavigation'
				),
			);
		}

		
		
		/**
		 * Append localised Title and Handle fields to Page edit menu.
		 *
		 * @param array $context - see delegate description
		 */
		public function dAppendPageContent($context) {
			$page_id = $page->_context[1];

			$all_languages = FLang::instance()->ld()->allLanguages();
			$language_codes = FLang::instance()->ld()->languageCodes();
			$reference_language = FLang::instance()->referenceLanguage();

			$fieldset = new XMLElement('fieldset');
			$fieldset->setAttribute('class', 'settings');
			$fieldset->appendChild(new XMLElement('legend', __('Page LHandles')));

			$group = new XMLElement('div');
			$group->setAttribute('class', 'group');

			$column = new XMLElement('div');
			$column->setAttribute('class', 'page_lhandles');


			/* Tabs */
				
			$ul = new XMLElement('ul', '', array('class' => 'tabs'));

			foreach( $language_codes as $language ){
				$li = new XMLElement(
					'li',
					($all_languages[$language] ? $all_languages[$language] : __('Unknown Lang : %s', array($language)) ),
					array('class' => $language . ($language == $reference_language ? ' active' : ''))
				);
				
				if( $language == $reference_language ){
					$ul->prependChild($li);
				}
				else{
					$ul->appendChild($li);
				}
			}

			$column->appendChild($ul);


			/* Localised Title */

			foreach( $language_codes as $key => $language ){
				$column->appendChild(
					Widget::Label(
						__('Localised Title'),
						Widget::Input(
							"fields[plh_t-".$language."]",
							$context['fields']['plh_t-'.$language],
							'text',
							array('length', '30')
						),
						'tab-panel tab-'.$language
					)
				);
			}


			/* Localised URL Handle */

			foreach( $language_codes as $key => $language ){
				$column->appendChild(
					Widget::Label(
						__('Localised URL Handle'),
						Widget::Input(
							"fields[plh_h-".$language."]",
							$context['fields']['plh_h-'.$language],
							'text',
							array('length', '30')
						),
						'tab-panel tab-'.$language
					)
				);
			}


			$group->appendChild($column);
			$fieldset->appendChild($group);
			$context['form']->prependChild($fieldset);
		}

		/**
		 * Process the URL and translate the localised page handles to Symphony handles.
		 *
		 * @param array $context - see delegate description
		 */
		public function dFrontendPrePageResolve($context) {

			if (   $this->first_pass == true 		//1. to prevent an endless loop if called after the 404 is generated
			    && $this->_validateDependencies()	//2. make sure needed extensions are enabled
			) {
				$this->first_pass = false;
				
				$url = MySQL::cleanValue( $context['page'] );
				
				$context['page'] = PLHManagerURL::instance()->lang2sym($url);
			}
		}

		/**
		 * Add a button on preferences page to Update all Pages' Title and Handle data.
		 *
		 * @param $context - see delegate description
		 */
		public function dAddCustomPreferenceFieldsets($context) {
			$group = new XMLElement('fieldset');
			$group->setAttribute('class', 'settings');
			$group->appendChild(new XMLElement('legend', __('Page LHandles')));


			$div = new XMLElement('div', NULL, array('id' => 'file-actions', 'class' => 'label'));
			
			$span = new XMLElement('span', NULL, array('class' => 'frame'));
			$span->appendChild(new XMLElement(
				'button',
				__('Fill test Names and Handles for Pages'),
				array('name' => 'action['.PLH_GROUP.'][update]', 'type' => 'submit')
			));

			$div->appendChild($span);
			
			
			$reference_language = FLang::instance()->referenceLanguage();
			$all_languages = FLang::instance()->ld()->allLanguages();
			
			$div->appendChild(new XMLElement(
				'p',
				__(
					'Updates every Page\'s empty Titles and Handles with the value for <b>%1$s - %2$s</b> language, prefixed by language code.<br />E.g. <code>Romana : Acasa => English : ENAcasa</code>',
					array($reference_language, $all_languages[$reference_language])
				),
				array('class' => 'help')
			));

			$group->appendChild($div);
			$context['wrapper']->appendChild($group);
		}
		
		/**
		 * On preferences page, personalize custom form actions.
		 */
		public function dCustomActions(){
			if(isset($_POST['action'][PLH_GROUP]['update'])){
				$this->_insertTestTitlesAndHandles();
			}
		}
		
		/**
		 * On Preferences page, right before saving the preferences, check whether or not
		 * the language codes have been changed. If yes, integrate the new ones.
		 *
		 * @param array $context - see delegate description
		 */
		public function dSavePreferences($context) {
			$saved_languages = FLang::instance()->ld()->getSavedLanguages($context);
			$stored_languages = FLang::instance()->ld()->languageCodes();

			$to_check_languages = array_diff($saved_languages, $stored_languages);

			if( !empty($to_check_languages) ){
				$this->_addColumnsToPageTable($to_check_languages);
			}
			
			$this->_insertTestTitlesAndHandles($saved_languages);

			return true;
		}

		/**
		 * Issue a warning if dependencies are not met.
		 */
		public function dAppendPageAlert() {
			if( !$this->_validateDependencies() ){
				
				Administration::instance()->Page->pageAlert(
					__('<code>%1$s</code> depends on <code>%2$s</code>. Make sure you have this extension installed and enabled.', array(PLH_NAME, 'Frontend localisation') ),
					Alert::ERROR
				);
			}
		}

		/**
		 * Add necessary assets to page head
		 */
		public function dInitaliseAdminPageHead() {
			$callback = Administration::instance()->getPageCallback();
			if ( $callback['driver'] == 'blueprintspages' && ( in_array($callback['context'][0], array('new', 'edit')) ) ) {
				Administration::instance()->Page->addScriptToHead(URL . '/extensions/page_lhandles/assets/page_lhandles.blueprintspages.js', 202, false);
				Administration::instance()->Page->addStylesheetToHead(URL . '/extensions/page_lhandles/assets/page_lhandles.blueprintspages.css', "screen");
			}
		}

		/**
		 * Edit navigation datasource content.
		 *
		 * @param array $context - see delegate description
		 */
		public function dDatasourceNavigation($context) {
			$context['contents'] = $this->plh_dsm->editNavDsTo('PLH', $context['contents']);

			return true;
		}
		

		
		/**
		 * For all Pages, fill the new added columns with the page_data from $reference_language.
		 *
		 * @param array $to_check_languages - languages to set data for.
		 */
		private function _insertTestTitlesAndHandles($to_check_languages = array()) {
			if ( empty($to_check_languages) ) {
				$to_check_languages = FLang::instance()->ld()->languageCodes();
	
				if ( empty($to_check_languages) ) {
					//means there are no language codes in Configuration file
					return (boolean) true;
				}
			}
			
			$reference_language = FLang::instance()->referenceLanguage();
			
			$pages_IDs = Symphony::Database()->fetchCol('id', 'SELECT `id` FROM `tbl_pages`');
			
			$query_fields = "`handle`,`title`,";
			
			foreach( $to_check_languages as $language ){
				$query_fields .= "`plh_t-{$language}`,";
				$query_fields .= "`plh_h-{$language}`,";
			}
			
			foreach( $pages_IDs as $page_id ){
				$query = sprintf("SELECT %s FROM `tbl_pages` WHERE `id` = '%s'",
					trim($query_fields,','),
					$page_id
				);
				
				$page_data = Symphony::Database()->fetch($query);
				
				$title = $page_data[0]["title"];
				$handle = $page_data[0]["handle"];
				
				unset($page_data[0]["handle"], $page_data[0]["title"]);
				
				$new_page_data = array();
				$query_update_fields = '';
				
				foreach( $page_data[0] as $key => $value ){
					if( empty($value) ){
						$is_title = strpos($key, '_t-');
						
						$lang_code = substr($key, 6);
						if( $lang_code == $reference_language ){
							$lang_code = '';
						}
						
						if( empty($is_title) ){
							$new_value = $lang_code . $handle; }
						else{
							$new_value = strtoupper($lang_code) . $title;
						}
						
						$query_update_fields .= "\n `{$key}` = '{$new_value}',";
					}
				}
				
				if( !empty($query_update_fields) ){
					$query = "UPDATE tbl_pages SET ".trim($query_update_fields, ',')." WHERE `id` = '{$page_id}';";
					
					Symphony::Database()->query($query);
				}
			}
			
			return true;
		}
		
		/**
		 *
		 * Adds columns to 'tbl_pages' table.
		 *
		 * @param array $language_codes - the language codes array to be inserted.
		 *
		 * @return boolean - true on success, false otherwise
		 */
		private function _addColumnsToPageTable($language_codes = array()) {
			if( empty($language_codes) ){
				$language_codes = FLang::instance()->ld()->languageCodes();

				if( empty($language_codes) ){
					//means there are no language codes in Configuration file
					return true;
				}
			}
			
			
			$tbl_pages = Symphony::Database()->fetch('DESCRIBE `tbl_pages`');
			$fields_count = count($tbl_pages);
			for( $i = 0; $i < $fields_count; $i++ ){
				$fields[$i] = $tbl_pages[$i]['Field'];
			}
			
			$query_fields = "";
			
			foreach( $language_codes as $language_code ){
				if( !in_array("plh_t-".$language_code, $fields) ){
					
					$query_fields .= "\nADD `plh_t-{$language_code}` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,";
					$query_fields .= "\nADD `plh_h-{$language_code}` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,";
				}
			}

			if( !empty($query_fields) ){
				$query = "ALTER TABLE `tbl_pages` ".trim($query_fields, ',');

				return (boolean) Symphony::Database()->query($query);
			}

			return true;
		}
		
		/**
		 * Validate extension dependencies.
		 *
		 * @return boolean - true if dependencies are met, false otherwise
		 */
		private function _validateDependencies(){
			return (boolean) Symphony::ExtensionManager()->fetchStatus('frontend_localisation') == EXTENSION_ENABLED;
		}
		
	}
