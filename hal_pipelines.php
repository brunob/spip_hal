<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Taches periodiques de récupération des publications 
 *
 * @param array $taches_generales
 * @return array
 */
function hal_taches_generales_cron($taches_generales){
	$taches_generales['hal'] = 90; 
	return $taches_generales;
}


/**
 * Optimiser la base de donnee en supprimant les liens orphelins
 *
 * @param array $flux
 * @return array
 */
function hal_optimiser_base_disparus($flux){
	$n = &$flux['data'];
	$mydate = $flux['args']['date'];


	sql_delete("spip_hals", "maj < $mydate AND statut = 'refuse'");


	# les articles syndiques appartenant a des sites effaces
	$res = sql_select("S.id_hal AS id",
		      "spip_hals_publications AS S
		        LEFT JOIN spip_hals AS hal
		          ON S.id_hal=hal.id_hal",
			"hal.id_hal IS NULL");

	$n+= optimiser_sansref('spip_hals_publications', 'id_hal', $res);
	

	return $flux;

}
?>
