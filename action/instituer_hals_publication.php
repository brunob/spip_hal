<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@action_instituer_syndic_article_dist
function action_instituer_hals_publication_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	list($id_hals_publication, $statut) = preg_split('/\W/', $arg);

	if ($id_hals_publication = intval($id_hals_publication)
	  AND $id_hal = sql_getfetsel('id_hal','spip_hals_publications',"id_hals_publication=".intval($id_hals_publication))
		AND autoriser('moderer','hal',$id_hal)) {
		sql_updateq("spip_hals_publications", array("statut" => $statut), "id_hals_publication=".intval($id_hals_publication));
	}

}

?>