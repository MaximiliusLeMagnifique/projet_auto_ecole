<?php

function getSalariesSansVehicule() {
	global $cnx;
	
	$req = $cnx->prepare('SELECT IDSALARIE, NOM, PRENOM
							FROM SALARIE S
							WHERE IDSALARIE NOT IN (SELECT IDSALARIE FROM VEHICULE)');
	$req->execute();
	$salaries = $req->fetchAll();
	
	return $salaries;
}

function getVehicules() {
	global $cnx;
	
	$req = $cnx->prepare('SELECT NUMVEHICULE, IMMATRICULATION, MARQUE, MODELE, IDSALARIE
							FROM VEHICULE
							ORDER BY NUMVEHICULE');
	$req->execute();
	$salaries = $req->fetchAll();
	
	return $salaries;
}

function verifDispoNumeroVehicule($numero) {
	global $cnx;

	$req = $cnx->prepare('SELECT NUMVEHICULE
							FROM VEHICULE
							WHERE NUMVEHICULE = :numero');
							
	$req->bindParam(':numero', $numero, PDO::PARAM_INT);
	$req->execute();
	$lecons = $req->fetchAll();

	return $lecons;
}

function creerVehicule($numero, $immatriculation, $marque, $modele, $idSalarie) {
	global $cnx;
	
	$req = $cnx->prepare('INSERT INTO VEHICULE
							VALUES(:numero, :immatriculation, :marque, :modele, :id_salarie)');
	
	$req->bindParam(':numero', $numero, PDO::PARAM_INT);
	$req->bindParam(':immatriculation', $immatriculation, PDO::PARAM_STR);
	$req->bindParam(':marque', $marque, PDO::PARAM_STR);
	$req->bindParam(':modele', $modele, PDO::PARAM_STR);
	$req->bindParam(':id_salarie', $idSalarie, PDO::PARAM_INT);
	$req->execute();
}

function supprimerVehicule($numero) {
	global $cnx;
	
	$req = $cnx->prepare('DELETE FROM VEHICULE WHERE NUMVEHICULE = :numero');
	
	$req->bindParam(':numero', $numero, PDO::PARAM_INT);
	$req->execute();

}
