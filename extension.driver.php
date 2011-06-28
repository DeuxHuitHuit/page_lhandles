<?php

	require_once('lib/class.pagelhandles.php');
	require_once(EXTENSIONS . '/language_redirect/lib/class.languageredirect.php');

	class Extension_page_lhandles extends Extension {
		
		private $_plh;
		private $_first_pass;
		private $_language_redirect;
		
		public function __construct($args) {
			$this->_Parent = $args['parent'];
			
			$this->_plh = new PageLHandles();
			$this->_first_pass = 1;
			$this->_language_redirect = 'on';
		}
		
		public function about() {
			return array(
				'name'			=> 'Page LHandles',
				'version'		=> '1.2.1',
				'release-date'	=> '2011-06-28',
				'author'		=> array(
					'name'			=> 'Vlad Ghita',
					'email'			=> 'vlad_micutul@yahoo.com'
				),
				'description'	=> 'Allows localisation of current page handle, including its ascending line.'
	 		);
		}

		
		
		/*------------------------------------------------------------------------------------------------*/
		/*  Delegates  */
		/*------------------------------------------------------------------------------------------------*/
	
		public function getSubscribedDelegates() {
			return array(
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
					'delegate' => 'Save',
					'callback' => 'dSave' 
				),
				
				array(
					'page' => '/backend/',
					'delegate' => 'AppendPageAlert', 
					'callback' => 'dAppendPageAlert'
				),
				
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => 'dInitaliseAdminPageHead'
				),
				
				array(
					'page' => '/blueprints/datasources/',
					'delegate' => 'DatasourcePreCreate',
					'callback' => 'dDatasourcePreCreate'
				),
				
				array(
					'page' => '/blueprints/datasources/',
					'delegate' => 'DatasourcePreEdit',
					'callback' => 'dDatasourcePreEdit'
				),
				
			);
		}
		
		
		/**
		 * Append localised Title and Handle fields to Page edit menu.
		 * 
		 * @param array $context - see delegate description
		 */
		public function dAppendPageContent($context) {
			$page = Symphony::Engine()->Page;
			
			if ( in_array($page->_context[0] , array('new', 'edit', 'template')) ) {
				$this->_plh->appendPageFormContent($context['form'], $page->_context[1]);
			}
		}
		
		/**
		 * Replace URLs' Localised Handles with the Symphony corresponding Pages handles for further processing.
		 * 
		 * @param array $context - see delegate description
		 */
		public function dFrontendPrePageResolve($context) {

			if (   $this->_first_pass == 1 				//1. to prevent an endless loop if called after the 404 is generated 
				&& $this->_language_redirect == 'on'	//2. well ... duhh
				&& !empty($context['page'])				//3. "== empty" means that www.mydomain.com was accessed. No substitution needed.
				&& $context['page'] != '//'				//4. "== '//'" becomes after default index page has been retrieved. (After 3.)
			) {
				$this->_first_pass = 0 ;

				$url = $context['page'];
				MySQL::cleanValue($url);
				$old_url = preg_split('/\//', trim($url, '/'), -1, PREG_SPLIT_NO_EMPTY);
				
				$context['page'] = $this->_plh->processUrl($old_url);
			}
		}	
		
		/**
		 * On Preferences page, right before Saving the preferences, check whether or not 
		 * the language codes have been changed. If yes, integrate the new ones.
		 * 
		 * @param array $context - see delegate description
		 */
		public function dSave($context) {
			$saved_languages = explode( ',', General::Sanitize($context['settings']['language_redirect']['language_codes']) );
			$saved_languages = LanguageRedirect::cleanLanguageCodes($saved_languages);
			
			$stored_languages = LanguageRedirect::instance()->getSupportedLanguageCodes();
			
			$to_check_languages = array_diff($saved_languages, $stored_languages);
			
			if ( !empty($to_check_languages) ) {
				return $this->_plh->addColumnsToPageTable($to_check_languages);
			}
			
			return true;
		}
		
		/**
		 * Check if Language Redirect is enabled. Warning issued if not.
		 */
		public function dAppendPageAlert() {
			$em = $this->_Parent->ExtensionManager;

			$language_redirect = $em->fetchStatus('language_redirect');

			if($language_redirect != EXTENSION_ENABLED) {
				$this->_language_redirect = 'off';
				
				Administration::instance()->Page->Alert = new Alert(
					__('<code>Page LHandles</code> depends on <code>%s</code>. Make sure you have this extension installed and enabled.', array('Language Redirect')), 
					Alert::ERROR
				);
			}
			else {
				$this->_language_redirect = 'on';
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
		public function dDatasourcePreCreate($context) {
			$this->_plh->editDatasource('insert', $context['contents']);
			
			return true;
		}
		
		/**
		 * Edit navigation datasource content.
		 * 
		 * @param array $context - see delegate description
		 */
		public function dDatasourcePreEdit($context) {
			$this->_plh->editDatasource('insert', $context['contents']);
			
			return true;
		}
		
		
		
		/*------------------------------------------------------------------------------------------------*/
		/*  Installation  */
		/*------------------------------------------------------------------------------------------------*/
		
		public function install(){
			$this->_plh->editDatasource('insert');
			
			return (boolean)$this->_plh->addColumnsToPageTable(null, 1);
		}
		
		public function uninstall(){
			
			$this->_plh->editDatasource('delete');
			
			$query_fields = '';			
			$fields = Symphony::$Database->fetch('DESCRIBE `tbl_pages`');
			$fields_count = count($fields);
			
			for ($i = 0; $i < $fields_count; $i++) {
				$field_name = $fields[$i]['Field'];
				$is_page_lhandle = strpos($field_name, 'page_lhandles');
				
				if ( $is_page_lhandle !== false )
					$query_fields.= "\nDROP `$field_name`,";
			}
			
			if ( !empty($query_fields) ) {
				$query_fields = trim($query_fields, ',');
				$query = "ALTER TABLE `tbl_pages` ".$query_fields;
				return (boolean)Symphony::Database()->query($query);
			}

			return true;
		}
		
		public function enable() {
			$this->_plh->editDatasource('insert');
			
			return (boolean)$this->_plh->addColumnsToPageTable();
		}
		
		public function disable() {
			$this->_plh->editDatasource('delete');
		}
		
	}
?>