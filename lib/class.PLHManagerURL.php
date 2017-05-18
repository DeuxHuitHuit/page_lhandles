<?php

if (!defined('__IN_SYMPHONY__')) die('<h2>Error</h2><p>You cannot directly access this file</p>');

require_once(EXTENSIONS.'/frontend_localisation/lib/class.FLang.php');

final class PLHManagerURL
{
    /**
     * Converts given URL from Symphony Page handles to $lang_code Page handles.
     *
     * @param string $url       - URL to convert
     * @param string $lang_code - language code. If empty, defaults to main language
     *
     * @static
     *
     * @return string - localised URL if $lang_code was found else original URL
     */
    public static function sym2lang($url, $lang_code = null)
    {
        self::processStrict($lang_code);

        // if no language is set, return current URL
        if (empty($lang_code)) {
            return $url;
        }

        $ref_handle = 'handle';
        $target_handle = 'plh_h-'.$lang_code;

        return self::processURL($url, $ref_handle, $target_handle);
    }

    /**
     * Converts given URL from $lang_code Page handles to Symphony Page handles.
     *
     * @param string $url       - URL to convert
     * @param string $lang_code - language code. If empty, defaults to main FLang
     *
     * @static
     *
     * @return string - symphony URL if $lang_code was found else original URL
     */
    public static function lang2sym($url, $lang_code = null)
    {
        self::processStrict($lang_code);

        // if no language is set, return current URL
        if (empty($lang_code)) {
            return $url;
        }

        $ref_handle = 'plh_h-'.$lang_code;
        $target_handle = 'handle';

        return self::processURL($url, $ref_handle, $target_handle);
    }


    /*------------------------------------------------------------------------------------------------*/
    /*  In-house  */
    /*------------------------------------------------------------------------------------------------*/

    /**
     * Process given URL. Finds target_handles from reference_handles.
     *
     * @param string $url
     * @param string $ref_handle
     * @param string $target_handle
     *
     * @static
     *
     * @return string - processed URL
     */
    private static function processURL($url, $ref_handle, $target_handle)
    {
        /*
         * This is here in case raw URLs are processed. (eg: URLs comming from Multilingual Entry URL).
         * Normally, a sanitized $url will come here (from Symphony)
         */
        $url_query = '';
        $url_hash = '';

        // find the Query
        $url_query_pos = strpos($url, '?');

        if ($url_query_pos !== false) {
            $url_query = substr($url, $url_query_pos);
            $url = substr($url, 0, $url_query_pos);
        }

        // else find the Hash
        else {
            $url_hash_pos = strpos($url, '#');

            if ($url_hash_pos !== false) {
                $url_hash = substr($url, $url_hash_pos);
                $url = substr($url, 0, $url_hash_pos);
            }
        }


        $old_url = preg_split('/\//', trim($url, '/'), -1, PREG_SPLIT_NO_EMPTY);
        $last_parent = null;


        // resolve index
        if ($old_url == null || empty($old_url) || !is_array($old_url)) {

            $query = "
                SELECT p.`id`, p.`{$target_handle}`, p.`parent`
                FROM `tbl_pages` as p
                INNER JOIN `tbl_pages_types` as pt ON pt.`page_id` = p.`id`
                WHERE pt.`type` = 'index'
                LIMIT 1";

            $bit = self::getPageHandle($query, $last_parent, $target_handle);

            $path = ($bit === false) ? $url : '/'.$bit;
        }

        // resolve other pages
        else {
            $op_mode = Symphony::Configuration()->get('op_mode', PLH_GROUP);
            // Assure we have a op_mode set
            $op_mode = empty($op_mode) ? 'strict' : $op_mode;
            $method = '_process'.ucfirst(strtolower($op_mode));

            if (method_exists(get_class(), $method)) {
                $path = call_user_func(array(self, $method), $old_url, $ref_handle, $target_handle);
            }
            else {
                $path = trim($url, '/');
            }
        }

        return (string) trim($path.'/'.$url_query.$url_hash, '/');
    }

    /**
     * Processes the URL with strict settings.
     * Respects Symphony Page parents structure.
     *
     * @param array  $old_url
     * @param string $ref_handle
     * @param string $target_handle
     *
     * @static
     *
     * @return string - the new path
     */
    private static function processStrict($old_url, $ref_handle, $target_handle)
    {
        $path = '';
        $page_mode = true;
        $last_parent = null;

        foreach( $old_url as $value) {
            if (!empty($value)) {

                if ($page_mode) {
                    $query = sprintf("
                        SELECT `id`, `%s`, `parent` FROM `tbl_pages` WHERE `%s` = '%s' AND `parent` %s LIMIT 1",
                        $target_handle,
                        $ref_handle,
                        $value,
                        $last_parent != null ? sprintf("= %s", $last_parent) : "IS NULL"
                    );

                    $bit = self::getPageHandle($query, $last_parent, $target_handle);

                    if ($bit === false) {
                        $path .= '/'.$value;
                        $page_mode = false;
                    }
                    else {
                        $path .= '/'.$bit;
                    }
                }
                else {
                    $path .= '/'.$value;
                }
            }
        }

        return $path;
    }

    /**
     * Executes the given query and returns target_handle or false if no match
     *
     * @param string $query
     * @param int    &$last_parent
     * @param string $target_handle - $target_handle desired
     *
     * @static
     *
     * @return mixed - Translated handle or false if no handle found.
     */
    private static function getPageHandle($query, &$last_parent, $target_handle)
    {
        try {
            $page = Symphony::Database()->fetch($query);
        }
        catch (DatabaseException $e) {
            //table column "$lhandle" doesn't exist. redirect to 404.
            if ($e->getDatabaseErrorCode() == 1054) {
                FrontendPageNotFoundExceptionHandler::render($e);
            }
            // re-trow non-handled exception
            else {
                throw $e;
            }
        }

        // page handle exists, store it
        if (!empty($page) && ($last_parent == $page[0]['parent'])) {
            $last_parent = $page[0]['id'];

            return $page[0][$target_handle];
        }

        return false;
    }

}
