<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

## valeurs modifiables dans mes_options
## attention il est tres mal vu de prendre une periode < 20 minutes
if (!defined('_PERIODE_SYNDICATION_HAL')) {
	define('_PERIODE_SYNDICATION_HAL', 2 * 60);
}
if (!defined('_PERIODE_SYNDICATION_SUSPENDUE_HAL')) {
	define('_PERIODE_SYNDICATION_SUSPENDUE_HAL', 24 * 60);
}

include_spip('inc/hal');

// http://doc.spip.org/@genie_syndic_dist
function genie_hal_dist($t) {
	return executer_une_syndication_hal();
}

//
// Effectuer la syndication d'un unique site,
// retourne 0 si aucun a faire ou echec lors de la tentative
//
function executer_une_syndication_hal() {
	// Et un site 'oui' de plus de 2 heures, qui passe en 'sus' s'il echoue
	$where = 'NOT(' . sql_date_proche('date_syndic', (0 - _PERIODE_SYNDICATION_HAL), 'MINUTE') . ' AND statut="publie")';
	$id_hal = sql_getfetsel('id_hal', 'spip_hals', $where, '', 'date_syndic', '1');
	if ($id_hal) {
		// inserer la tache dans la file, avec controle d'unicite
		job_queue_add('hal_a_jour', 'hal_a_jour', [$id_hal], 'genie/hal', true);
	}
	return 0;
}


/**
 * Mettre a jour le dépot
 * Attention, cette fonction ne doit pas etre appellee simultanement
 * sur un meme site: un verrouillage a du etre pose en amont.
 * => elle doit toujours etre appelee par job_queue_add
 *
 * @param int $now_id_hal
 * 	Identifiant numérique du dépot
 * @return bool|string
 */
function hal_a_jour($now_id_hal) {
	$call = debug_backtrace();
	if ($call[1]['function'] !== 'queue_start_job') {
		spip_log('hal_a_jour doit etre appelee par JobQueue Cf. http://trac.rezo.net/trac/spip/changeset/10294', _LOG_ERREUR);
	}

	$row = sql_fetsel('*', 'spip_hals', 'id_hal=' . intval($now_id_hal) . ' AND statut = "publie"');

	if (!$row) {
		return 0;
	}

	$url_syndic = $row['url_syndic'];
	if ($row['moderation'] == 'oui') {
		$moderation = 'dispo';	// a valider
	} else {
		$moderation = 'publie';	// en ligne sans validation
	}

	// Aller chercher les donnees du JSON et les analyser
	include_spip('inc/distant');
	include_spip('inc/config');

	$limite = ($row['limite'] > 0) ? $row['limite'] : (defined('_SPIP_HAL_ROWS') ? _SPIP_HAL_ROWS : lire_config('hal/nb_publication', '100'));

	$url_syndic = parametre_url(parametre_url($url_syndic, 'rows', $limite, '&'), 'fl', '*', '&');

	$json = recuperer_url($url_syndic, ['transcoder' => true, 'taille_max' => 268_435_456]);
	$json = $json['page'] ?? '';
	if (!$json) {
		$publications = _T('hal:avis_echec_recuperation');
	} else {
		$publications = analyser_publications($json, $url_syndic);
	}

	// Renvoyer l'erreur le cas echeant
	if (!is_array($publications)) {
		sql_updateq('spip_hals', ['date_syndic' => date('Y-m-d H:i:s')], 'id_hal=' . intval($now_id_hal));
		return 0;
	}

	// Les enregistrer dans la base
	$faits = [];
	foreach ($publications as $data) {
		inserer_publication_hal($data, $now_id_hal, $moderation, $url_syndic, $faits);
	}

	sql_updateq('spip_hals', ['date_syndic' => date('Y-m-d H:i:s')], 'id_hal=' . intval($now_id_hal));
	return 0; # c'est bon
}


