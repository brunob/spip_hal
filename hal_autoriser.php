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
function autoriser_halspublication_creer_dist($faire, $type, $id, $qui, $opt){
	return false;
}
?>
