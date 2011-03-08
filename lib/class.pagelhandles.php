<?php
require_once(TOOLKIT . '/class.datasourcemanager.php');

class PageLHandles
{
	private $_lang = array(						// [English name]
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
			'hy' => 'Հայերեն',							// Armenian
			'as' => 'অসমীয়া',								// Assamese
			'az' => 'azərbaycan',					// Azeri
			'eu' => 'euskera',						// Basque
			'be' => 'Беларуская',					// Belarusian
			'bn' => 'বাংলা',								// Bengali
			'bg' => 'Български',					// Bulgarian
			'ca' => 'Català',						// Catalan
			'zh-cn' => '简体中文 (China)',					// Chinese simplified script
			'zh-hk' => '繁體中文 (Hong Kong SAR)',			// Chinese traditional script
			'zh-mo' => '繁體中文 (Macau SAR)',				// Chinese traditional script
			'zh-sg' => '简体中文 (Singapore)',				// Chinese simplified script
			'zh-tw' => '繁體中文 (Taiwan)',				// Chinese traditional script
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
			'ka' => 'ქართული ენა',						// Georgian
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
			'kn' => 'ಕನ್ನಡ',							// Kannada
			'kk' => 'Қазақ',						// Kazakh
			'rw' => 'Kinyarwanda',					// Kinyarwanda
			'kok' => 'कोंकणी',							// Konkani
			'ko' => '한국어/조선말',							// Korean
			'kz' => 'Кыргыз',						// Kyrgyz
			'lv' => 'Latviešu',						// Latvian
			'lt' => 'Lietuviškai',					// Lithuanian
			'luo'=> 'Dholuo',						// Luo
			'ms' => 'Bahasa Melayu',				// Malay
			'mk' => 'Македонски',					// Macedonian
			'ml' => 'മലയാളം',								// Malayalam
			'mt' => 'Malti',						// Maltese
			'mr' => 'मराठी',							// Marathi
			'mn' => 'Монгол',						// Mongolian  (Cyrillic)
			'ne' => 'नेपाली',							// Nepali
			'nb-no' => 'Norsk bokmål',				// Norwegian Bokmål
			'nb' => 'Norsk bokmål',					// Norwegian Bokmål
			'nn-no' => 'Norsk nynorsk',				// Norwegian Nynorsk
			'nn' => 'Norsk nynorsk',				// Norwegian Nynorsk
			'no' => 'Norsk',						// Norwegian
			'or' => 'ଓଡ଼ିଆ',								// Oriya
			'ps' => 'پښتو',							// Pashto
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
			'ug' => 'ئۇيغۇرچە‎/Uyƣurqə/Уйғурчә',		// Uighur, Uyghur
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

	//holds the current language code
	private static $_currentLanguage;
	
	//holds the enabled language codes separated by dash '-'. eg: [en-us], [ro]
	private static $_languageCodes;
	
	//holds the enabled language codes separated by underscore '_'. eg: [en_us], [ro]
	private static $_languageCodesH;
	
	//ascending line of curent page, including the page
	private static $_pageAscendingLine;
	
	
	
	public function __construct() {
	
		self::$_pageAscendingLine = array();	
		
		$languageCodes = $this->_get_supported_language_codes();
		self::$_languageCodes = $languageCodes;
		
		self::replace_dashes($languageCodes);
		self::$_languageCodesH = $languageCodes;
		
		$urlLanguage = MySQL::cleanValue($_GET['language']);
		$urlRegion = MySQL::cleanValue($_GET['region']);
		self::$_currentLanguage = $urlLanguage . ( !empty($urlRegion) ? '_'.$urlRegion : '' );
	}
	
	/**
	 * Appends the Localisation Titles and Handles to the backend Page form.
	 * @param XMLElement $form
	 * @param Integer $pageID
	 */
	public function append_page_form_content(&$form, $pageID) {

		$fieldset = new XMLElement('fieldset');
		$fieldset->setAttribute('class', 'settings');
		$fieldset->appendChild(new XMLElement('legend', __('Page LHandles')));

		$group = new XMLElement('div');
		$group->setAttribute('class', 'group');

		$column = new XMLElement('div');
		$column->setAttribute('class', 'page_lhandles');

		/* Tabs */

		$ul = new XMLElement('ul');
		$ul->setAttribute('class', 'tabs');
		
		foreach ( self::$_languageCodes as $language ) {
			$class = $language . ($language == self::$_languageCodes[0] ? ' active' : '');
			$li = new XMLElement( 'li', ($this->_lang[$language] ? $this->_lang[$language] : __('Unknown Lang').' : '.$language) );
			$li->setAttribute('class', $class);

			$ul->appendChild($li);
		}

		$column->appendChild($ul);

		/* Localised Title */

		$qselect = '';
		foreach ( self::$_languageCodesH as $language ) {
			$qselect .= "p.page_lhandles_t_".$language.",";
		}
		self::remove_last_char($qselect);

		$page_lhandles_values = Symphony::Database()->fetch("
					SELECT 
					{$qselect}
					FROM
						`tbl_pages` AS p
					WHERE
						id = '{$pageID}'
					LIMIT 1
		");

		foreach ( self::$_languageCodes as $key => $language ) {
			$panel = Widget::Label(__('Localised Title'));
			$panel->setAttribute('class', 'tab-panel tab-'.$language);
		
			$input = Widget::Input("fields[page_lhandles_t_".self::$_languageCodesH[$key]."]", $page_lhandles_values[0][ 'page_lhandles_t_'.self::$_languageCodesH[$key] ]);
			$input->setAttribute('length', '30');
							
			$panel->appendChild($input);
			$column->appendChild($panel);
		}

		/* Localised URL Handle */

		$qselect = '';
		foreach ( self::$_languageCodesH as $language ) {
			$qselect .= "p.page_lhandles_h_".$language.",";
		}
		self::remove_last_char($qselect);

		$page_lhandles_values = Symphony::Database()->fetch("
					SELECT 
					{$qselect}
					FROM
						`tbl_pages` AS p
					WHERE
						id = '{$pageID}'
					LIMIT 1
		");						

		foreach ( self::$_languageCodes as $key => $language ) {
			$panel = Widget::Label(__('Localised URL Handle'));
			$panel->setAttribute('class', 'tab-panel tab-'.$language);
			
			$input = Widget::Input("fields[page_lhandles_h_".self::$_languageCodesH[$key]."]", $page_lhandles_values[0][ 'page_lhandles_h_'.self::$_languageCodesH[$key] ]);
			$input->setAttribute('length', '30');
			
			$panel->appendChild($input);
			$column->appendChild($panel);
		}
		
		$group->appendChild($column);
		$fieldset->appendChild($group);
		$form->prependChild($fieldset);
	}

	/**
	 * Adds columns to 'tbl_pages' table, depending on language codes from Language Redirect.
	 * @param Array $toCheckLanguages. 
	 * <p>The language codes array to be inserted.</p>
	 * @param Integer $installation. 
	 * <p>Set if called from the install() function in extension.driver.php.</p>
	 */
	public function add_columns_to_page_table($toCheckLanguages = null, $installation = null) {
		if ( empty($toCheckLanguages) ) {
			$toCheckLanguages = self::$_languageCodesH;

			if ( empty($toCheckLanguages) ) {
				//means there are no language codes in Configuration file
				return true;
			}
		}
			
		$queryFields = "";

		if ( $installation == 1 ) {
			//if called from install(), then just add all the fields
			foreach ($toCheckLanguages as $language) {
				$queryFields .= "\nADD `page_lhandles_t_{$language}` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,";
				$queryFields .= "\nADD `page_lhandles_h_{$language}` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,";
			}
		}
		else {
			//else add only the language codes that are not present in `tbl_pages`
			$fields = Symphony::$Database->fetch('DESCRIBE `tbl_pages`');
			$fieldsCount = count($fields);
			for ($i = 0; $i < $fieldsCount; $i++) {
				$fieldArray[] = $fields[$i]['Field'];
			}

			foreach ($toCheckLanguages as $language) {
				$fieldName = "page_lhandles_t_".$language;
				if ( !in_array($fieldName, $fieldArray) ) {
					$queryFields .= "\nADD `page_lhandles_t_{$language}` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,";
					$queryFields .= "\nADD `page_lhandles_h_{$language}` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,";
				}
			}
		}
			
		if ( !empty($queryFields) ) {
			self::remove_last_char($queryFields);
			$query = "ALTER TABLE `tbl_pages` ".$queryFields;
			
			return (boolean)Symphony::Database()->query($query);
		}
			
		return true;
	}
	
	/**
	 * <p>Process the accessed URL in browser and translate the localised page handles to Symphony handles.</p> 
	 * @param String $oldURL
	 * <p>Contains the URL with localised handles.</p>
	 * @return The new URL string containing Symphony handles.
	 */
	public function process_url($oldURL) {

		$path = '/';
		$boolPages = true;
		
		$querySelect = '`id`, `handle`';
		foreach ( self::$_languageCodesH as $language ) {
			$querySelect .= ', `page_lhandles_h_'.$language.'`';
			$querySelect .= ', `page_lhandles_t_'.$language.'`';
		}

		foreach ( $oldURL as $value ) {

			if ( !empty($value) ) {
				
				$lhandle = 'page_lhandles_h_' . self::$_currentLanguage;
				$query = "SELECT {$querySelect} FROM `tbl_pages` WHERE `{$lhandle}` = '{$value}'";
				
				try {
					$page = Symphony::Database()->fetch($query);
				} catch (DatabaseException $e) {
					if ( $e->getDatabaseErrorCode() == 1054 ) {
						//table column "$lhandle" doesn't exist. redirect to 404.
						FrontendPageNotFoundExceptionHandler::render($e);
					}
				}
					
				//$boolPage is used so if the value of an URL param matches the handle of a page the DB is not queried.
				// URL = domain/lang-code/pages/parameters
				if ( !empty($page) && $boolPages ) {
					self::$_pageAscendingLine[] = $page[0];
					$path .= $page[0]['handle'] .'/';
				}
				else {
					$boolPages = false;
					$path .= sprintf('%s/',$value);
				};
				
			}
		}
		
		return $path;
	}

	/**
	 * <p>Changes the source of the included datasource template action.</p>
	 * @param String $contents
	 * <p>A String containing the output of the file.</p>
	 * @return The new contents.
	 */
	public function edit_datasource($mode, &$contents = null) {

		if ( $mode == 'insert' && $contents != null ) {
			if ( $this->_is_source_navigation($contents) ) {
				self::_insert_at_datasource($contents);
			}
		}
		else {
			$DSManager = new DatasourceManager(Symphony::Engine());
			$datasources = array_keys( $DSManager->listAll() );
				
			if(is_array($datasources) && !empty($datasources)){

				foreach ($datasources as $value) {
					$filename = WORKSPACE . '/data-sources/data.' . $value . '.php';
						
					if ( file_exists($filename) ) {
						$datasource = file_get_contents($filename);
						
						if ( $this->_is_source_navigation($datasource) ) {
							$newDS = call_user_func( array($this, "_{$mode}_at_datasource"), $datasource );
							
							if ( $datasource != $newDS ) {
								$fileHandle = fopen($filename, 'w');
								fwrite($fileHandle, $newDS);
							}
						}
					}
				}
			}
		}
	}
	
	
	
	public static function get_current_language() {
		return self::$_currentLanguage;
	}
	
	public static function get_language_codes() {
		return self::$_languageCodes;
	}
	
	public static function get_language_codes_h() {
		return self::$_languageCodesH;
	}
	
	public static function get_page_ascending_line() {
		return self::$_pageAscendingLine;
	}
	
	/**
	 * Replaces all dashes '-' with underscores '_'.
	 * @param Array $languageCodes passed by reference.
	 * <p>The target Language Codes array.</p>
	 */
	public static function replace_dashes(&$languageCodes) {
		foreach ($languageCodes as $key => $language) {
			$languageCodes[$key] = str_replace('-', '_', $language);
		}
	}

	/**
	 * Removes last char from a string; used for removing last comma ',' from automatic generated SQL queries.
	 * 
	 * @param String $string passed by reference.
	 */
	public static function remove_last_char(&$string) {
		$string = substr($string, 0, strlen($string)-1);
	}

	/**
	 * Some sanitization for Language Codes Arrays
	 * @param Array $languageCodes passed by reference.
	 * <p>The array to be cleaned.</p>
	 */
	public static function clean_language_codes(&$languageCodes) {
		$languageCodes = array_map('trim', $languageCodes);
		$languageCodes = array_filter($languageCodes);
	}
	
	/**
	 * Insert string $needle in string $haystack starting from position $startPos in haystack.
	 * @param String $needle
	 * @param String $haystack
	 * @param Integer $startPos
	 * @return The new string.
	 */
	public static function str_ins_str($needle, $haystack, $startPos) {
		$output = substr($haystack, 0, $startPos);
		$output .= $needle;
		$output .= substr($haystack, $startPos);
		
		return $output;
	}
	
	
	
	private function _is_source_navigation($contents) {
		$navigationSource = "return 'navigation';";
		$navigationSourcePos = strpos($contents, $navigationSource);
		
		return (boolean)!empty($navigationSourcePos);
	}
	
	private function _get_supported_language_codes() {
		$supportedLanguageCodes = explode( ',', General::Sanitize( Symphony::Configuration()->get('language_codes', 'language_redirect') ) );
		self::clean_language_codes($supportedLanguageCodes);
			
		return $supportedLanguageCodes;
	}

	
	
	private static function _insert_at_datasource($contents){
		
		$includedTemplate = "include(TOOLKIT . '/data-sources/datasource.navigation.php');";
		$includedTemplatePos = strpos($contents , $includedTemplate);

		if ( !empty($includedTemplatePos) ) {
			$newIncludedTemplate = "include(EXTENSIONS . '/page_lhandles/lib/datasource.navigation.php');//PLH-COMM//";
			return self::str_ins_str($newIncludedTemplate, $contents, $includedTemplatePos);
		}
		else {
			/* include(TOOLKIT . '/data-sources/datasource.navigation.php'); was not found */
		}
		
		return $contents;
	}
	
	private static function _delete_at_datasource($contents){

		$wasEdited = strpos($contents , "//PLH-COMM//");
		
		if ( !empty($wasEdited) ) {
			
			$includedTemplate = "include(EXTENSIONS . '/page_lhandles/lib/datasource.navigation.php');//PLH-COMM//";
			$includedTemplatePos = strpos($contents , $includedTemplate);
			
			if ( !empty($includedTemplatePos) ) {
				return str_replace($includedTemplate, '', $contents);
			}
			else {
				/* include(TOOLKIT . '/data-sources/datasource.navigation.php'); was not found
				 * or
				 * this DS was already changed
				 */
			}	
		}
		
		return $contents;
	}
	
}