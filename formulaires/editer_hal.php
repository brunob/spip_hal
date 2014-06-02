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
 * @param int|string $id_hal Identifiant numérique du point ou 'new' pour un nouveau
 */
function formulaires_editer_hal_charger_dist($id_hal='new', $retour='', $config_fonc='hals_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('hal', $id_hal, null, null, $retour, '',$config_fonc);
	spip_log($valeurs,'test.'._LOG_ERREUR);
	return $valeurs;
}

/**
 * Vérification des valeurs du formulaire
 * 
 * 2 champs sont obligatoires :
 * -* Son titre
 * -* Son authid
 * 
 * @param int|string $id_hal Identifiant numérique du point ou 'new' pour un nouveau
 * 
 */
function formulaires_editer_hal_verifier_dist($id_hal='new', $retour='', $config_fonc='hals_edit_config', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('hal', $id_hal,array('titre','authid'));
	return $erreurs;
}

// Choix par defaut des options de presentation
function hals_edit_config($row){
	global $spip_lang;
	return array();
}
/**
 * Traitement des valeurs du formulaire
 * 
 * @param int|string $id_hal Identifiant numérique du point ou 'new' pour un nouveau
 * 
 */
function formulaires_editer_hal_traiter_dist($id_hal='new', $retour='', $config_fonc='hals_edit_config', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('hal', $id_hal, '', '', $retour, '',$config_fonc,$row,$hidden);
}

?>
