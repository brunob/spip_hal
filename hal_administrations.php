<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Installation/maj des tables hals et hals_publications
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function hal_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_hals','spip_hals_publications')),
	);
	$maj['0.1.1'] = array(
		array('maj_tables',array('spip_hals_publications')),
	);
	$maj['0.1.2'] = array(
		array('maj_tables',array('spip_hals_publications')),
	);
	$maj['0.1.3'] = array(
		array('maj_tables',array('spip_hals_publications')),
	);
	$maj['0.1.4'] = array(
		array('maj_tables',array('spip_hals_publications')),
	);
	$maj['0.1.5'] = array(
		array('maj_tables',array('spip_hals_publications')),
	);
	$maj['0.1.6'] = array(
		array('maj_tables',array('spip_hals')),
		array('upgrade_hal_limites')
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Desinstallation/suppression des tables hals et hals_publications
 *
 * @param string $nom_meta_base_version
 */
function hal_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_hals");
	sql_drop_table("spip_hals_publications");

	effacer_meta($nom_meta_base_version);
}


function upgrade_hal_limites(){
	$hals = sql_allfetsel('*','spip_hals');
	foreach ($hals as $hal) {
		$url_syndic = parametre_url($hal['url_syndic'],'rows','');
		sql_updateq('spip_hals',array('url_syndic' => $url_syndic),'id_hal='.intval($hal['id_hal']));
	}
}
?>
