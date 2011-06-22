<?php

require_once(TOOLKIT . '/class.datasource.php');
	
Class datasourceplh_page extends Datasource{
		
	public function about(){
			return array(
				'name' => 'PLH Page',
				'author' => array(
					'name' => 'Vlad Ghita',
					'email' => 'vlad_micutul@yahoo.com'),
				'version' => '1.0',
				'release-date' => '2011-02-16',
				'description' => 'From Page LHandles extension. Retrieves the current pages\' and it\'s parents localised titles and handles in all supported languages.');
	}
		
	public function allowEditorToParse(){
		return false;
	}

    public function grab(&$param_pool=NULL){
    	$result = new XMLElement('plh-page');
		
    	$pages = PageLHandles::getPageAscendingLine();
    	$languageCodes = LanguageR::instance()->getSupportedLanguageCodes();
    	$languageCodesH = PageLHandles::getLanguageCodes_();
		
    	$i = 0;
		
    	$result->appendChild(
    		$this->_addPageXML($pages, $i, $languageCodes, $languageCodesH)
    	);

        return $result;
    }
    
    private function _addPageXML($pages, $i, $languageCodes, $languageCodesH) {
    	
    	$pageXML = new XMLElement(
    			'page', 
    			null,
		    	array(
		    		'handle' => $pages[$i]['handle'],
					'id' => $pages[$i]['id']
    			)
    	);
    	
    	foreach ($languageCodes as $key => $language) {
    		$itemXML = new XMLElement(
					'item', 
    				htmlspecialchars ( $pages[$i]['page_lhandles_t_'.$languageCodesH[$key] ] , ENT_NOQUOTES ), // espace &
    				array(
						'lang' => $language,
						'handle' => $pages[$i][ 'page_lhandles_h_'.$languageCodesH[$key] ]
    				)
    		);

    		$pageXML->appendChild($itemXML);
    	}
    	
    	if ( $i < count($pages)-1 ) {
    		
	    	$pageXML->appendChild(
	    		$this->_addPageXML($pages, $i + 1, $languageCodes, $languageCodesH)
	    	);
	    	
    	}
    	
    	return $pageXML;
    	
    }
    
}
