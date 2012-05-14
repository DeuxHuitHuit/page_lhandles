<?php

	if( !defined('__IN_SYMPHONY__') ) die('<h2>Error</h2><p>You cannot directly access this file</p>');



	require_once('lib/class.datasource.MultilingualNavigation.php');
	require_once('lib/class.PLHDatasourceManager.php');
	require_once('lib/class.PLHManagerURL.php');
	require_once(EXTENSIONS.'/frontend_localisation/lib/class.FLang.php');



	define_safe(PLH_NAME, 'Page LHandles');
	define_safe(PLH_GROUP, 'page_lhandles');



	class Extension_page_lhandles extends Extension
	{

		/**
		 * Supported operating modes.
		 *
		 * @var array
		 */
		private $op_modes;

		/**
		 * Knows if the first time the URL has been processed or not.
		 *
		 * @var bollean
		 */
		private $first_pass;



		public function __construct(){
			$this->first_pass = true;

			$this->op_modes = array(
				array(
					'handle' => 'strict',
					'name' => __('Strict'),
					'desc' => __('Default & Recommended mode.')
				),
				array(
					'handle' => 'relax',
					'name' => __('Relax'),
					'desc' => __('Compatibility mode for URL Router. Enable this to use URLs like: <code>site.com/clients/_param1_/projects/_param2_/</b>. Without this mode, if you have <code>_param1_ = `projects`</code>, it will be a collision and Page LHandles will fail.')
				)
			);
		}



		/*------------------------------------------------------------------------------------------------*/
		/*  Installation  */
		/*------------------------------------------------------------------------------------------------*/

		public function install(){
			PLHDatasourceManager::editAllNavDssTo('PLH');

			Symphony::Configuration()->set('op_mode', $this->op_modes[0]['handle'], PLH_GROUP);

			return (boolean)$this->_addColumnsToPageTable();
		}

		public function update($previous_version){
			if( version_compare($previous_version, '2.0', '<') ){
				$query_change = '';

				$fields = Symphony::Database()->fetch('DESCRIBE `tbl_pages`');
				$fields_count = count($fields);

				for( $i = 0; $i < $fields_count; $i++ ){
					$old_name = $fields[$i]['Field'];
					$is_page_lhandle = strpos($old_name, 'page_lhandles');

					if( $is_page_lhandle !== false ){
						$new_name = 'plh_'.str_replace('_', '-', substr($old_name, 14));

						$query_change .= sprintf(
							' CHANGE `%s` `%s` VARCHAR ( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,',
							$old_name, $new_name
						);
					}
				}

				if( !empty($query_change) ){
					$query = "ALTER TABLE `tbl_pages` ".trim($query_change, ',');

					return Symphony::Database()->query($query);
				}
			}

			if( version_compare($previous_version, '2.4', '<') ){
				Symphony::Configuration()->set('op_mode', $this->op_modes[0]['handle'], PLH_GROUP);
			}

			return true;
		}

		public function uninstall(){
			// Reset navigation datasources
			PLHDatasourceManager::editAllNavDssTo('SYMPHONY');

			// remove config settings
			Symphony::Configuration()->remove(PLH_GROUP);

			// remove columns from tbl_pages table
			$query_fields = '';
			$fields = Symphony::Database()->fetch('DESCRIBE `tbl_pages`');
			$fields_count = count($fields);

			for( $i = 0; $i < $fields_count; $i++ ){
				$field_name = $fields[$i]['Field'];
				$is_page_lhandle = strpos($field_name, 'plh');

				if( $is_page_lhandle !== false )
					$query_fields .= "\nDROP `$field_name`,";
			}

			if( !empty($query_fields) ){
				$query = "ALTER TABLE `tbl_pages` ".trim($query_fields, ',');

				return (boolean)Symphony::Database()->query($query);
			}

			return true;
		}

		public function enable(){
			PLHDatasourceManager::editAllNavDssTo('PLH');

			return true;
		}

		public function disable(){
			PLHDatasourceManager::editAllNavDssTo('SYMPHONY');

			return true;
		}



		/*------------------------------------------------------------------------------------------------*/
		/*  Delegates  */
		/*------------------------------------------------------------------------------------------------*/

		public function getSubscribedDelegates(){
			return array(
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => 'dInitaliseAdminPageHead'
				),

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
					'delegate' => 'AddCustomPreferenceFieldsets',
					'callback' => 'dAddCustomPreferenceFieldsets'
				),

				array(
					'page' => '/system/preferences/',
					'delegate' => 'CustomActions',
					'callback' => 'dCustomActions'
				),

				array(
					'page' => '/extensions/frontend_localisation/',
					'delegate' => 'FLSavePreferences',
					'callback' => 'dFLSavePreferences'
				),

				array(
					'page' => '/backend/',
					'delegate' => 'AppendPageAlert',
					'callback' => 'dAppendPageAlert'
				),

				array(
					'page' => '/blueprints/datasources/',
					'delegate' => 'DatasourcePreCreate',
					'callback' => 'dDatasourceNavigation'
				),

				array(
					'page' => '/blueprints/datasources/',
					'delegate' => 'DatasourcePreEdit',
					'callback' => 'dDatasourceNavigation'
				),
			);
		}



		/*------------------------------------------------------------------------------------------------*/
		/*  Frontend  */
		/*------------------------------------------------------------------------------------------------*/

		/**
		 * Process the URL and translate the localised page handles to Symphony handles.
		 *
		 * @param array $context - see delegate description
		 */
		public function dFrontendPrePageResolve($context){

			if( $this->first_pass === true //1. to prevent an endless loop if called after the 404 is generated
				&& $this->_validateDependencies() //2. make sure needed extensions are enabled
			){
				$this->first_pass = false;

				$url = MySQL::cleanValue($context['page']);

				$context['page'] = PLHManagerURL::lang2sym($url);
			}
		}



		/*------------------------------------------------------------------------------------------------*/
		/*  Pages  */
		/*------------------------------------------------------------------------------------------------*/

		/**
		 * Append localised Title and Handle fields to Page edit menu.
		 *
		 * @param array $context - see delegate description
		 */
		public function dAppendPageContent($context){
			$this->_appendAssets();

			$main_lang = FLang::getMainLang();
			$all_langs = FLang::getAllLangs();
			$langs = FLang::getLangs();

			$fieldset = new XMLElement('fieldset', null, array('class' => 'settings'));
			$fieldset->appendChild(new XMLElement('legend', __('Page LHandles')));

			$group = new XMLElement('div', null, array('class' => 'field-multilingual'));


			/* Tabs */

			$ul = new XMLElement('ul', '', array('class' => 'tabs'));

			foreach( $langs as $lc ){
				$li = new XMLElement(
					'li',
					$all_langs[$lc] ? $all_langs[$lc] : __('Unknown Lang : %s', array($lc)),
					array('class' => $lc.($lc === $main_lang ? ' active' : ''))
				);

				$lc === $main_lang ? $ul->prependChild($li) : $ul->appendChild($li);
			}

			$group->appendChild($ul);


			/* Content */

			foreach( $langs as $lc ){

				// title
				$group->appendChild(
					Widget::Label(
						__('Localised Title'),
						Widget::Input(
							"fields[plh_t-".$lc."]",
							$context['fields']['plh_t-'.$lc],
							'text',
							array('length', '30')
						),
						'tab-panel tab-'.$lc
					)
				);

				// handle
				$group->appendChild(
					Widget::Label(
						__('Localised URL Handle'),
						Widget::Input(
							"fields[plh_h-".$lc."]",
							$context['fields']['plh_h-'.$lc],
							'text',
							array('length', '30')
						),
						'tab-panel tab-'.$lc
					)
				);
			}


			$fieldset->appendChild($group);
			$context['form']->prependChild($fieldset);
		}



		/*------------------------------------------------------------------------------------------------*/
		/*  Preferences  */
		/*------------------------------------------------------------------------------------------------*/

		/**
		 * Display options on Preferences page.
		 *
		 * @param array $context
		 */
		public function dAddCustomPreferenceFieldsets($context){
			$group = new XMLElement('fieldset');
			$group->setAttribute('class', 'settings');
			$group->appendChild(new XMLElement('legend', __('Page LHandles')));

			$this->_appendUpdateButton($group);
			$this->_appendConsolidate($group);
			$this->_appendOperatingMode($group);

			$context['wrapper']->appendChild($group);
		}

		/**
		 * Convenience method; builds the update button
		 *
		 * @param XMLElement &$wrapper
		 */
		private function _appendUpdateButton(XMLElement &$wrapper){
			$main_lang = FLang::getMainLang();
			$all_langs = FLang::getAllLangs();

			$div = new XMLElement('div', NULL, array('id' => 'file-actions', 'class' => 'label'));

			$span = new XMLElement('span', NULL, array('class' => 'frame'));
			$span->appendChild(new XMLElement(
				'button',
				__('Fill test Names and Handles for Pages'),
				array('name' => 'action['.PLH_GROUP.'][update]', 'type' => 'submit')
			));
			$div->appendChild($span);
			$div->appendChild(new XMLElement('p', __('Updates every Page\'s empty Titles and Handles with the value for <b>%1$s - %2$s</b> language, prefixed by language code.<br />E.g. <code>Romana : Acasa => English : ENAcasa</code>', array($main_lang, $all_langs[$main_lang])), array('class' => 'help')));

			$wrapper->appendChild($div);
		}

		/**
		 * Convenience method; builds consolidate checkbox
		 *
		 * @param XMLElement &$wrapper
		 */
		private function _appendConsolidate(XMLElement &$wrapper){
			$label = Widget::Label(__('Consolidate entry data'));
			$label->appendChild(Widget::Input('settings['.PLH_GROUP.'][consolidate]', 'yes', 'checkbox', array('checked' => 'checked')));
			$wrapper->appendChild($label);
			$wrapper->appendChild(new XMLElement('p', __('Check this field if you want to consolidate database by <b>keeping</b> entry values of removed/old Language Driver language codes. Entry values of current language codes will not be affected.'), array('class' => 'help')));
		}

		/**
		 * Convenience method; builds operatin mode select
		 *
		 * @param XMLElement &$wrapper
		 */
		private function _appendOperatingMode(XMLElement &$wrapper){
			$label = Widget::Label(__('Operating mode'));

			$op_mode = Symphony::Configuration()->get('op_mode', PLH_GROUP);
			$options = array();
			$message = '';

			foreach( $this->op_modes as $idx => $op_mode_details ){
				$options[] = array(
					$op_mode_details['handle'],
					($op_mode_details['handle'] == $op_mode),
					$op_mode_details['name']
				);

				if( $idx > 0 ) $message .= "<br />";

				$message .= "<b>".$op_mode_details['name']."</b>: ".$op_mode_details['desc'];
			}

			$label->appendChild(Widget::Select('settings['.PLH_GROUP.'][op_mode]', $options));
			$wrapper->appendChild($label);
			$wrapper->appendChild(new XMLElement('p', $message, array('class' => 'help')));
		}

		/**
		 * Handle custom preferences actions
		 */
		public function dCustomActions(){
			if( isset($_POST['action'][PLH_GROUP]['update']) ){
				$this->_insertTestTitlesAndHandles();
			}
		}

		/**
		 * Save options from Preferences page
		 *
		 * @param array $context
		 *
		 * @return boolean
		 */
		public function dFLSavePreferences($context){
			try{
				$show_columns = Symphony::Database()->fetch("SHOW COLUMNS FROM `tbl_pages` LIKE 'plh_t-%';");
			}
			catch( Exception $e ){
				die('Pages table from Database doesn\'t exist. Grab a <a href="http://github.com/vlad-ghita/page_Lhandles/">newer version</a>  of Page LHandles extension.');
			}

			$columns = array();

			if( $show_columns ){
				foreach( $show_columns as $column ){
					$lc = substr($column['Field'], strlen($column['Field']) - 2);

					// If not consolidate option AND column lang_code not in supported languages codes -> Drop Column
					if( ($_POST['settings'][PLH_GROUP]['consolidate'] !== 'yes') && !in_array($lc, $context['new_langs']) ){
						Symphony::Database()->query("ALTER TABLE  `tbl_pages` DROP COLUMN `plh_t-{$lc}`");
						Symphony::Database()->query("ALTER TABLE  `tbl_pages` DROP COLUMN `plh_h-{$lc}`");
					} else{
						$columns[] = $column['Field'];
					}
				}
			}

			// Add new fields
			foreach( $context['new_langs'] as $lc ){
				// If column lang_code dosen't exist in the laguange add columns

				if( !in_array('plh_t-'.$lc, $columns) ){
					Symphony::Database()->query("ALTER TABLE  `tbl_pages` ADD COLUMN `plh_t-{$lc}` varchar(255) default NULL");
					Symphony::Database()->query("ALTER TABLE  `tbl_pages` ADD COLUMN `plh_h-{$lc}` int(11) unsigned NULL");
				}
			}

			$this->_insertTestTitlesAndHandles($context['new_langs']);

			return true;
		}



		/*------------------------------------------------------------------------------------------------*/
		/*  Notifications  */
		/*------------------------------------------------------------------------------------------------*/

		/**
		 * Issue a warning if dependencies are not met.
		 */
		public function dAppendPageAlert(){
			if( !$this->_validateDependencies() ){

				Administration::instance()->Page->pageAlert(
					__('<code>%1$s</code> depends on <code>%2$s</code>. Make sure you have this extension installed and enabled.', array(PLH_NAME, 'Frontend localisation')),
					Alert::ERROR
				);
			}
		}



		/*------------------------------------------------------------------------------------------------*/
		/*  Datasources  */
		/*------------------------------------------------------------------------------------------------*/

		/**
		 * Edit navigation datasource content.
		 *
		 * @param array $context - see delegate description
		 */
		public function dDatasourceNavigation($context){
			$context['contents'] = PLHDatasourceManager::editNavDsTo('PLH', $context['contents']);
		}



		/**
		 * For all Pages, fill the new added columns with the page_data from $reference_language.
		 *
		 * @param array $langs - languages to set data for.
		 *
		 * @return boolean
		 */
		private function _insertTestTitlesAndHandles($langs = array()){
			if( empty($langs) ){
				$langs = FLang::getLangs();

				// languages codes must exist
				if( empty($langs) ){
					return true;
				}
			}

			$main_lang = FLang::getMainLang();

			$pages_IDs = Symphony::Database()->fetchCol('id', 'SELECT `id` FROM `tbl_pages`');

			$query_fields = "`handle`,`title`,";

			foreach( $langs as $lc ){
				$query_fields .= "`plh_t-{$lc}`,";
				$query_fields .= "`plh_h-{$lc}`,";
			}

			foreach( $pages_IDs as $page_id ){
				$query = sprintf("SELECT %s FROM `tbl_pages` WHERE `id` = '%s'",
					trim($query_fields, ','), $page_id
				);

				$page_data = Symphony::Database()->fetch($query);

				$title = $page_data[0]["title"];
				$handle = $page_data[0]["handle"];

				unset($page_data[0]["handle"], $page_data[0]["title"]);

				$fields = '';

				foreach( $page_data[0] as $key => $value ){
					if( empty($value) ){
						$lang_code = substr($key, 6) !== $main_lang ? substr($key, 6) : '';

						$new_value = strpos($key, '_t-') === false ? $lang_code.$handle : strtoupper($lang_code).$title;

						$fields .= "\n `{$key}` = '{$new_value}',";
					}
				}

				if( !empty($fields) ){
					Symphony::Database()->query("UPDATE tbl_pages SET ".trim($fields, ',')." WHERE `id` = '{$page_id}';");
				}
			}

			return true;
		}

		/**
		 * Validate extension dependencies.
		 *
		 * @return boolean - true if dependencies are met, false otherwise
		 */
		private function _validateDependencies(){
			$fl_status = ExtensionManager::fetchStatus(array('handle' => 'frontend_localisation'));

			return (boolean) ($fl_status[0] === EXTENSION_ENABLED);
		}

		private function _appendAssets(){
			$this->assets_loaded = true;

			$page = Administration::instance()->Page;

			// multilingual stuff
			$fl_assets = URL.'/extensions/frontend_localisation/assets/frontend_localisation.multilingual_tabs';
			$page->addStylesheetToHead($fl_assets.'.css', 'screen', null, false);
			$page->addScriptToHead($fl_assets.'_init.js', null, false);
		}

	}
