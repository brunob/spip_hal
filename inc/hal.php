<?php

// prend un fichier backend et retourne un tableau des items lus,
// et une chaine en cas d'erreur
// http://doc.spip.org/@analyser_backend
function analyser_publications($json, $url_syndic='') {
	$json = json_decode($json,true);
	$json = pipeline('pre_syndication_publications', $json);
	$publications  = false;
	if(isset($json['response']) && isset($json['response']['docs']) && is_array($json['response']['docs'])){
		include_spip('inc/distant');
		$publications  = array();
		foreach($json['response']['docs'] as $id => $doc){
			preg_match('/&lt;(hal-[0-9]*)&gt;/Uims',$doc['label_s'],$ref);
			$contenu_publication = recuperer_page("https://halv3-preprod.archives-ouvertes.fr/".$ref[1]."/json");
			$contenu_publication = json_decode($contenu_publication,true);
			$infos_publication = array();
			foreach(array('docid','identifiant','uri','format','typdoc','submittedDate','producedDate','modifiedDate','citationRef','citationFull','metas') as $info){
				if(isset($contenu_publication[$info]) && (is_array($contenu_publication[$info]) OR (strlen($contenu_publication[$info]) > 0))){
					switch ($info) {
						case 'uri':
							$infos_publication['url'] = $contenu_publication[$info];
							break;
						case 'submittedDate':
							$infos_publication['date_soumission'] = $contenu_publication[$info];
							break;
						case 'producedDate':
							$infos_publication['date_production'] = $contenu_publication[$info];
							break;
						case 'modifiedDate':
							$infos_publication['date_modif'] = $contenu_publication[$info];
							break;
						case 'citationRef':
							$infos_publication['citation_reference'] = $contenu_publication[$info];
							break;
						case 'citationFull':
							$infos_publication['citation_complete'] = $contenu_publication[$info];
							break;
						case 'metas':
							if(isset($contenu_publication[$info]['language']))
								$infos_publication['lang'] = $contenu_publication[$info]['language'];
							if(isset($contenu_publication[$info]['title']))
								$infos_publication['titre'] = $contenu_publication[$info]['title'][$infos_publication['lang']];
							if(isset($contenu_publication[$info]['page']))
								$infos_publication['page'] = $contenu_publication[$info]['page'];
							break;
						default: // docid, identifiant, typdoc
							$infos_publication[$info] = $contenu_publication[$info];
							break;
					}
				}
			}
			$infos_publication['hal_complet'] = serialize($contenu_publication);
			$publications[] = $infos_publication;
		}
	}
	return $publications;
}

?>