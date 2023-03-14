<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

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
function formulaires_editer_hals_publication_charger_dist($id_hals_publication, $retour = '', $config_fonc = 'hals_publications_edit_config', $row = [], $hidden = '') {
	$valeurs = formulaires_editer_objet_charger('hals_publication', $id_hals_publication, null, null, $retour, $config_fonc, $row, $hidden);
	list($valeurs['date_production'],$valeurs['heure_production']) = explode(' ', date('d/m/Y H:i', strtotime($valeurs['date_production'])));
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
function formulaires_editer_hals_publication_verifier_dist($id_hals_publication, $retour = '', $config_fonc = 'hals_publications_edit_config', $row = [], $hidden = '') {
	$erreurs = formulaires_editer_objet_verifier('hals_publication', $id_hals_publication);
	if (_request('date_production')) {
		/**
		 * Ce fichier se trouve dans plugins-dist/organiseur/inc/date_gestion.php
		 * Mériterait une réincorporation dans SPIP?
		 */
		include_spip('inc/date_gestion');
		if (!$erreurs['date_production'] and ($date_production = _request('date_production'))) {
			$date_production = verifier_corriger_date_saisie('production', 'non', $erreurs);
		}
	}
	return $erreurs;
}

// Choix par defaut des options de presentation
function hals_publications_edit_config($row) {
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
function formulaires_editer_hals_publication_traiter_dist($id_hals_publication, $retour = '', $config_fonc = 'hals_publications_edit_config', $row = [], $hidden = '') {
	if (_request('date_production')) {
		include_spip('inc/date_gestion');
		$date_production = date('Y-m-d H:i:s', verifier_corriger_date_saisie('production', 'non', $erreurs));
		set_request('date_production', $date_production);
	}
	$res = formulaires_editer_objet_traiter('hals_publication', $id_hals_publication, '', '', $retour, '', $config_fonc, $row, $hidden);
	return $res;
}
