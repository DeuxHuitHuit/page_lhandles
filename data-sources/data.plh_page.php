<?php

require_once(TOOLKIT . '/class.datasource.php');

class datasourceplh_page extends Datasource
{
    public function about() {
        return array(
            'name'         => 'PLH: Page',
            'author'       => array(
                'name'  => 'Vlad Ghita',
                'email' => 'vlad_micutul@yahoo.com'
            ),
            'version'      => '1.1',
            'release-date' => '2011-11-15',
            'description'  => 'From Page LHandles extension. Retrieves the current pages\' and it\'s parents localised titles and handles in all supported languages.'
        );
    }

    public function allowEditorToParse()
    {
        return false;
    }

    public function execute(array &$param_pool = null)
    {
        $result = new XMLElement('plh-page');

        $langs  = FLang::getLangs();
        $fields = array(
            'id',
            'handle',
            'parent'
        );

        foreach ($langs as $lc) {
            $fields[] = "`plh_t-{$lc}`";
            $fields[] = "`plh_h-{$lc}`";
        }

        $pages = array();
        foreach (PageManager::fetch(null, $fields) as $page) {
            $pages[$page['id']] = $page;
        }

        $this->appendPage($pages, $this->_env['param']['current-page-id'], $langs, $result);

        return $result;
    }



    /**
     * Add parent pages including current to XML output.
     *
     * @param array      $pages   - contains all pages data
     * @param int        $page_id - current page id
     * @param array      $langs   - all supported language codes
     * @param XMLElement $result  - resulting XML
     *
     * @return XMLElement - a pages XML ouput
     */
    private function appendPage(array $pages, $page_id, array $langs, XMLElement $result)
    {
        $page = $pages[$page_id];

        if ($page['parent'] !== null) {
            $result = $this->appendPage($pages, $page['parent'], $langs, $result);
        }

        $page_xml = new XMLElement('page');
        $page_xml->setAttribute('handle', $page['handle']);
        $page_xml->setAttribute('id', $page_id);

        foreach ($langs as $lc) {
            $item_xml = new XMLElement('item');
            $item_xml->setValue(General::sanitize($page["plh_t-$lc"]));
            $item_xml->setAttribute('lang', $lc);
            $item_xml->setAttribute('handle', $page["plh_h-$lc"]);

            $page_xml->prependChild($item_xml);
        }

        $result->appendChild($page_xml);

        return $page_xml;
    }
}
