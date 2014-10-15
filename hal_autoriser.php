<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// fonction pour le pipeline
function hal_autoriser() {}

/**
 * Creer une nouvelle publication :
 * 
 * Interdit, seul le CRON peut créer une publication
 * 
 * @return bool false
 */ 
 
function autoriser_hal_creer_dist($faire, $type, $id, $qui, $opt){
	return in_array($qui['statut'],array('0minirezo','1comite'));
}

function autoriser_halspublication_creer_dist($faire, $type, $id, $qui, $opt){
	return false;
}

function autoriser_halspublication_modifier_dist($faire, $type, $id, $qui, $opt){
	if($qui['statut'] == '0minirezo' || auteur_hals_publication($id,$qui) && in_array($qui['statut'],array('1comite')))
		return true;
	
	return false;
}

function autoriser_hal_modifier_dist($faire, $type, $id, $qui, $opt){
	if($qui['statut'] == '0minirezo' || auteur_hal($id,$qui) && in_array($qui['statut'],array('1comite')))
		return true;
	
	return false;
}

function autoriser_hal_moderer_dist($faire, $type, $id, $qui, $opt){
	return autoriser('modifier','hal',$id,$qui,$opt);
}
?>
