<?php

if (!isset($_SESSION['mail'])) {
	header('location:main.php?rubrique=index');
}


if (isset($_GET['action'])) {
	$action = $_GET['action'];
} else {
	$action = 'lister';
}

require_once('modeles/m_examen.php');
require_once('inc/fonctions_diverses.php');


switch ($action) {
	case 'lister':
		$examens = getExamens();
		require_once('vues/v_examen.lister.php');
		break;
		
		
		
	case 'creer':
		$date    = '';
		$type    = '';
		$message = '';
		
		if (isset($_POST['soumission'])) {
			$date = $_POST['date'];
			$type = $_POST['type'];
			
			if ($date == '') {
				$message = 'Le champ "Date" doit être renseigné.';
			} else {
				if (validateDate($date)) {
					creerExamen($date, $type);
					header('location:main.php?rubrique=examen&action=lister');
				} else {
					$message = 'La valeur saisie dans le champ "Date" est incorrecte.';
				}
			}
		}
		
		require_once('vues/v_examen.creer.php');
		break;
		
		
		
	case 'supprimer':
		supprimerExamen($_GET['id']);
		header('location:main.php?rubrique=examen&action=lister');
		break;
		
		
		
	case 'editer':
		$date = '';
		$type = '';
		
		if (isset($_POST['soumission'])) {
			$date = $_POST['date'];
			$type = $_POST['type'];
			
			if ($date == '') {
				$message = 'Le champ "Date" doit être renseigné.';
			} else {
				if (validateDate($date)) {
					modifierExamen($_GET['id'], $date, $type);
					header('location:main.php?rubrique=examen&action=lister');
				} else {
					$message = 'La valeur saisie dans le champ "Date" est incorrecte.';
				}
			}
		} else {
			$examen = getExamen($_GET['id']);
			
			$date = $examen['DATEEXAM'];
			$type = $examen['TYPEEXAM'];
		}
		
		require_once('vues/v_examen.editer.php');
		
		break;
		
		
		
	case 'lister_editer_participants':
	
		if (isset($_POST['soumission'])) {
			foreach($_SESSION['id_inscrits'] as $id_inscrit) {
				modifierStatutPassageExamenEleve($_GET['id'], $id_inscrit, $_POST['statut'.$id_inscrit]);
			}
			
			$message = 'Les statuts ont bien étés modifiés';
		}
		
		$examen = getExamen($_GET['id']);
		$inscrits = getInscritsAExamen($_GET['id']);
		
		$_SESSION['id_inscrits'] = array();
		unset($_SESSION['id_inscrits']);
		
		$i = 0;
		
		foreach($inscrits as $inscrit) {
			$_SESSION['id_inscrits'][$i] = $inscrit['IDELEVE'];
			$i++;
		}
		
		require_once('vues/v_examen.lister_editer_participants.php');
		break;
		
	case 'ajouter_participant':
		
		if (isset($_POST['soumission'])) {
			$eleve = getEleve($_POST['participant']);
			$examen = getExamen($_GET['id']);
			
			if ($examen['TYPEEXAM'] == 'conduite') {
				if ($eleve['AGE'] < 18) {
					$message = 'Impossible d\'ajouter l\'élève '.strtoupper($eleve['NOM']).' '.$eleve['PRENOM'].' à cet examen : il n\'a pas encore 18 ans.';
				} else {
					$dernierPassageCodeEleve = getDernierPassageCodeEleve($_POST['participant']);
					
					if (($dernierPassageCodeEleve['AGE'] < 2) && ($dernierPassageCodeEleve['STATUTEXAM'] == 'réussi')) {
						
					} else {
						$message = 'Impossible d\'ajouter l\'élève '.strtoupper($eleve['NOM']).' '.$eleve['PRENOM'].' à cet examen : il n\'est pas en possession du code depuis moins de 2 ans.';
					}
				}
			} else {
				inscrireEleveAExamen($_GET['id'], $_POST['participant']);
				$message = 'L\'élève '.strtoupper($eleve['NOM']).' '.$eleve['PRENOM'].' a bien été ajouté à cet examen.';
			}
		}
	
		$non_inscrits = getNonInscritsAExamen($_GET['id']);
		require_once('vues/v_examen.ajouter_participant.php');
		break;
		
		
		
	case 'supprimer_participant':
		desinscrireEleveAExamen($_GET['idExamen'], $_GET['idEleve']);
		header('location:main.php?rubrique=examen&action=lister_editer_participants&id='.$_GET['idExamen']);
		break;
}