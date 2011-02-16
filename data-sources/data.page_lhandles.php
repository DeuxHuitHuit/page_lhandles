<?php

require_once(TOOLKIT . '/class.datasource.php');
	
Class datasourcepage_lhandles extends Datasource{
		
	public function about(){
			return array(
				'name' => 'Page LHandles',
				'author' => array(
					'name' => 'Vlad Ghita',
					'email' => 'vlad_micutul@yahoo.com'),
				'version' => '1.0',
				'release-date' => '2011-02-16',
				'description' => 'From Page LHandles extension. Retrieves the current pages\' localised titles and handles.');
	}
		
	public function allowEditorToParse(){
		return false;
	}

    public function grab(&$param_pool=NULL){
    	$result = new XMLElement('page-lhandles');
		
		$languageCodes = Extension_page_lhandles::getSupportedLanguageCodes();
		$languageCodesH = $languageCodes; 
		Extension_page_lhandles::replaceDashes($languageCodesH);
		
		$qselect = '';
   		foreach($languageCodesH as $language) {
			$qselect .= "p.page_lhandles_t_".$language.",";
			$qselect .= "p.page_lhandles_h_".$language.",";
		}
		Extension_page_lhandles::removeEndComma($qselect);

		$page_id = $this->_env['param']['current-page-id'];
		$page_lhandles_values = Symphony::Database()->fetch("
			SELECT 
				{$qselect}
			FROM
				`tbl_pages` AS p
			WHERE
				id = '{$page_id}'
			LIMIT 1
		");
				
		foreach ($languageCodes as $key => $language) {
			$itemXML =new XMLElement(
				'item', 
				$page_lhandles_values[0]['page_lhandles_t_'.$languageCodesH[$key] ], 
				array(
					'lang' => $language,
					'handle' => $page_lhandles_values[0][ 'page_lhandles_h_'.$languageCodesH[$key] ]
				)
			);
			
			$result->appendChild($itemXML);
		}

        return $result;
    }
}
