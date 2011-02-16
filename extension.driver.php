<?php

	class Extension_page_lhandles extends Extension {
		
		public $_lang = array(						// [English name]
			'ab' => 'аҧсуа бызшәа',					// Abkhazian
			'af' => 'Afrikaans',					// Afrikaans
			'sq' => 'shqip',						// Albanian
			'am' => 'አማርኛ',							// Amharic
			'ar-dz' => 'العربية (Algeria)',			// Arabic
			'ar-bh' => 'العربية (Bahrain)',			// Arabic
			'ar-eg' => 'العربية (Egypt)',			// Arabic
			'ar-iq' => 'العربية (Iraq)',			// Arabic
			'ar-jo' => 'العربية (Jordan)',			// Arabic
			'ar-kw' => 'العربية (Kuwait)',			// Arabic
			'ar-lb' => 'العربية (Lebanon)',			// Arabic
			'ar-ly' => 'العربية (Libya)',			// Arabic
			'ar-ma' => 'العربية (Morocco)',			// Arabic
			'ar-om' => 'العربية (Oman)',			// Arabic
			'ar-qa' => 'العربية (Qatar)',			// Arabic
			'ar-sa' => 'العربية (Saudi Arabia)',	// Arabic
			'ar-sy' => 'العربية (Syria)',			// Arabic
			'ar-tn' => 'العربية (Tunisia)',			// Arabic
			'ar-ae' => 'العربية (U.A.E.)',			// Arabic
			'ar-ye' => 'العربية (Yemen)',			// Arabic
			'ar' => 'العربية',						// Arabic
			'hy' => 'Հայերեն',						// Armenian
			'as' => 'অসমীয়া',							// Assamese
			'az' => 'azərbaycan',					// Azeri
			'eu' => 'euskera',						// Basque
			'be' => 'Беларуская',					// Belarusian
			'bn' => 'বাংলা',							// Bengali
			'bg' => 'Български',					// Bulgarian
			'ca' => 'Català',						// Catalan
			'zh-cn' => '简体中文 (China)',			// Chinese simplified script
			'zh-hk' => '繁體中文 (Hong Kong SAR)',	// Chinese traditional script
			'zh-mo' => '繁體中文 (Macau SAR)',		// Chinese traditional script
			'zh-sg' => '简体中文 (Singapore)',		// Chinese simplified script
			'zh-tw' => '繁體中文 (Taiwan)',			// Chinese traditional script
			'zh' => '中文',							// Chinese
			'hr' => 'Hrvatski',						// Croatian
			'cs' => 'čeština',						// Czech
			'da' => 'Dansk',						// Danish
			'dv' => 'ދިވެހި',							// Divehi
			'nl-be' => 'Nederlands (Belgium)',		// Dutch
			'nl' => 'Nederlands (Netherlands)',		// Dutch
			'en-au' => 'English (Australia)',		// English
			'en-bz' => 'English (Belize)',			// English
			'en-ca' => 'English (Canada)',			// English
			'en-ie' => 'English (Ireland)',			// English
			'en-jm' => 'English (Jamaica)',			// English
			'en-nz' => 'English (New Zealand)',		// English
			'en-ph' => 'English (Philippines)',		// English
			'en-za' => 'English (South Africa)',	// English
			'en-tt' => 'English (Trinidad)',		// English
			'en-gb' => 'English (United Kingdom)',	// English
			'en-us' => 'English (United States)',	// English
			'en-zw' => 'English (Zimbabwe)',		// English
			'en' => 'English',						// English
			'ee' => 'Ɛʋɛ',							// Ewe
			'et' => 'Eesti',						// Estonian
			'fo' => 'Føroyskt',						// Faeroese
			'fa' => 'فارسی',						// Farsi
			'fi' => 'Suomi',						// Finnish
			'fr-be' => 'Français (Belgium)',		// French (Belgium)
			'fr-ca' => 'Français canadien',			// French (Canada)
			'fr-lu' => 'Français (Luxembourg)',		// French
			'fr-mc' => 'Français (Monaco)',			// French
			'fr-ch' => 'Français (Switzerland)',	// French
			'fr' => 'Français',						// French
			'ff' => 'Fulfulde, Pulaar, Pular',		// Fula, Fulah, Fulani
			'gl' => 'Galego',						// Galician
			'gd' => 'Gàidhlig',						// Gaelic (Scottish)
			'ga' => 'Gaeilge',						// Gaelic (Irish)
			'gv' => 'Gaelg',						// Gaelic (Manx) (Isle of Man)
			'ka' => 'ქართული ენა',					// Georgian
			'de-at' => 'Deutsch (Austria)',			// German
			'de-li' => 'Deutsch (Liechtenstein)',	// German
			'de-lu' => 'Deutsch (Luxembourg)',		// German
			'de-ch' => 'Deutsch (Switzerland)',		// German
			'de' => 'Deutsch',						// German
			'el' => 'Ελληνικά',						// Greek
			'gu' => 'ગુજરાતી',							// Gujarati
			'ha' => 'هَوْسَ',							// Hausa
			'he' => 'עברית',						// Hebrew
			'hi' => 'हिंदी',							// Hindi
			'hu' => 'Magyar',						// Hungarian
			'is' => 'Íslenska',						// Icelandic
			'id' => 'Bahasa Indonesia',				// Indonesian
			'it-ch' => 'italiano (Switzerland)',	// Italian
			'it' => 'Italiano',						// Italian
			'ja' => '日本語',							// Japanese
			'kn' => 'ಕನ್ನಡ',						// Kannada
			'kk' => 'Қазақ',						// Kazakh
			'rw' => 'Kinyarwanda',					// Kinyarwanda
			'kok' => 'कोंकणी',							// Konkani
			'ko' => '한국어/조선말',					// Korean
			'kz' => 'Кыргыз',						// Kyrgyz
			'lv' => 'Latviešu',						// Latvian
			'lt' => 'Lietuviškai',					// Lithuanian
			'luo'=> 'Dholuo',						// Luo
			'ms' => 'Bahasa Melayu',				// Malay
			'mk' => 'Македонски',					// Macedonian
			'ml' => 'മലയാളം',							// Malayalam
			'mt' => 'Malti',						// Maltese
			'mr' => 'मराठी',							// Marathi
			'mn' => 'Монгол',						// Mongolian  (Cyrillic)
			'ne' => 'नेपाली',							// Nepali
			'nb-no' => 'Norsk bokmål',				// Norwegian Bokmål
			'nb' => 'Norsk bokmål',					// Norwegian Bokmål
			'nn-no' => 'Norsk nynorsk',				// Norwegian Nynorsk
			'nn' => 'Norsk nynorsk',				// Norwegian Nynorsk
			'no' => 'Norsk',						// Norwegian
			'or' => 'ଓଡ଼ିଆ',							// Oriya
			'ps' => 'پښتو',						// Pashto
			'pl' => 'polski',						// Polish
			'pt-br' => 'português brasileiro',		// Portuguese (Brasil)
			'pt' => 'português',					// Portuguese
			'pa' => 'پنجابی/ਪੰਜਾਬੀ',					// Punjabi
			'qu' => 'Runa Simi/Kichwa',				// Quechua
			'rm' => 'Romansch',						// Rhaeto-Romanic
			'ro-md' => 'Română (Moldova)',			// Romanian
			'ro' => 'Română',						// Romanian
			'rn' => 'kiRundi', 						// Rundi
			'ru-md' => 'Pyccĸий (Moldova)',			// Russian
			'ru' => 'Pyccĸий',						// Russian
			'sg' => 'yângâ tî sängö',				// Sango
			'sa' => 'संस्कृतम्',							// Sanskrit
			'sc' => 'Sardu',						// Sardinian
			'sr' => 'Srpski/српски',				// Serbian
			'sn' => 'chiShona',						// Shona
			'ii' => 'ꆇꉙ',							// Sichuan Yi
			'si' => 'සිංහල',						// Sinhalese, Sinhala
			'sk' => 'Slovenčina',					// Slovak
			'ls' => 'Slovenščina',					// Slovenian
			'so' => 'Soomaaliga/af Soomaali',		// Somali
			'st' => 'Sesotho',						// Sotho, Sutu
			'es-ar' => 'Español (Argentina)',		// Spanish
			'es-bo' => 'Español (Bolivia)',			// Spanish
			'es-cl' => 'Español (Chile)',			// Spanish
			'es-co' => 'Español (Colombia)',		// Spanish
			'es-cr' => 'Español (Costa Rica)',		// Spanish
			'es-do' => 'Español (Dominican Republic)',// Spanish
			'es-ec' => 'Español (Ecuador)',			// Spanish
			'es-sv' => 'Español (El Salvador)',		// Spanish
			'es-gt' => 'Español (Guatemala)',		// Spanish
			'es-hn' => 'Español (Honduras)',		// Spanish
			'es-mx' => 'Español (Mexico)',			// Spanish
			'es-ni' => 'Español (Nicaragua)',		// Spanish
			'es-pa' => 'Español (Panama)',			// Spanish
			'es-py' => 'Español (Paraguay)',		// Spanish
			'es-pe' => 'Español (Peru)',			// Spanish
			'es-pr' => 'Español (Puerto Rico)',		// Spanish
			'es-us' => 'Español (United States)',	// Spanish
			'es-uy' => 'Español (Uruguay)',			// Spanish
			'es-ve' => 'Español (Venezuela)',		// Spanish
			'es' => 'Español',						// Spanish
			'sw' => 'Kiswahili',					// Swahili
			'sv-fi' => 'svenska (Finland)',			// Swedish
			'sv' => 'svenska',						// Swedish
			'syr' => 'ܣܘܪܝܝܐ',						// Syriac
			'ta' => 'தமிழ்',							// Tamil
			'tt' => 'татарча/تاتارچا',				// Tatar
			'te' => 'తెలుగు',							// Telugu
			'th' => 'ภาษาไทย',						// Thai
			'ti' => 'ትግርኛ',							// Tigrinya
			'ts' => 'Xitsonga',						// Tsonga
			'tn' => 'Setswana',						// Tswana
			'tr' => 'Türkçe',						// Turkish
			'tk' => 'Түркмен',						// Turkmen
			'ug' => 'ئۇيغۇرچە‎/Uyƣurqə/Уйғурчә',	// Uighur, Uyghur
			'uk' => 'Українська',					// Ukrainian
			'ur' => 'اردو',							// Urdu
			'uz' => 'o\'zbek',						// Uzbek
			've' => 'Tshivenḓa',					// Venda
			'vi' => 'Tiếng Việt',					// Vietnamese
			'wa' => 'Walon',						// Waloon
			'cy' => 'Cymraeg',						// Welsh
			'wo' => 'Wolof',						// Wolof
			'xh' => 'isiXhosa',						// Xhosa
			'yi' => 'ייִדיש',						// Yiddish
			'yo' => 'Yorùbá',						// Yoruba
			'zu' => 'isiZulu',						// Zulu
		);
		
		/**
		 * Boolean to pass only once through "frontendPrePageResolve".
		 */
		private $_firstPass = 1;
		private $_languageRedirect = 'on';
		private $_installation = 0;
		
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
				),
				array(
					'page' => '/backend/',
					'delegate' => 'AppendPageAlert', 
					'callback' => 'dependenciesCheck'
				),
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => 'initialiseAdminPageHead'
				),
			);
		}
		
		/**
		 * Edit the fieldset of Page Edit, right before actions take place
		 * @param $context - see delegate description
		 */
		public function appendPageContent($context) {
			$page = Symphony::Engine()->Page;
			
			if ($page->_context[0] == 'new' || $page->_context[0] == 'edit' || $page->_context[0] == 'template') {
				$form = $context['form'];

				$languageCodes = $this->_getSupportedLanguageCodes();
				$languageCodesH = $languageCodes; 
				$this->_replaceDashes($languageCodesH);
				
				$fieldset = new XMLElement('fieldset');
				$fieldset->setAttribute('class', 'settings');
				$fieldset->appendChild(new XMLElement('legend', __('Page LHandles')));

				$group = new XMLElement('div');
				$group->setAttribute('class', 'group');

				$column = new XMLElement('div');
				$column->setAttribute('class', 'page_lhandles');
				
				$label = Widget::Label(__('Localisation for URL Handle'));
				$column->appendChild($label);
				
				/* Tabs */
				
				$ul = new XMLElement('ul');
				$ul->setAttribute('class', 'tabs');
				
				foreach($languageCodes as $language) {
					$class = $language . ($language == $languageCodes[0] ? ' active' : '');
					$li = new XMLElement('li',($this->_lang[$language] ? $this->_lang[$language] : __('Unknown Lang')));	
					$li->setAttribute('class', $class);
					
					$ul->appendChild($li);
				}
				
				$column->appendChild($ul);
						
				/* Inputs */
				
				$page_id = $page->_context[1];
				
				$qselect = '';
				foreach($languageCodesH as $language) {
					$qselect .= "p.page_lhandles_".$language.",";
				}
				$this->_removeEndComma($qselect);
				
				$page_lhandles_values = Symphony::Database()->fetch("
					SELECT 
						{$qselect}
					FROM
						`tbl_pages` AS p
					WHERE
						id = '{$page_id}'
					LIMIT 1
				");						
				
				foreach($languageCodes as $key => $language) {
					$panel = Widget::Label();
					$panel->setAttribute('class', 'tab-panel tab-'.$language);
					
					$input = Widget::Input(
						"fields[page_lhandles_".$languageCodesH[$key]."]", $page_lhandles_values[0][ 'page_lhandles_'.$languageCodesH[$key] ]
					);
					$input->setAttribute('length', '30');
					
					$panel->appendChild($input);
					$column->appendChild($panel);
				}
				
				$group->appendChild($column);
				$fieldset->appendChild($group);
				$form->prependChild($fieldset);
			}
		}
		
		/**
		 * Replaces URLs' Localised Handle with the Symphony corresponding Page handle for further processing 
		 * @param $context - see delegate description
		 */
		public function frontendPrePageResolve($context) {
			//checks to see if the page is resolved for the first time. 
			//If yes, then is the requested URL in browser, if no, it is probably a "page not found 404" page redirect.
			//Used to prevent an endless loop
			if ( $this->_firstPass == 1 && $this->_languageRedirect == 'on') {
				$this->_firstPass = 0;
				$oldPage = explode('/', $context['page'],3);
				$lhandle = $oldPage[1];
				
				$urlLanguage = $_GET['language'];
				$urlRegion = $_GET['region'];
				
				$lfield = 'page_lhandles_' . $urlLanguage . (!empty($urlRegion) ? '_'.$urlRegion : '');
				
				try {
					$result = Symphony::Database()->fetch("
						SELECT 
							p.handle
						FROM
							`tbl_pages` AS p
						WHERE
							{$lfield} = '{$lhandle}'
						LIMIT 1
					");
				}
				catch (DatabaseException $e) {					
					if ( $e->getDatabaseErrorCode() == 1054 ) {
						//table column "$lfield" doesn't exist. redirect to 404.
						FrontendPageNotFoundExceptionHandler::render($e);
					}
				}
	
				if ( empty($result) ) {
					//no match. redirect to 404
					$e = new FrontendPageNotFoundException();
					FrontendPageNotFoundExceptionHandler::render($e);
				}
				else {
					//valid page. proceed with translated handle
					$context['page'] = '/'.$result[0]['handle'].'/'.$oldPage[2];
				}
			}
			
		}	
		
		/**
		 * On Preferences page, right before Saving the preferences, check whether or not 
		 * the language codes have been changed. If yes, integrate the new ones.
		 * @param $context - see delegate description
		 */
		public function savePreferences($context) {
			$savedLanguages = explode(',',$context['settings']['language_redirect']['language_codes']);
			$this->_cleanLanguageCodes($savedLanguages);
			$storedLanguages = $this->_getSupportedLanguageCodes();
			
			$toCheckLanguages = array_diff($savedLanguages, $storedLanguages);
			if ( !empty($toCheckLanguages) ) {
				return $this->_addColumnsPageTable($toCheckLanguages);
			}
			
			return true;
		}
		
		/**
		 * Check if Language Redirect is enabled. Warning issued if not.
		 */
		public function dependenciesCheck() {
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
		
		public function initialiseAdminPageHead() {
			$callback = Administration::instance()->getPageCallback();
			if ( $callback['driver'] == 'blueprintspages' && ( $callback['context'][0] == 'edit' || $callback['context'][0] == 'new') ) {
				Administration::instance()->Page->addScriptToHead(URL . '/extensions/page_lhandles/assets/page_lhandles.blueprintspages.js', 202, false);
				Administration::instance()->Page->addStylesheetToHead(URL . '/extensions/page_lhandles/assets/page_lhandles.blueprintspages.css', "screen");
			}
		}
		
		public function install(){
			$this->_installation = 1;
			
			return (boolean)$this->_addColumnsPageTable();
		}
		
		public function uninstall(){
			$queryFields = '';			
			$fields = Symphony::$Database->fetch('DESCRIBE `tbl_pages`');
			$fieldsCount = count($fields);
			
			for ($i = 0; $i < $fieldsCount; $i++) {
				$fieldName = $fields[$i]['Field'];
				$isPageLHandle = strpos($fieldName, 'page_lhandle');
				
				if ( $isPageLHandle !== false )
					$queryFields.= "\nDROP `$fieldName`,";
			}
			
			if ( !empty($queryFields) ) {
				$this->_removeEndComma($queryFields);
				$query = "ALTER TABLE `tbl_pages` ".$queryFields;
				return (boolean)Symphony::Database()->query($query);
			}

			return true;
		}
		
		public function enable() {
			return (boolean)$this->_addColumnsPageTable();
		}
		
		/**
		 * Adds columns to tbl_pages table, depending on language codes from Language Redirect.
		 * ex: page_lhandles_ro, page_lhandles_en-us, page_lhandles_fr ...
		 */
		private function _addColumnsPageTable($toCheckLanguages = null) {
			if ( empty($toCheckLanguages) ) {
				$toCheckLanguages = $this->_getSupportedLanguageCodes();
				
				if ( empty($toCheckLanguages) ) {
					//means there are no language codes in Configuration file
					return true;
				}
			}
			
			$this->_replaceDashes($toCheckLanguages);
			
			$queryFields = ""; 

			if ( $this->_installation == 1 ) {
				//if called from install(), then just add all the fields
				foreach ($toCheckLanguages as $language) {
					$queryFields .= "\nADD `page_lhandles_{$language}` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,";
				}
				$this->_installation = 0;
			}
			else {
				//else add only the language codes that are not present in `tbl_pages`
				$fields = Symphony::$Database->fetch('DESCRIBE `tbl_pages`');
				$fieldsCount = count($fields);
				for ($i = 0; $i < $fieldsCount; $i++) {
					$fieldArray[] = $fields[$i]['Field'];
				}

				foreach ($toCheckLanguages as $language) {
					$fieldName = "page_lhandles_".$language;
					if ( !in_array($fieldName, $fieldArray) ) {
						$queryFields .= "\nADD `$fieldName` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,";
					}
				}
			}
			
			if ( !empty($queryFields) ) {
				$this->_removeEndComma($queryFields);
				$query = "ALTER TABLE `tbl_pages` ".$queryFields;
				return (boolean)Symphony::Database()->query($query);
			}
			
			return true;
		}

		private function _getSupportedLanguageCodes() {
			$supportedLanguageCodes = explode(',', General::Sanitize(Symphony::Engine()->Configuration->get('language_codes', 'language_redirect')));
			$this->_cleanLanguageCodes($supportedLanguageCodes);
			
			return $supportedLanguageCodes;
		}
		
		private function _cleanLanguageCodes(&$languageCodes) {
			$languageCodes = array_map('trim', $languageCodes);
			$languageCodes = array_filter($languageCodes);
		}
		
		private function _removeEndComma(&$string) {
			$string = substr($string, 0, strlen($string)-1);
		}
		
		private function _replaceDashes(&$languageCodes) {
			foreach ($languageCodes as $key => $language) {
				$languageCodes[$key] = str_replace('-', '_', $language);
			}
		}
	
	}
?>