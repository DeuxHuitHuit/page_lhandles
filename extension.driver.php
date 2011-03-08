<?php

require_once('lib/class.pagelhandles.php');

	class Extension_page_lhandles extends Extension {
		
		private $_pageLHandles;
		private $_firstPass;
		private $_languageRedirect;
		
		public function __construct($args) {
			$this->_Parent = $args['parent'];
			
			$this->_pageLHandles = new PageLHandles();
			$this->_firstPass = 1;
			$this->_languageRedirect = 'on';
		}
		
		public function about() {
			return array(
				'name'			=> 'Page LHandles',
				'version'		=> '1.1',
				'release-date'	=> '2011-03-7',
				'author'		=> array(
					'name'			=> 'Vlad Ghita',
					'email'			=> 'vlad.ghita@xandergroup.ro'
				),
				'description'	=> 'Allows localisation of current page handle, including its ascending line.'
	 		);
		}

	
		public function getSubscribedDelegates() {
			return array(
				array(
					'page' => '/blueprints/pages/',
					'delegate' => 'AppendPageContent',
					'callback' => 'append_page_content'
				),
				
				array(
					'page' => '/frontend/',
					'delegate' => 'FrontendPrePageResolve',
					'callback' => 'frontend_pre_page_resolve'
				),
				
				array(
					'page' => '/system/preferences/',
					'delegate' => 'Save',
					'callback' => 'save_preferences' 
				),
				
				array(
					'page' => '/backend/',
					'delegate' => 'AppendPageAlert', 
					'callback' => 'dependencies_check'
				),
				
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => 'initialise_admin_page_head'
				),
				
				array(
					'page' => '/blueprints/datasources/',
					'delegate' => 'DatasourcePreCreate',
					'callback' => 'datasource_pre_create'
				),
				
				array(
					'page' => '/blueprints/datasources/',
					'delegate' => 'DatasourcePreEdit',
					'callback' => 'datasource_pre_edit'
				),
				
			);
		}
		
		/**
		 * Append localised Title and Handle fields to Page edit menu.
		 * @param $context - see delegate description
		 */
		public function append_page_content($context) {
			$page = Symphony::Engine()->Page;
			
			if ($page->_context[0] == 'new' || $page->_context[0] == 'edit' || $page->_context[0] == 'template') {
				$this->_pageLHandles->append_page_form_content($context['form'], $page->_context[1]);
			}
		}
		
		/**
		 * Replace URLs' Localised Handles with the Symphony corresponding Pages handles for further processing.
		 * @param $context - see delegate description
		 */
		public function frontend_pre_page_resolve($context) {

			if (   $this->_firstPass == 1 				//1. to prevent an endless loop if called after the 404 is generated 
				&& $this->_languageRedirect == 'on'		//2. well ... duhh
				&& !empty($context['page'])				//3. "== empty" means that www.mydomain.com was accessed. No substitution needed.
				&& $context['page'] != '//'				//4. "== '//'" becomes after default index page has been retrieved. (After 3.)
			) {
				$this->_firstPass = 0 ;

				$url = $context['page'];
				MySQL::cleanValue($url);
				
				$oldURL = explode('/', $url);
				$newURL = $this->_pageLHandles->process_url($oldURL);
				$context['page'] = $newURL;
			}
		}	
		
		/**
		 * On Preferences page, right before Saving the preferences, check whether or not 
		 * the language codes have been changed. If yes, integrate the new ones.
		 * @param $context - see delegate description
		 */
		public function save_preferences($context) {
			$savedLanguages = explode( ',', General::Sanitize($context['settings']['language_redirect']['language_codes']) );
			PageLHandles::clean_language_codes($savedLanguages);
			
			$storedLanguages = PageLHandles::get_language_codes();
			
			$toCheckLanguages = array_diff($savedLanguages, $storedLanguages);
			
			if ( !empty($toCheckLanguages) ) {
				PageLHandles::replace_dashes($toCheckLanguages);
				return $this->_pageLHandles->add_columns_to_page_table($toCheckLanguages);
			}
			
			return true;
		}
		
		/**
		 * Check if Language Redirect is enabled. Warning issued if not.
		 */
		public function dependencies_check() {
			$ExtensionManager = $this->_Parent->ExtensionManager;

			$language_redirect = $ExtensionManager->fetchStatus('language_redirect');

			if($language_redirect != EXTENSION_ENABLED) {
				$this->_languageRedirect = 'off';
				
				Administration::instance()->Page->Alert = new Alert(
					__('<code>Page LHandles</code> depends on <code>%s</code>. Make sure you have this extension installed and enabled.', array('Language Redirect')), 
					Alert::ERROR
				);
			}
			else {
				$this->_languageRedirect = 'on';
			}

		}
		
		public function initialise_admin_page_head() {
			$callback = Administration::instance()->getPageCallback();
			if ( $callback['driver'] == 'blueprintspages' && ( $callback['context'][0] == 'edit' || $callback['context'][0] == 'new') ) {
				Administration::instance()->Page->addScriptToHead(URL . '/extensions/page_lhandles/assets/page_lhandles.blueprintspages.js', 202, false);
				Administration::instance()->Page->addStylesheetToHead(URL . '/extensions/page_lhandles/assets/page_lhandles.blueprintspages.css', "screen");
			}
		}

		public function datasource_pre_create($context) {
			$this->_pageLHandles->edit_datasource('insert', $context['contents']);
			
			return true;
		}
		
		public function datasource_pre_edit($context) {
			$this->_pageLHandles->edit_datasource('insert', $context['contents']);
			
			return true;
		}
		
		
		public function install(){
			$this->_pageLHandles->edit_datasource('insert');
			
			return (boolean)$this->_pageLHandles->add_columns_to_page_table(null, 1);
		}
		
		public function uninstall(){
			
			$this->_pageLHandles->edit_datasource('delete');
			
			$queryFields = '';			
			$fields = Symphony::$Database->fetch('DESCRIBE `tbl_pages`');
			$fieldsCount = count($fields);
			
			for ($i = 0; $i < $fieldsCount; $i++) {
				$fieldName = $fields[$i]['Field'];
				$isPageLHandle = strpos($fieldName, 'page_lhandles');
				
				if ( $isPageLHandle !== false )
					$queryFields.= "\nDROP `$fieldName`,";
			}
			
			if ( !empty($queryFields) ) {
				PageLHandles::remove_last_char($queryFields);
				$query = "ALTER TABLE `tbl_pages` ".$queryFields;
				return (boolean)Symphony::Database()->query($query);
			}

			return true;
		}
		
		public function enable() {
			$this->_pageLHandles->edit_datasource('insert');
			
			return (boolean)$this->_pageLHandles->add_columns_to_page_table();
		}
		
		public function disable() {
			$this->_pageLHandles->edit_datasource('delete');
		}
		
	}
?>