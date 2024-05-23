<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation/maj des tables hals et hals_publications
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function hal_upgrade($nom_meta_base_version, $version_cible) {

	$maj = [];
	$maj['create'] = [
		['maj_tables',['spip_hals','spip_hals_publications']],
	];
	$maj['0.1.1'] = [
		['maj_tables',['spip_hals_publications']],
	];
	$maj['0.1.2'] = [
		['maj_tables',['spip_hals_publications']],
	];
	$maj['0.1.3'] = [
		['maj_tables',['spip_hals_publications']],
	];
	$maj['0.1.4'] = [
		['maj_tables',['spip_hals_publications']],
	];
	$maj['0.1.5'] = [
		['maj_tables',['spip_hals_publications']],
	];
	$maj['0.1.6'] = [
		['maj_tables',['spip_hals']],
		['upgrade_hal_limites']
	];
	$maj['0.1.7'] = [
		['maj_tables',['spip_hals_publications']],
		['upgrade_hal_resume']
	];
	$maj['0.1.8'] = [
		['upgrade_hal_api']
	];
	$maj['0.1.9'] = [
		['maj_tables', ['spip_hals']]
	];
	$maj['0.2.0'] = [
		['maj_tables', ['spip_hals']],
		['sql_alter', 'TABLE spip_hals DROP authid'],
	];

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Desinstallation/suppression des tables hals et hals_publications
 *
 * @param string $nom_meta_base_version
 */
function hal_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_hals');
	sql_drop_table('spip_hals_publications');

	effacer_meta($nom_meta_base_version);
}


function upgrade_hal_limites() {
	$hals = sql_allfetsel('*', 'spip_hals');
	foreach ($hals as $hal) {
		$url_syndic = parametre_url($hal['url_syndic'], 'rows', '');
		sql_updateq('spip_hals', ['url_syndic' => $url_syndic], 'id_hal=' . intval($hal['id_hal']));
	}
}

function upgrade_hal_resume() {
	$hals = sql_allfetsel('id_hals_publication,hal_complet', 'spip_hals_publications');
	foreach ($hals as $hal) {
		$hals_content = unserialize($hal['hal_complet']);
		if (isset($hals_content['abstract_s']) and is_array($hals_content['abstract_s'])) {
			sql_updateq('spip_hals_publications', ['resume' => $hals_content['abstract_s'][0]], 'id_hals_publication=' . intval($hal['id_hals_publication']));
		}
	}
}

function upgrade_hal_api() {
	$hals = sql_allfetsel('id_hal,url_syndic', 'spip_hals');
	$ajout_syndic = charger_fonction('update_publications', 'action');
	foreach ($hals as $hal) {
		$new_api = str_replace('api-preprod.archives-ouvertes.fr', 'api.archives-ouvertes.fr', $hal['url_syndic']);
		if ($new_api != $hal['url_syndic']) {
			sql_updateq('spip_hals', ['url_syndic' => $new_api], 'id_hal=' . intval($hal['id_hal']));
			$ajout_syndic($hal['id_hal']);
		}
	}
}
