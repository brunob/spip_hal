<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@action_editer_site_dist
function action_update_publications_dist($id_hal=null) {

	if (is_null($id_hal)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_hal = $securiser_action();
	}


	$id_job = job_queue_add('hal_a_jour','hal_a_jour',array($id_hal),'genie/hal',true);
	// l'executer immediatement si possible
	if ($id_job) {
		spip_log($id_job,'test.'._LOG_ERREUR);
		include_spip('inc/queue');
		queue_schedule(array($id_job));
	}
	else {
		spip_log("Erreur insertion hal_a_jour($id_hal) dans la file des travaux",_LOG_ERREUR);
	}

}

?>