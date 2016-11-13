<?php

session_start();

require_once('inc/bdd.inc.php');
require_once('inc/header.inc.php');
	
if (isset($_GET['rubrique'])) {
	if ($_GET['rubrique'] == 'index') {
		require_once('controleurs/c_index.php');
		
	} else if ($_GET['rubrique'] == 'login') {
		require_once('controleurs/c_login.php');
		
	} else if ($_GET['rubrique'] == 'examen') {
		require_once('controleurs/c_examen.php');
		
	} else if ($_GET['rubrique'] == 'vehicule') {
		require_once('controleurs/c_vehicule.php');
		
	} else if ($_GET['rubrique'] == 'lecon') {
		require_once('controleurs/c_lecon.php');
		
	} else if ($_GET['rubrique'] == 'achat') {
		require_once('controleurs/c_achat.php');
		
	}
} else {
	require_once('controleurs/c_index.php');
}

require_once('inc/footer.inc.php');