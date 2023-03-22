<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Modifier une publication HAL
 *
 * $c est un contenu (par defaut on prend le contenu via _request())
 *
 * @param int $id_hals_publication
 * @param array|bool $set
 * @return string
 */
function hals_publication_modifier($id_hals_publication, $set = false) {
	include_spip('inc/modifier');
	$c = collecter_requests(
		// white list
		objet_info('hals_publication', 'champs_editables'),
		// black list
		['statut', 'date'],
		// donnees eventuellement fournies
		$set
	);
	// Si le dépot est publie, invalider les caches et demander sa reindexation
	$t = sql_getfetsel('statut', 'spip_hals_publications', 'id_hals_publication=' . intval($id_hals_publication));
	if ($t == 'publie') {
		$invalideur = "id='hals_publication/$id_hals_publication'";
		$indexation = true;
	}

	if (
		$err = objet_modifier_champs(
			'hals_publication',
			$id_hals_publication,
			[
			'nonvide' => [],
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
	$err = objet_instituer('hals_publication', $id_hals_publication, $c);

	return $err;
}
