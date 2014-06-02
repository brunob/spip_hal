<?php

// prend un json issu de la recherche de HAL et retourne un tableau des documents lus,
// et false en cas d'erreur
function analyser_publications($json, $url_syndic='') {
	$json = json_decode($json,true);
	$json = pipeline('pre_syndication_publications', $json);
	$publications  = false;
	if(isset($json['response']) && isset($json['response']['docs']) && is_array($json['response']['docs'])){
		include_spip('inc/distant');
		$publications = array();
		$count = 0;
		foreach($json['response']['docs'] as $id => $contenu_publication){
			$count++;
			$infos_publication = array();
			$affiche = false;
			foreach(array('docid','language_s','title_s','subTitle_s','hal_id','uri_s','page_s','docType_s','publisher_s','submittedDate_s','producedDate_s','modifiedDate_s','citationRef_s','citationFull_s','authFullName_s') as $info){
				if(isset($contenu_publication[$info]) && (is_array($contenu_publication[$info]) OR (strlen($contenu_publication[$info]) > 0))){
					switch ($info) {
						case 'docid':
							$infos_publication['docid'] = $contenu_publication[$info];
							break;
						case 'title_s':
							$infos_publication['titre'] = $contenu_publication[$info][0];
							break;
						case 'subTitle_s':
							$infos_publication['soustitre'] = $contenu_publication[$info][0];
							break;
						case 'hal_id':
							$infos_publication['identifiant'] = $contenu_publication[$info][0];
							break;
						case 'uri_s':
							$infos_publication['url'] = $contenu_publication[$info];
							break;
						case 'page_s':
							$infos_publication['page'] = $contenu_publication[$info];
							break;
						case 'docType_s':
							$infos_publication['typdoc'] = $contenu_publication[$info];
							break;
						case 'publisher_s':
							$infos_publication['editeur'] = implode(',',$contenu_publication[$info]);
							break;
						case 'submittedDate_s':
							$infos_publication['date_soumission'] = $contenu_publication[$info];
							break;
						case 'producedDate_s':
							if(strlen($contenu_publication[$info]) == 4 && preg_match('/\d{4}/',$contenu_publication[$info])){
								$infos_publication['date_production'] = $contenu_publication[$info].'-01-01 00:00:00';
								$infos_publication['date_production_format'] = 'annee';
							}else if(strlen($contenu_publication[$info]) == 7 && preg_match('/\d{4}-\d{2}/',$contenu_publication[$info])){
								$infos_publication['date_production'] = $contenu_publication[$info].'-01 00:00:00';
								$infos_publication['date_production_format'] = 'mois';
							}else if(strlen($contenu_publication[$info]) == 10 && preg_match('/\d{4}-\d{2}-\d{2}/',$contenu_publication[$info])){
								$infos_publication['date_production'] = $contenu_publication[$info].' 00:00:00';
								$infos_publication['date_production_format'] = 'jour';
							}else if(strlen($contenu_publication[$info]) == 19){
								$infos_publication['date_production'] = $contenu_publication[$info];
								$infos_publication['date_production_format'] = 'complet';
							}
							break;
						case 'modifiedDate_s':
							$infos_publication['date_modif'] = $contenu_publication[$info];
							break;
						case 'citationRef_s':
							$infos_publication['citation_reference'] = $contenu_publication[$info];
							break;
						case 'citationFull_s':
							$infos_publication['citation_complete'] = $contenu_publication[$info];
							break;
						case 'language_s':
							$infos_publication['lang'] = $contenu_publication[$info][0];
							break;
						case 'authFullName_s':
							$infos_publication['lesauteurs'] = implode(',',$contenu_publication[$info]);
							break;
						default: 
							$infos_publication[$info] = $contenu_publication[$info];
							break;
					}
				}
			}
			$infos_publication['hal_complet'] = serialize($contenu_publication);
			$publications[] = $infos_publication;
			if(!isset($infos_publication['titre']) OR $infos_publication['titre'] == null)
				spip_log($contenu_publication,'test.'._LOG_ERREUR);
		}
	}
	spip_log($count.'/'.$json['response']['numFound'],'test.'._LOG_ERREUR);
	return $publications;
}

?>