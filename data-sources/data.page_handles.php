<?php

require_once(TOOLKIT . '/class.datasource.php');
	
Class datasourcepage_lhandles extends Datasource{
		
	public function about(){
			return array(
				'name' => 'Page Handles',
				'author' => array(
					'name' => 'Vlad Ghita',
					'email' => 'vlad.ghita@xandergroup.ro'),
				'version' => '1.0',
				'release-date' => '2011-01-24',
				'description' => 	'<h2>Fetches Page Handles from the System for multilingual stuff.</h2>
Example:<br />

<p>Let\'s take four pages defined in Blueprints->Pages with Page Title matching the following pattern:</p>

<p><strong>{LANGUAGE_CODE}</strong> :<strong>{DEVELOPMENT_HANDLE}</strong>: <strong>{OTHER_CONTENT}</strong></p>

<p>where:</p>
<ul>
	<li><strong>{LANGUAGE_CODE}</strong> = country language. eg: en, ro, en-us, ro-md, fr etc.</li>
	<li><strong>{DEVELOPMENT_HANDLE}</strong> = handle used in Data Source to identify the Symphony Pages which refer to the same Frontend page.</li>
	<li><strong>{OTHER_CONTENT}</strong> = other content in title.</li>
</ul>

<p>and the page\'s handle (the one witch appears in frontend URL) is translated in the desired language.</p>

<table border="1" cellspacing="1" cellpadding="3">
	<tbody>
		<tr>
			<th>Title</th>
			<th>Handle</th>
			<th>Frontend URL example</th>
		<tr>
			<td>EN-US :certificari: Certificates for the company</td>
			<td>certificates</td>
			<td>www.mysite.com/certificates</td>
		<tr>
			<td>ES :certificari: Certificados para la empresa</td>
			<td>certificados</td>
			<td>www.mysite.com/certificados</td>
		<tr>
			<td>FR :certificari: Certificats pour la societe</td>
			<td>certificats</td>
			<td>www.mysite.com/certificats</td>
		<tr>
			<td>RO :certificari: Certificari pentru companie</td>
			<td>certificari</td>
			<td>www.mysite.com/certificari</td>
	</tbody>
</table>
<br />

<p>Every <strong>title</strong> contains the {LANGUAGE_CODE}. Next is the {DEVELOPMENT_HANDLE} surrounded with ":". In this case it is: ":certificari:", 
the romanian for "certificates" because my development language is romanian. And finally comes the rest of the title. <br />
The <strong>handle</strong> is translated in the corresponding language. It will appear in the frontend URL.</p>

<p>For the URL <code>www.mysite.com/certificados</code> the DataSource will return the following XML:</p>

<code>
&lt;page-handles&gt;<br />
&#160;&#160;&#160;&#160;&lt;page handle="certificados" handle-en-us="certificates" handle-es="certificados" handle-fr="certificats" handle-ro="certificari" /&gt;<br />
&lt;/page-handles&gt;
</code>');
	}
		
	public function allowEditorToParse(){
		return false;
	}
		
    public function grab(&$param_pool=NULL){
        $result = new XMLElement('page-handles');
		
        $db = Symphony::Database();
        
        die(print_r($db->fetch('SELECT * FROM `tbl_pages`')));
        
		include_once(TOOLKIT . '/class.entrymanager.php');
		$entryManager = new EntryManager($this->_Parent);
		
		//setez handle-ul sectiunii dorite, conform paginii curente
		switch ($this->_env['param']['current-page']) {
			case 'companie':
			case 'istoric':
			case 'sonepar':
			case 'cariere':
			case 'contact':
				$sectionHandle = 'p-'.$this->_env['param']['current-page'];
				break;
				
			//implicit Pagina Principala
			default :
				$sectionHandle .= 'p-companie';
		}
		
		$sectionId = $entryManager->sectionManager->fetchIDFromHandle($sectionHandle);
		
		//afisez si niste detalii despre sectiunea in cauza
		$section = $entryManager->sectionManager->fetch($sectionId);
		
    	if(!$section){
			$about = $this->about();
			trigger_error(__('The section associated with the data source <code>%s</code> could not be found.', array($about['name'])), E_USER_ERROR);
		}
		
		$sectioninfoXML = new XMLElement(
			'section', 
			$section->get('name'), 
			array(
				'id' => $section->get('id'), 
				'handle' => $section->get('handle')
			)
		);
		
		//extrag toate inregistrarile din sistem. Fiind sectiuni statice, doar 1 inregistrare va exista, deci 'entries per page' = 1
		$entries = $entryManager->fetchByPage(1, $sectionId, 1);
		
		if ($entries['total-entries'] == 0) {
			$about = $this->about();			
			$result->appendChild(new XMLElement('error', 'Sectiunea asociata cu sursa de date <code>'.$about['name'].'</code> nu contine inregistrari.'));
			return $result;
		}

		//$entry contine un array cu informatiile necesare. Key-urile sunt date de ID-urile campurilor dorite
		$entry = $entries['records'][0]->getData();
		
		//caut ID-ul campului 'meta-keywords' care ma intereseaza din sectiunea dorita
		$fieldKeywordsId 	= $entryManager->fieldManager->fetchFieldIDFromElementName('meta-keywords', $sectionId);
		
		//caut ID-ul campului 'meta-description' care ma intereseaza din sectiunea dorita
		$fieldDescriptionId = $entryManager->fieldManager->fetchFieldIDFromElementName('meta-description', $sectionId);
		
		//salvez continutul acum ca stiu ID-ul
		$metaKeywords = $entry[$fieldKeywordsId]['value'];
		$metaDescription = $entry[$fieldDescriptionId]['value'];

		//pun totul in XML si trimit mai departe
		$metaKeywordsXML = new XMLElement(
			'meta-description',
			$metaDescription,
			array(
				'handle' => 'meta-description'
			)
		);
			
		$metaDescriptionXML = new XMLElement(
			'meta-keywords',
			$metaKeywords,
			array(
				'handle' => 'meta-keywords'
			)
		);
		
		$entryXML = new XMLElement('entry');
		$entryXML->appendChild($metaKeywordsXML);
		$entryXML->appendChild($metaDescriptionXML);
		
		$result->appendChild($sectioninfoXML);
		$result->appendChild($entryXML);

        return $result;
    }
}