//
// Insere une publication (renvoie true si l'article est nouveau)
// en  verifiant qu'on ne vient pas de l'ecrire avec
// un autre item du meme feed qui aurait le meme link
//
function inserer_publication_hal($data, $now_id_hal, $statut, $url_syndic, &$faits) {
	$ajout = null;
	$id_hal = null;
 // Creer le lien s'il est nouveau - cle=(id_hal,url)
	// On coupe a 255 caracteres pour eviter tout doublon
	// sur une URL de plus de 255 qui exloserait la base de donnees
	$le_lien = substr($data['url_publication'], 0, 255);
	$docid = $data['docid'];

	// si true, un lien deja syndique arrivant par une autre source est ignore
	// par defaut [false], chaque source a sa liste de liens, eventuellement
	// les memes
	define('_HAL_SYNDICATION_URL_UNIQUE', true);

	// Si false, on ne met pas a jour un lien deja syndique avec ses nouvelles
	// donnees ; par defaut [true] : on met a jour si le contenu a change
	// Attention si on modifie a la main un article syndique, les modifs sont
	// ecrasees lors de la syndication suivante
	define('_HAL_SYNDICATION_CORRECTION', true);

	// Chercher les liens de meme cle
	// S'il y a plusieurs liens qui repondent, il faut choisir le plus proche
	// (ie meme titre et pas deja fait), le mettre a jour et ignorer les autres
	$n = 0;
	$id = null;
	$s = sql_select(
		'id_hals_publication,docid,titre,id_hal,statut',
		'spip_hals_publications',
		'docid=' . sql_quote($docid)
		. (_HAL_SYNDICATION_URL_UNIQUE
			? ''
			: ' AND id_hal=' . intval($now_id_hal))
		. ' AND ' . sql_in('id_hals_publication', $faits, 'NOT'),
		'',
		'maj DESC'
	);
	while ($a = sql_fetch($s)) {
		$id =  $a['id_hals_publication'];
		$id_hal = $a['id_hal'];
		if ($a['titre'] == $data['titre']) {
			$id_hals_publication = $id;
			break;
		}
		$n++;
	}
	// S'il y en avait qu'un, le prendre quel que soit le titre
	if ($n == 1) {
		$id_hals_publication = $id;
	}
	// Si l'article n'existe pas, on le cree
	elseif (!isset($id_hals_publication)) {
		$champs = [
			'id_hal' => $now_id_hal,
			'url_publication' => $le_lien,
			'docid' => $docid,
			'date' => date('Y-m-d H:i:s'),
			'statut'  => $statut
		];
		// Envoyer aux plugins
		$champs = pipeline(
			'pre_insertion',
			[
				'args' => [
					'table' => 'spip_hals_publications',
				],
				'data' => $champs
			]
		);
		$ajout = $id_hals_publication = sql_insertq('spip_hals_publications', $champs);
		if (!$ajout) {
			return;
		}
		pipeline(
			'post_insertion',
			[
				'args' => [
					'table' => 'spip_hals_publications',
					'id_objet' => $id_hals_publication
				],
				'data' => $champs
			]
		);
	}
	$faits[] = $id_hals_publication;

	// Si le lien n'est pas nouveau, plusieurs options :
	if (!$ajout) {
		// 1. Lien existant : on corrige ou pas ?
		if (!_HAL_SYNDICATION_CORRECTION) {
			return;
		}
		// 2. Le lien existait deja, lie a un autre hal
		if (_HAL_SYNDICATION_URL_UNIQUE and $id_hal != $now_id_hal) {
			return;
		}
	}

	// Mise a jour du contenu (titre,auteurs,description,date?,source...)
	$vals = [
		'docid' => $data['docid'],
		'titre' => $data['titre'],
		'soustitre' => $data['soustitre'],
		'identifiant' => $data['identifiant'],
		'typdoc' => $data['typdoc'],
		'date_ecriture' => $data['date_ecriture'],
		'date_soumission' => $data['date_soumission'],
		'date_production' => $data['date_production'],
		'date_production_format' => $data['date_production_format'],
		'date_modif' => $data['date_modif'],
		'citation_reference' => $data['citation_reference'],
		'citation_complete' => $data['citation_complete'],
		'page' => $data['page'],
		'lesauteurs' => $data['lesauteurs'],
		'livre' => $data['livre'],
		'revue' => $data['revue'],
		'revue_id' => $data['revue_id'],
		'commentaire' => $data['commentaire'],
		'isbn' => $data['isbn'],
		//'format' => $data['format'],
		'hal_complet' => $data['hal_complet'],
		'editeur' => $data['editeur'],
		'url_publication' => $data['url_publication'],
		'tags' => $data['tags'],
		'lang' => substr($data['lang'], 0, 10)
	];

	sql_updateq('spip_hals_publications', $vals, 'id_hals_publication=' . intval($id_hals_publication));

	// Point d'entree post_syndication
	pipeline(
		'post_syndication',
		[
			'args' => [
				'table' => 'spip_hals_publications',
				'id_objet' => $id_hals_publication,
				'url_publication' => $le_lien,
				'id_hal' => $now_id_hal,
				'ajout' => $ajout,
			],
			'data' => $data
		]
	);

	return $ajout;
}
