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
?>