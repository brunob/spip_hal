<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline declarer_tables_interfaces (SPIP)
 * Interfaces des tables hals et hals_publications
 *
 * @param array $interfaces
 * @return array
 */
function hal_declarer_tables_interfaces($interfaces){

	$interfaces['table_des_tables']['hals'] = 'hals';
	$interfaces['table_des_tables']['hals_publications'] = 'hals_publications';
	
	$interfaces['table_des_traitements']['TYPDOC'][] = 'typo(typdoc_propre(%s))';

	return $interfaces;
}

/**
 * Insertion dans le pipeline declarer_tables_objets_sql (SPIP)
 * Définition des objets hals et hals_publications
 *
 * @param array $tables
 * 	La description des objets SPIP
 * @return array
 * 	La description des objets SPIP complétée
 */
function hal_declarer_tables_objets_sql($tables){
	$tables['spip_hals'] = array(
		'type'=>'hal',
		'texte_retour' => 'icone_retour',
		'texte_objets' => 'hal:icone_hals_references',
		'texte_objet' => 'hal:icone_hal_reference',
		'texte_modifier' => 'hal:icone_modifier_hal',
		'texte_creer' => 'hal:icone_referencer_nouveau_hal',
		'info_aucun_objet'=> 'hal:info_aucun_hal',
		'info_1_objet' => 'hal:info_1_hal',
		'info_nb_objets' => 'hal:info_nb_hals',
		'date' => 'date',
		'principale' => 'oui',
		'icone_objet' => 'hal',
		'field'=> array(
			"id_hal"	=> "bigint(21) NOT NULL",
			"authid" => "bigint(21) NOT NULL",
			"structid" => "bigint(21) NOT NULL",
			"titre"	=> "text DEFAULT '' NOT NULL",
			"url_syndic"	=> "text DEFAULT '' NOT NULL",
			"descriptif"	=> "text DEFAULT '' NOT NULL",
			"maj"	=> "TIMESTAMP",
			"statut"	=> "varchar(10) DEFAULT '0' NOT NULL",
			"date"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_syndic"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_index"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"moderation"	=> "VARCHAR(3) DEFAULT 'non'",
			"miroir"	=> "VARCHAR(3) DEFAULT 'non'",
			"oubli"	=> "VARCHAR(3) DEFAULT 'non'"
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_hal",
			"KEY statut"	=> "statut, date_syndic",
		),
		'join' => array(
			"id_hal"=>"id_hal",
		),
		'statut' => array(
			 array('champ'=>'statut','publie'=>'publie','previsu'=>'publie,prop','exception'=>'statut')
		),
		'texte_changer_statut' => 'hal:info_statut_hal',
		'statut_textes_instituer' => array(
			'prop' => 'texte_statut_propose_evaluation',
			'publie' => 'texte_statut_publie',
			'refuse' => 'texte_statut_poubelle',
		),

		'rechercher_champs' => array(
			'titre' => 5, 'descriptif' => 1
		),
		'champs_versionnes' => array('titre', 'descriptif','authid','structid'),
	);

	$tables['spip_hals_publications'] = array(
		'type'=>'hals_publication',
		'texte_retour' => 'icone_retour',
		'texte_objets' => 'hals_publication:icone_hals_publications',
		'texte_objet' => 'hals_publication:icone_hals_publication',
		'texte_modifier' => 'hals_publication:icone_modifier_publication',
		'info_aucun_objet'=> 'hals_publication:info_aucun_hals_publication',
		'info_1_objet' => 'hals_publication:info_1_hals_publication',
		'info_nb_objets' => 'hals_publication:info_nb_hals_publications',
		'date' => 'date_production',
		'editable' => 'oui',
		'principale' => 'oui',
		'field'=> array(
			"id_hals_publication"	=> "bigint(21) NOT NULL",
			"id_hal"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"docid" => "bigint(21) DEFAULT '0' NOT NULL",
			"identifiant" => "text DEFAULT '' NOT NULL",
			"typdoc" => "text DEFAULT '' NOT NULL",
			"date_soumission" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_modif" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_production" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"editeur" => "text DEFAULT '' NOT NULL",
			"titre"	=> "text DEFAULT '' NOT NULL",
			"citation_reference"	=> "text DEFAULT '' NOT NULL",
			"citation_complete"	=> "text DEFAULT '' NOT NULL",
			"page"	=> "VARCHAR(255) DEFAULT '' NOT NULL",
			"url"	=> "VARCHAR(255) DEFAULT '' NOT NULL",
			"format" => "VARCHAR(255) DEFAULT '' NOT NULL",
			"date"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"lesauteurs"	=> "text DEFAULT '' NOT NULL",
			"maj"	=> "TIMESTAMP",
			"statut"	=> "varchar(10) DEFAULT '0' NOT NULL",
			"descriptif"	=> "text DEFAULT '' NOT NULL",
			"lang"	=> "VARCHAR(10) DEFAULT '' NOT NULL",
			"url_source" => "TINYTEXT DEFAULT '' NOT NULL",
			"tags" => "TEXT DEFAULT '' NOT NULL",
			"hal_complet"	=> "longtext DEFAULT '' NOT NULL",
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_hals_publication",
			"KEY id_hal"	=> "id_hal",
			"KEY statut"	=> "statut",
			"KEY url"	=> "url"
		),
		'join' => array(
			"id_hals_publication"=>"id_hals_publication",
			"id_hal"=>"id_hal"
		),
		'statut' => array(
			array('champ'=>'statut','publie'=>'publie','previsu'=>'publie,prop','exception'=>'statut'),
			array('champ'=>array(array('spip_hals','id_hal'),'statut'),'publie'=>'publie','previsu'=>'publie,prop','exception'=>'statut'),
		),
		'statut_images' => array(
			'puce-rouge-anim.gif','publie'=>'puce-publier-8.png','refuse'=>'puce-supprimer-8.png','dispo'=>'puce-proposer-8.png','off'=>'puce-refuser-8.png',
		),
		'rechercher_champs' => array(
				'titre' => 5, 'descriptif' => 1
		)
	);

	return $tables;
}

?>
