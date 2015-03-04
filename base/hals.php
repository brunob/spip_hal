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
	$interfaces['table_des_traitements']['CITATION_REFERENCE'][] = 'propre(%s)';
	$interfaces['table_des_traitements']['CITATION_COMPLETE'][] = 'propre(%s)';
	$interfaces['table_des_traitements']['RESUME'][] = 'propre(%s)';
	$interfaces['table_des_traitements']['COMMENTAIRE'][] = 'propre(%s)';
	$interfaces['table_des_traitements']['DESCRIPTIF'][] = 'propre(%s)';
	$interfaces['table_des_traitements']['EDITEUR'][] = 'typo(%s)';

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
 */
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
			"id_hal" => "bigint(21) NOT NULL",
			"authid" => "bigint(21) NOT NULL",
			"idhal" => "varchar(200) NOT NULL",
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
			"limite"	=> "bigint(21) NOT NULL",
			"oubli"	=> "VARCHAR(3) DEFAULT 'non'"
		),
		'champs_editables' => array('titre', 'authid', 'idhal', 'descriptif','limite'),
		'key' => array(
			"PRIMARY KEY"	=> "id_hal",
			"KEY statut"	=> "statut, date_syndic",
		),
		'join' => array(
			"id_hal"=>"id_hal",
		),
		
		'statut' => array(
			 array('champ'=>'statut','publie'=>'publie','previsu'=>'publie,prop','exception'=>array('statut','tout'))
		),
		'texte_changer_statut' => 'hal:info_statut_hal',
		'statut_textes_instituer' => array(
			'prop' => 'texte_statut_propose_evaluation',
			'publie' => 'texte_statut_publie',
			'refuse' => 'texte_statut_poubelle',
		),
		'rechercher_champs' => array(
			'titre' => 5,'authid' => 5, 'structid'=>5, 'descriptif' => 3
		),
		'champs_versionnes' => array('titre','descriptif','authid','structid','limite'),
	);

	$tables['spip_hals_publications'] = array(
		'type'=>'hals_publication',
		'table_objet' => 'hals_publications',
		'table_objet_surnoms' => array('halspublication'),
		'texte_retour' => 'icone_retour',
		'texte_objets' => 'hals_publication:icone_hals_publications',
		'texte_objet' => 'hals_publication:icone_hals_publication',
		'texte_modifier' => 'hals_publication:icone_modifier_publication',
		'info_aucun_objet'=> 'hals_publication:info_aucun_hals_publication',
		'info_1_objet' => 'hals_publication:info_1_hals_publication',
		'info_nb_objets' => 'hals_publication:info_nb_hals_publications',
		'date' => 'date_production',
		'principale' => 'oui',
		'field'=> array(
			"id_hals_publication"	=> "bigint(21) NOT NULL",
			"id_hal"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"docid" => "bigint(21) DEFAULT '0' NOT NULL",
			"titre"	=> "text DEFAULT '' NOT NULL",
			"soustitre"	=> "text DEFAULT '' NOT NULL",
			"identifiant" => "text DEFAULT '' NOT NULL",
			"typdoc" => "text DEFAULT '' NOT NULL",
			"date_ecriture" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_soumission" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_modif" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_production" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_revue" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_production_format" => "VARCHAR(255) DEFAULT '' NOT NULL",
			"editeur" => "text DEFAULT '' NOT NULL",
			"citation_reference"	=> "text DEFAULT '' NOT NULL",
			"citation_complete"	=> "text DEFAULT '' NOT NULL",
			"page"	=> "VARCHAR(255) DEFAULT '' NOT NULL",
			"issue"	=> "VARCHAR(255) DEFAULT '' NOT NULL",
			"url_publication"	=> "VARCHAR(255) DEFAULT '' NOT NULL",
			"format" => "VARCHAR(255) DEFAULT '' NOT NULL",
			"date"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"lesauteurs"	=> "text DEFAULT '' NOT NULL",
			"maj"	=> "TIMESTAMP",
			"statut"	=> "varchar(10) DEFAULT '0' NOT NULL",
			"descriptif"	=> "text DEFAULT '' NOT NULL",
			"livre" => "text DEFAULT '' NOT NULL",
			"revue" => "text DEFAULT '' NOT NULL",
			"revue_id" => "bigint(21) DEFAULT '0' NOT NULL",
			"resume" => "text DEFAULT '' NOT NULL",
			"commentaire" => "text DEFAULT '' NOT NULL",
			"isbn" => "text DEFAULT '' NOT NULL",
			"lang"	=> "VARCHAR(10) DEFAULT '' NOT NULL",
			"url_source" => "TINYTEXT DEFAULT '' NOT NULL",
			"tags" => "TEXT DEFAULT '' NOT NULL",
			"hal_complet"	=> "longtext DEFAULT '' NOT NULL",
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_hals_publication",
			"KEY id_hal"	=> "id_hal",
			"KEY statut"	=> "statut",
			"KEY url_publication"	=> "url_publication"
		),
		'join' => array(
			"id_hals_publication"=>"id_hals_publication",
			"id_hal"=>"id_hal"
		),
		'statut' => array(
			array(
				'champ'=>'statut',
				'publie'=>'publie',
				'previsu'=>'publie',
				'exception'=> array('statut','tout')
			),
			array(
				'champ'=>array(array('spip_hals','id_hal'),'statut'),
				'publie'=>'publie',
				'previsu'=>'publie,prop',
				'exception'=>array('statut','tout')),
		),
		'statut_images' => array(
			'publie'=>'puce-publier-8.png','refuse'=>'puce-refuser-8.png'
		),
		'statut_textes_instituer' => array(
			'publie' => 'texte_statut_publie',
			'refuse' => 'texte_statut_refuse',
		),
		'url_voir' => 'hals_publication',
		'url_edit' => 'hals_publication_edit',
		'editable' => 'oui',
		'champs_editables' => array('titre','descriptif','soustitre','resume','commentaire','editeur','page','citation_reference','date_production'),
		'rechercher_champs' => array(
				'titre' => 5, 'soustitre' => 3, 'descriptif' => 3, 'citation_reference' => 3, 'citation_complete' => 3, 'livre' => 2,'revue' => 2,'editeur' => 1
		)
	);

	return $tables;
}

?>
