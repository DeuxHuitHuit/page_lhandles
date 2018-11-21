<?php

if (!defined('__IN_SYMPHONY__')) die('<h2>Error</h2><p>You cannot directly access this file</p>');

require_once(TOOLKIT.'/class.datasourcemanager.php');

final class PLHDatasourceManager
{
    private static $plh_template_extends = "extends MultilingualNavigationDatasource";
    private static $sym_template_extends = "extends NavigationDatasource";
    private static $plh_template_class = "php\n\nrequire_once(EXTENSIONS.'/page_lhandles/lib/class.datasource.MultilingualNavigation.php');\n\nclass datasource";
    private static $sym_template_class = "php\n\nclass datasource";
    private static $grab_template_sign = "public function grab(";
    private static $execute_template_sign = "public function execute(";
    private static $grab_template_type = 'grab(&$param_pool = null)';
    private static $execute_template_type = 'execute(array &$param_pool = null)';
    private static $grab_template_par = "parent::grab(";
    private static $execute_template_par = "parent::execute(";

    /**
     * Changes the source of navigation datasources.
     * @static
     * @param string $mode - flag for datasource status.
     */
    public static function editAllNavDssTo($mode)
    {
        $datasources = array_keys(DatasourceManager::listAll());

        if (is_array($datasources) && !empty($datasources)) {
            foreach ($datasources as $value) {
                $filename = WORKSPACE.'/data-sources/data.'.$value.'.php';

                if (is_file($filename) && is_writable($filename)) {
                    $old_content = file_get_contents($filename);

                    if (self::isDsTypeNavigation($old_content)) {
                        if (method_exists(get_class(), "setNavDsTo{$mode}")) {
                            $new_content = call_user_func(array('self', "setNavDsTo{$mode}"), $old_content);
                            $new_content = self::setNavDsToExecute($new_content);

                            General::writeFile($filename, $new_content);
                        }
                    }
                }
            }
        }
    }

    public static function reg_replace($pattern, $replacement, $contents)
    {
        return preg_replace('/'.preg_quote($pattern, '/').'/i', $replacement, $contents);
    }

    /**
     * Changes the source of the datasource to PLH or SYMPHONY
     *
     * @param string $mode     - flag for datasource status.
     * @param string $contents - holding original contents.
     * @static
     * @return string - new contents
     */
    public function editNavDsTo($mode, $contents)
    {
        if (self::isDsTypeNavigation($contents)) {
            if (method_exists(get_class(), "setNavDsTo{$mode}")) {
                return call_user_func(array('self', "setNavDsTo{$mode}"), $contents);
            }
        }

        return $contents;
    }

    private static function isDsTypeNavigation($contents)
    {
        return (boolean) (preg_match("/return 'navigation';/", $contents) === 1);
    }

    /**
     * Replace standard navigation template with PLH navigation template
     *
     * @param string $contents- old datasource file contents.
     * @static
     * @return string - new datasource file contents.
     */
    public static function setNavDsToPLH($contents)
    {
        $contents = self::setNavDsToRequireParent($contents); // sym_template_class
        $contents = self::reg_replace(self::$sym_template_extends, self::$plh_template_extends, $contents);
        return $contents;
    }

    /**
     * Replace old grab() method with the new execute() one.
     *
     * @param string $contents- old datasource file contents.
     * @static
     * @return string - new datasource file contents.
     */
    public static function setNavDsToExecute($contents)
    {
        $contents = self::reg_replace(self::$grab_template_type, self::$execute_template_type, $contents);
        $contents = self::reg_replace(self::$grab_template_sign, self::$execute_template_sign, $contents);
        $contents = self::reg_replace(self::$grab_template_par, self::$execute_template_par, $contents);
        return $contents;
    }

    /**
     * Adds the sometimes needed require call to load the parent class.
     *
     * @param string $contents- old datasource file contents.
     * @static
     * @return string - new datasource file contents.
     */
    public static function setNavDsToRequireParent($contents)
    {
        if (strpos($contents, self::$plh_template_class) === false) {
            $contents = self::reg_replace(self::$sym_template_class, self::$plh_template_class, $contents);
        }
        return $contents;
    }

    /**
     * Replace PLH navigation template with standard navigation template.
     *
     * @param string $contents Old datasource file contents.
     * @static
     * @return string New datasource file contents.
     */
    public static function setNavDsToSYMPHONY($contents)
    {
        $contents = self::reg_replace(self::$plh_template_extends, self::$sym_template_extends, $contents);
        $contents = self::reg_replace(self::$plh_template_class, self::$sym_template_class, $contents);
        return $contents;
    }

}
