<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_editer_hal_dist($arg = null) {

	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	if (!$id_hal = intval($arg)) {
		$id_hal = hal_inserer();
	}

	if (!$id_hal) {
		return [0,''];
	}

	$err = hal_modifier($id_hal);

	return [$id_hal,$err];
}


/**
 * Inserer un nouveau dépot HAL en base
 *
 * @return bool
 */
function hal_inserer() {

	$champs = [
		'statut' => 'prop',
		'date' => date('Y-m-d H:i:s')];

	// Envoyer aux plugins
	$champs = pipeline(
		'pre_insertion',
		[
			'args' => [
				'table' => 'spip_hals',
			],
			'data' => $champs
		]
	);

	$id_hal = sql_insertq('spip_hals', $champs);
	pipeline(
		'post_insertion',
		[
			'args' => [
				'table' => 'spip_hals',
				'id_objet' => $id_hal
			],
			'data' => $champs
		]
	);

	return $id_hal;
}

/**
 * Modifier un dépot HAL
 *
 * $c est un contenu (par defaut on prend le contenu via _request())
 *
 * @param int $id_hal
 * @param array|bool $set
 * @return string
 */
function hal_modifier($id_hal, $set = false) {
	include_spip('inc/modifier');
	include_spip('inc/filtres');
	$c = collecter_requests(
		// white list
		objet_info('hal', 'champs_editables'),
		// black list
		['statut', 'date'],
		// donnees eventuellement fournies
		$set
	);

	$q = '';
	$hal_api = 'http://api.archives-ouvertes.fr/search/';
	if (isset($c['authid']) and $c['authid'] > 0) {
		$q .= 'authId_i:' . $c['authid'];
	}
	if (isset($c['idhal']) and $c['idhal'] != '') {
		$q .= 'authIdHal_s:' . $c['idhal'];
	}
	$hal_api = parametre_url($hal_api, 'q', $q);
	if (!function_exists('lire_config')) {
		include_spip('inc/config');
	}
	$hal_api = parametre_url($hal_api, 'sort', 'modifiedDate_s desc');

	$c['url_syndic'] = $hal_api;

	// Si le dépot est publie, invalider les caches et demander sa reindexation
	$t = sql_getfetsel('statut', 'spip_hals', 'id_hal=' . intval($id_hal));
	if ($t == 'publie') {
		$invalideur = "id='hal/$id_hal'";
		$indexation = true;
	}

	if (
		$err = objet_modifier_champs(
			'hal',
			$id_hal,
			[
			'nonvide' => ['titre' => _T('info_sans_titre')],
			'invalideur' => $invalideur ?? false,
			'indexation' => $indexation ?? false
			],
			$c
		)
	) {
		return $err;
	}

	// Modification de statut, changement de rubrique ?
	$c = collecter_requests(['date', 'statut'], [], $set);
	include_spip('action/editer_objet');
	$err = objet_instituer('hal', $id_hal, $c);

	return $err;
}

function instituer_hal($id_hal, $c) {
	include_spip('action/editer_objet');
	return objet_instituer('hal', $id_hal, $c);
}
