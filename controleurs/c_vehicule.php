<?php

if (!isset($_SESSION['mail'])) {
	header('location:main.php?rubrique=index');
}


if (isset($_GET['action'])) {
	$action = $_GET['action'];
} else {
	$action = 'lister';
}

require_once('modeles/m_vehicule.php');

switch ($action) {
	case 'lister':
		$vehicules = getVehicules();
		require_once('vues/v_examen.lister.php');
		break;

	case 'creer':
		$salaries = getSalariesSansVehicule();
		$immatriculation = '';
		$marque = '';
		$modele = '';
		$numero = '';
		$idSalarie = '';
		
		if (isset($_POST['soumission'])) {
			$immatriculation = $_POST['immatriculation'];
			$marque = $_POST['marque'];
			$modele = $_POST['modele'];
			$numero = $_POST['numero'];
			$idSalarie = $_POST['salarie'];
			
			if ($immatriculation == '' or $marque == '' or $modele = '' or $numero = '') {
				$message = 'Tous les champs doivent être renseignés.';
			} else {
				
				$verif_dispo_numero = verifDispoNumeroVehicule($numero);
				
				if (empty($verif_dispo_numero)) {
					creerVehicule($numero, $immatriculation, $marque, $modele, $idSalarie);
					header('location:main.php?rubrique=vehicule&action=lister');
				} else {
					$message = 'Le numéro de véhicule saisi est déjà utilisé.';
				}
			}
		}
		
		require_once('vues/v_vehicule.creer.php');
		break;
	case 'supprimer':
		supprimerVehicule($_GET['numero']);
		header('location:main.php?rubrique=vehicule&action=lister');
		break;
}