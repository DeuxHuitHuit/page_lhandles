<?php

	require_once(TOOLKIT.'/class.datasource.php');
	require_once(EXTENSIONS.'/frontend_localisation/lib/class.FLPageManager.php');

	Class datasourceplh_page extends Datasource
	{

		public function about(){
			return array(
				'name' => 'PLH: Page',
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

		public function grab(&$param_pool = NULL){
			$result = new XMLElement('plh-page');

			$langs = FLang::getLangs();
			$fields = array();

			foreach( $langs as $lc ){
				$fields[] = "plh_t-{$lc}";
				$fields[] = "plh_h-{$lc}";
			}

			$result->appendChild(
				$this->_addPageXML(FLPageManager::instance()->listAll($fields), $this->_env['param']['current-page-id'], $langs)
			);

			return $result;
		}



		/**
		 * Add parent pages including current to XML output.
		 *
		 * @param array $pages - contains all pages data
		 * @param $page_id     - current page id
		 * @param $langs       - all supported language codes
		 *
		 * @return XMLElement - a pages XML ouput
		 */
		private function _addPageXML($pages, $page_id, $langs){
			$pageXML = new XMLElement('page', null, array(
				'handle' => $pages[$page_id]['handle'],
				'id' => $page_id
			));

			foreach( $langs as $lc ){
				$handle = $pages[$page_id]['plh_h-'.$lc];

				$itemXML = new XMLElement(
					'item',
					General::sanitize($handle),
					array(
						'lang' => $lc,
						'handle' => $handle
					)
				);

				$pageXML->prependChild($itemXML);
			}

			// if it has a parent, generate it, append current page and return parent
			if( !empty($pages[$page_id]['parent']) ){
				$parentXML = $this->_addPageXML($pages, $pages[$page_id]['parent'], $langs);
				$parentXML->appendChild($pageXML);

				return $parentXML;
			}

			// return this page
			return $pageXML;
		}

	}
