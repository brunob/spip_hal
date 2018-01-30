<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// fonction pour le pipeline
function hal_autoriser() {} 

function autoriser_hal_creer_dist($faire, $type, $id, $qui, $opt){
	return in_array($qui['statut'],array('0minirezo','1comite'));
}

function autoriser_hal_modifier_dist($faire, $type, $id, $qui, $opt){
	if($qui['statut'] == '0minirezo' || auteur_hal($id,$qui) && in_array($qui['statut'],array('1comite')))
		return true;
	
	return false;
}

function autoriser_hal_moderer_dist($faire, $type, $id, $qui, $opt){
	return autoriser('modifier','hal',$id,$qui,$opt);
}

/**
 * Creer une nouvelle publication :
 * 
 * Interdit, seul le CRON peut créer une publication
 * 
 * @return bool false
 */
function autoriser_halspublication_creer_dist($faire, $type, $id, $qui, $opt){
	return false;
}

function autoriser_halspublication_modifier_dist($faire, $type, $id, $qui, $opt){
	if($qui['statut'] == '0minirezo' || auteur_hals_publication($id,$qui) && in_array($qui['statut'],array('1comite')))
		return true;
	
	return false;
}

function auteur_hal($id_hal,$qui=array()){
	if(isset($qui['statut'])){
		return sql_allfetsel("id_auteur", "spip_auteurs_liens", "objet='hal' AND id_objet=".intval($id_hal)." AND id_auteur=".intval($qui['id_auteur']));
	}
	return false;
}

function auteur_hals_publication($id_hals_publication,$qui=array()){
	if(isset($qui['statut'])){
		$id_hal = sql_getfetsel('id_hal','spip_hals_publications','id_hals_publication='.intval($id_hals_publication));
		return sql_allfetsel("id_auteur", "spip_auteurs_liens", "objet='hal' AND id_objet=".intval($id_hal)." AND id_auteur=".intval($qui['id_auteur']));
	}
	return false;
}

