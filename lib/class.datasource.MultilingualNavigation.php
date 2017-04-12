<?php

	require_once(TOOLKIT . '/class.datasource.php');
	require_once(TOOLKIT . '/data-sources/class.datasource.navigation.php');



	Class MultilingualNavigationDatasource extends NavigationDatasource {

		public function buildMultilingualPageXML($page, $page_types, $qf) {
			$lang_code = FLang::getLangCode();

			$oPage = new XMLElement('page');
			$oPage->setAttribute('handle', $page['handle']);
			$oPage->setAttribute('id', $page['id']);
			// keep current first
			$oPage->appendChild(new XMLElement(
				'item',
				General::sanitize($page['plh_t-'.$lang_code]),
				array(
					'lang' => $lang_code,
					'handle' => $page['plh_h-'.$lang_code],
				)
			));
			
			// add others
			foreach( FLang::getLangs() as $lc ){
				if($lang_code != $lc) {
					$oPage->appendChild(new XMLElement(
						'item',
						General::sanitize($page['plh_t-'.$lc]),
						array(
							'lang' => $lc,
							'handle' => $page['plh_h-'.$lc],
						)
					));
				}
			}

			if(in_array($page['id'], array_keys($page_types))) {
				$xTypes = new XMLElement('types');
				foreach($page_types[$page['id']] as $type) {
					$xTypes->appendChild(new XMLElement('type', $type));
				}
				$oPage->appendChild($xTypes);
			}

			if($page['children'] != '0') {
				if($children = PageManager::fetch(false, array($qf.'id, handle, title'), array(sprintf('`parent` = %d', $page['id'])))) {
					foreach($children as $c) $oPage->appendChild($this->buildMultilingualPageXML($c, $page_types, $qf));
				}
			}

			return $oPage;
		}

		public function execute(array &$param_pool = null) {
			$result = new XMLElement($this->dsParamROOTELEMENT);
			$type_sql = $parent_sql = null;

			if(trim($this->dsParamFILTERS['type']) != '') {
				$type_sql = $this->__processNavigationTypeFilter($this->dsParamFILTERS['type'], $this->__determineFilterType($this->dsParamFILTERS['type']));
			}

			if(trim($this->dsParamFILTERS['parent']) != '') {
				$parent_sql = $this->__processNavigationParentFilter($this->dsParamFILTERS['parent']);
			}

			$query_fields = "";
			$qf = "";

			foreach( FLang::getLangs() as $lc ){
				$qf .= "`plh_t-{$lc}`,";
				$qf .= "`plh_h-{$lc}`,";
			}

			// Build the Query appending the Parent and/or Type WHERE clauses
			$query = sprintf("
					SELECT DISTINCT {$qf}p.id, p.title, p.handle, (SELECT COUNT(id) FROM `tbl_pages` WHERE parent = p.id) AS children
					FROM `tbl_pages` AS p
					LEFT JOIN `tbl_pages_types` AS pt ON (p.id = pt.page_id)
					WHERE 1 = 1
					%s
					%s
					ORDER BY p.`sortorder` ASC
				",
				// Add Parent SQL
				!is_null($parent_sql) ? $parent_sql : " AND p.parent IS NULL ",
				// Add Types SQL
				!is_null($type_sql) ? $type_sql : ""
			);

			$pages = Symphony::Database()->fetch($query);

			if((!is_array($pages) || empty($pages))){
				if($this->dsParamREDIRECTONEMPTY == 'yes'){
					throw new FrontendPageNotFoundException;
				}
				$result->appendChild($this->__noRecordsFound());
			}

			else {
				// Build an array of all the types so that the page's don't have to do
				// individual lookups.
				$page_types = PageManager::fetchAllPagesPageTypes();

				foreach($pages as $page) {
					$result->appendChild($this->buildMultilingualPageXML($page, $page_types, $qf));
				}
			}

			return $result;
		}
	}
