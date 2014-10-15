<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Formulaire de création et d'édition d'un dépôt HAL
 */

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Chargement des valeurs par défaut du formulaire
 * 
 * @param int|string $id_hals_publication Identifiant numérique du point ou 'new' pour un nouveau
 */
function formulaires_editer_hals_publication_charger_dist($id_hals_publication, $retour='', $config_fonc='hals_publications_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('hals_publication', $id_hals_publication, null, null, $retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Vérification des valeurs du formulaire
 * 
 * 2 champs sont obligatoires :
 * -* Son titre
 * -* Son authid
 * 
 * @param int|string $id_hals_publication Identifiant numérique du point ou 'new' pour un nouveau
 * 
 */
function formulaires_editer_hals_publication_verifier_dist($id_hals_publication, $retour='', $config_fonc='hals_publications_edit_config', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('hals_publication', $id_hals_publication);
	return $erreurs;
}

// Choix par defaut des options de presentation
function hals_publications_edit_config($row){
	global $spip_lang;
	$config = $GLOBALS['meta'];
	$config['langue'] = $spip_lang;
	return $config;
}
/**
 * Traitement des valeurs du formulaire
 * 
 * @param int|string $id_hals_publication Identifiant numérique du point ou 'new' pour un nouveau
 * 
 */
function formulaires_editer_hals_publication_traiter_dist($id_hals_publication, $retour='', $config_fonc='hals_publications_edit_config', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('hals_publication', $id_hals_publication, '', '', $retour, '',$config_fonc,$row,$hidden);
	return $res;
}

?>
