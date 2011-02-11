<?php

	class Extension_page_lhandles extends Extension {
	
		public function about() {
			return array(
				'name'			=> 'Page LHandles',
				'version'		=> '1.0',
				'release-date'	=> '2011-02-10',
				'author'		=> array(
					'name'			=> 'Vlad Ghita',
					'email'			=> 'vlad_micutul@yahoo.com'
				),
				'description'	=> __('Allows localisation of page\'s handle.')
	 		);
		}
		
		public function getSubscribedDelegates() {
			return array(
				array(
					'page' => '/blueprints/pages/',
					'delegate' => 'AppendPageContent',
					'callback' => 'appendPageContent'
				),
				array(
					'page' => '/frontend/',
					'delegate' => 'FrontendPrePageResolve',
					'callback' => 'frontendPrePageResolve'
				),
				array(
					'page' => '/system/preferences/',
					'delegate' => 'Save',
					'callback' => 'savePreferences' 
				)
			);
		}
		
		public function appendPageContent($context) {
			$page = Symphony::Engine()->Page;
			
			if ($page->_context[0] == 'new' || $page->_context[0] == 'edit' || $page->_context[0] == 'template') {
				$form = $context['form'];

				$page_id = $page->_context[1];

				$page_lhandles = Symphony::Database()->fetch("
					SELECT
						p.page_lhandles_value
					FROM
						`tbl_pages` AS p
					WHERE
						p.id = '{$page_id}'
					LIMIT 1
				");
				
				$page_lhandles_value = $page_lhandles[0][page_lhandles_value];

				$fieldset = new XMLElement('fieldset');
				$fieldset->setAttribute('class', 'settings');
				$fieldset->appendChild(new XMLElement('legend', __('Page LHandles')));

				$group = new XMLElement('div');
				$group->setAttribute('class', 'group');

				$column = new XMLElement('div');
				$label = Widget::Label(__('Localised Handles'));
				
				$label->appendChild(Widget::Input('fields[page_lhandles_value]',$page_lhandles_value));
				$column->appendChild($label);
				$group->appendChild($column);

				$fieldset->appendChild($group);
				$form->prependChild($fieldset);
			}
		}

		public function frontendPrePageResolve($context) {
//			echo $context['page'].'<br />';
			
			$context['page'] = "companie";
			
//			die($context['page']);
		}
		
		public function savePreferences($context) {
			$savedLanguages = $context['settings']['language_redirect']['language_codes'];
			$storedLanguages = Symphony::Configuration()->get('language_codes', 'language_redirect');

			if ($savedLanguages !== $storedLanguages) {
				//if user changed the languages, update the tbl_pages with the new languages 
				$savedLanguages = explode( ',' , $savedLanguages );
				$storedLanguages = explode( ',' , $storedLanguages );
				$toCheckLanguages = array_diff_assoc( $savedLanguages, $storedLanguages );
				
				if ( !empty($toCheckLanguages) )
					return $this->_addColumnsPageTable($toCheckLanguages);
			}
			
			return true;
		}
		
		public function install(){
			$page_lhandles = $this->_addColumnsPageTable(null, 1);
			
			return $page_lhandles ? true : false;
		}
		
		public function uninstall(){
			$query = "ALTER TABLE `tbl_pages` ";			
			$fields = $this->_fetchFields();
			$columns = mysql_num_fields($fields);
			
			for ($i = 0; $i < $columns; $i++) {
				$fieldName = mysql_field_name($fields, $i);
				$isPageHandleField = strpos($fieldName, 'page_lhandle');
				
				if ( $isPageHandleField !== false )
					$query .= "\nDROP `$fieldName`,";
			}
			
			//remove the last comma ","
			$query = substr($query, 0, strlen($query)-1);

			Symphony::Database()->query($query);
		}
		
		public function enable() {
			$page_lhandles = $this->_addColumnsPageTable();
			
			return $page_lhandles ? true : false;
		}
		
		/**
		 * 
		 * Adds columns to tbl_pages table, depending on language codes from Language Redirect.
		 * ex: page_lhandle_ro, page_lhandle_en-us, page_lhandle_fr ...
		 */
		private function _addColumnsPageTable($toCheckLanguages = null, $fromInstall = null) {
			if ( empty($toCheckLanguages) )
				$toCheckLanguages = explode( ',' , Symphony::Configuration()->get('language_codes', 'language_redirect') );
			
			$queryFields = ""; 

			if ( $fromInstall == 1 ) {
				foreach ($toCheckLanguages as $key => $language) {
					$queryFields .= "\nADD `page_lhandles_$language` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,";
				}
			}
			else {
				$fields = $this->_fetchFields();
				$columns = mysql_num_fields($fields);
				for ($i = 0; $i < $columns; $i++) {
					$field_array[] = mysql_field_name($fields, $i);
				}

				foreach ($toCheckLanguages as $language) {
					$fieldName = "page_lhandles_".$language;
					if ( !in_array($fieldName, $field_array) ) {
						$queryFields .= "\nADD `$fieldName` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,";
					}
				}
			}
			
			$page_lhandles = true;
			
			if ( !empty($queryFields) ) {
				$queryFields = substr($queryFields, 0, strlen($queryFields)-1);
				$query = "ALTER TABLE `tbl_pages` ".$queryFields;
				$page_lhandles = Symphony::Database()->query($query);
			}
			
			return $page_lhandles ? true : false;
		}
		
		private function _fetchFields() {
			$db = Symphony::Configuration()->get('db', 'database');
			$tbl_prefix = Symphony::Configuration()->get('tbl_prefix', 'database');
			$table = $tbl_prefix.'pages';
			$fields = mysql_list_fields($db, $table);
			
			return $fields;
		}
	}

?>