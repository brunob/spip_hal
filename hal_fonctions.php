<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function typdoc_propre($texte){
	return _T('hals_publication:typdoc_'.strtolower($texte));
}

function hal_affdate($date,$date_format){
	if(in_array($date_format,array('annee','mois','jour','complet'))){
		switch ($date_format) {
			case 'annee':
				$date = affdate($date,'Y');
				break;
			default :
				$date = affdate_mois_annee($date);
				break;
		}
	}
	return $date;
}

function auteur_hal($id_hal,$qui=array()){
	if(isset($qui['statut'])){
		return sql_allfetsel("id_auteur", "spip_auteurs_liens", "objet='hal' AND id_objet=".intval($id_hal)." AND id_auteur=".intval($qui['id_auteur']));
	}
	return false;
}

function auteur_hals_publication($id_hals_publication,$qui=array()){
	if(isset($qui['statut'])){
		$id_hal = sql_getfetsel('id_hal','spip_hals_publications','id_hals_publication='.intval($id_hals_publication));
		return sql_allfetsel("id_auteur", "spip_auteurs_liens", "objet='hal' AND id_objet=".intval($id_hal)." AND id_auteur=".intval($qui['id_auteur']));
	}
	return false;
}
?>