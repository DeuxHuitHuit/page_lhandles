<?php

	require_once(TOOLKIT . '/class.datasource.php');
	
	Class datasourceplh_page extends Datasource{

		public function about(){
			return array(
				'name' => 'PLH Page',
				'author' => array(
					'name' => 'Vlad Ghita',
					'email' => 'vlad_micutul@yahoo.com'
				),
				'version' => '1.1',
				'release-date' => '2011-11-15',
				'description' => 'From Page LHandles extension. Retrieves the current pages\' and it\'s parents localised titles and handles in all supported languages.');
		}

		public function allowEditorToParse(){
			return false;
		}

	    public function grab(&$param_pool=NULL){
	    	$result = new XMLElement('plh-page');
			
	    	$language_codes = FLang::instance()->ld()->languageCodes();
	    	$fields = array();
	    	
	    	foreach( $language_codes as $language_code ){
	    		$fields[] = "plh_t-{$language_code}";
	    		$fields[] = "plh_h-{$language_code}";
	    	}
	    	
	    	$result->appendChild(
	    		$this->_addPageXML(FLPageManager::instance()->listAll($fields), $this->_env['param']['current-page-id'], $language_codes)
	    	);

	        return $result;
	    }
		
	    
	    
	 	/**
	     * Add parent pages including current to XML output.
	     * 
	     * @param array $pages - contains all pages data
	     * @param $page_id - current page id
	     * @param $language_codes - all supported language codes
	     * 
	     * @return XMLElement - a pages XML ouput
	     */
		private function _addPageXML($pages, $page_id, $language_codes) {
			$pageXML = new XMLElement(
				'page',
				null,
				array(
					'handle' => $pages[$page_id]['handle'],
					'id' => $page_id
				)
			);
			
			foreach( $language_codes as $language_code ){
				$itemXML = new XMLElement(
					'item',
					General::sanitize( $pages[$page_id]['plh_t-'.$language_code] ),
					array(
						'lang' => $language_code,
						'handle' => $pages[$page_id]['plh_h-'.$language_code]
					)
				);

				$pageXML->prependChild($itemXML);
			}
			
			// if it has a parent, generate it, append current page and return parent
			if( !empty($pages[$page_id]['parent']) ){
				$parentXML = $this->_addPageXML($pages, $pages[$page_id]['parent'], $language_codes);
				$parentXML->appendChild($pageXML);
				
				return $parentXML;
			}
			
			// return this page
			return $pageXML;
		}
	    
	}
