<?php
	
	function getLeconsDuJour() {
	global $cnx;
	
	$req = $cnx->prepare('SELECT LC.IDLECON, TO_CHAR(LC.DATELECON, \'DD/MM/YYYY HH24:MI:SS\') AS "DATELECON", LC.STATUTLECON, E.NOM AS "NOMELEVE", E.PRENOM AS "PRENOMELEVE", S.NOM AS "NOMSALARIE", S.PRENOM AS "PRENOMSALARIE", LC.NUMVEHICULE
							FROM LECON_CONDUITE LC, ELEVE E, SALARIE S
							WHERE LC.IDSALARIE = S.IDSALARIE
							AND LC.IDELEVE = E.IDELEVE
							AND TRUNC(LC.DATELECON) = TRUNC(SYSDATE)
							ORDER BY LC.DATELECON');
	$req->execute();
	$lecons = $req->fetchAll();
	
	return $lecons;
}

function getLeconsFiltrage($dateDebut, $dateFin, $statut, $idEleve, $idSalarie, $numVehicule) {
	global $cnx;
	
	$chaine_req = 'SELECT LC.IDLECON, TO_CHAR(LC.DATELECON, \'DD/MM/YYYY HH24:MI:SS\') AS "DATELECON", LC.STATUTLECON, E.NOM AS "NOMELEVE", E.PRENOM AS "PRENOMELEVE", S.NOM AS "NOMSALARIE", S.PRENOM AS "PRENOMSALARIE", LC.NUMVEHICULE
							FROM LECON_CONDUITE LC, ELEVE E, SALARIE S
							WHERE LC.IDSALARIE = S.IDSALARIE
							AND LC.IDELEVE = E.IDELEVE
							AND LC.STATUTLECON LIKE \''.$statut.'\'
							AND E.IDELEVE LIKE \''.$idEleve.'\'
							AND S.IDSALARIE LIKE \''.$idSalarie.'\'
							AND LC.NUMVEHICULE LIKE \''.$numVehicule.'\'';
	
	if ($dateDebut != '' and $dateFin != '') {
		$chaine_req = $chaine_req.' AND LC.DATELECON >= TO_DATE(\''.$dateDebut.'\', \'DD/MM/YYYY HH24:MI:SS\')'.' AND LC.DATELECON <= TO_DATE(\''.$dateFin.'\', \'DD/MM/YYYY HH24:MI:SS\')';
	}
	
	$chaine_req = $chaine_req.' ORDER BY LC.DATELECON';
	
	$req = $cnx->prepare($chaine_req);
	$req->execute();
	$lecons = $req->fetchAll();
	
	return $lecons;
}

function getMoniteurs() {
	global $cnx;
	
	$req = $cnx->prepare('SELECT IDSALARIE, NOM, PRENOM
							FROM SALARIE
							WHERE FONCTION = \'moniteur\'');
	$req->execute();
	$moniteurs = $req->fetchAll();
	
	return $moniteurs;
}

function getEleves() {
	global $cnx;
	
	$req = $cnx->prepare('SELECT IDELEVE, NOM, PRENOM
							FROM ELEVE');
	$req->execute();
	$eleves = $req->fetchAll();
	
	return $eleves;
}

function getVehicules() {
	global $cnx;
	
	$req = $cnx->prepare('SELECT NUMVEHICULE
							FROM VEHICULE');
	$req->execute();
	$vehicules = $req->fetchAll();
	
	return $vehicules;
}

function getLecon($id) {
	global $cnx;
	
	$req = $cnx->prepare('SELECT IDLECON, TO_CHAR(DATELECON, \'DD/MM/YYYY HH24:MI:SS\') AS DATELECON, STATUTLECON, IDSALARIE, IDELEVE, NUMVEHICULE
							FROM LECON_CONDUITE
							WHERE IDLECON = :id');
	$req->bindParam(':id', $id, PDO::PARAM_INT);
	$req->execute();
	$lecon = $req->fetch(PDO::FETCH_ASSOC);
	
	return $lecon;
}

function supprimerLecon($id) {
	global $cnx;
	
	$req = $cnx->prepare('DELETE FROM LECON_CONDUITE
							WHERE IDLECON = :id');
	$req->bindParam(':id', $id, PDO::PARAM_INT);
	$req->execute();
}

function getPreferencesEleve($idEleve) {
	global $cnx;
	
	$req = $cnx->prepare('SELECT E.IDSALARIE, V.NUMVEHICULE
							FROM ELEVE E, SALARIE S, VEHICULE V
							WHERE E.IDSALARIE = S.IDSALARIE
							AND S.IDSALARIE = V.IDSALARIE
							AND E.IDELEVE = :id_eleve');
	$req->bindParam(':id_eleve', $idEleve, PDO::PARAM_INT);
	$req->execute();
	$preferences = $req->fetch(PDO::FETCH_ASSOC);
	
	return $preferences;
}

function verifDispoDateLecon($date, $idSalarie, $idEleve, $numVehicule) {
	global $cnx;

	$req = $cnx->prepare('SELECT IDLECON
							FROM LECON_CONDUITE
							WHERE IDSALARIE = :id_salarie
							AND IDELEVE = :id_eleve
							AND NUMVEHICULE = :num_vehicule
							AND STATUTLECON <> \'annulée\'
							AND DATELECON BETWEEN TO_DATE(\''.$date.'\', \'DD/MM/YYYY HH24:MI:SS\') AND (TO_DATE(\''.$date.'\', \'DD/MM/YYYY HH24:MI:SS\') + 3599/86400)');
							
	$req->bindParam(':id_salarie', $idSalarie, PDO::PARAM_INT);
	$req->bindParam(':id_eleve', $idEleve, PDO::PARAM_INT);
	$req->bindParam(':num_vehicule', $numVehicule, PDO::PARAM_INT);
	$req->execute();
	$lecons = $req->fetchAll();

	return $lecons;
}

function verifDispoDateLeconSalarie($idLecon, $date, $idSalarie) {
	global $cnx;

	$req = $cnx->prepare('SELECT IDLECON
							FROM LECON_CONDUITE
							WHERE IDSALARIE = :id_salarie
							AND STATUTLECON <> \'annulée\'
							AND IDLECON <> :id_lecon
							AND DATELECON BETWEEN TO_DATE(\''.$date.'\', \'DD/MM/YYYY HH24:MI:SS\') AND (TO_DATE(\''.$date.'\', \'DD/MM/YYYY HH24:MI:SS\') + 3599/86400)');
							
	$req->bindParam(':id_salarie', $idSalarie, PDO::PARAM_INT);
	$req->bindParam(':id_lecon', $idLecon, PDO::PARAM_INT);
	$req->execute();
	$lecons = $req->fetchAll();

	return $lecons;
}

function verifDispoDateLeconEleve($idLecon, $date, $idEleve) {
	global $cnx;

	$req = $cnx->prepare('SELECT IDLECON
							FROM LECON_CONDUITE
							WHERE IDELEVE = :id_eleve
							AND STATUTLECON <> \'annulée\'
							AND IDLECON <> :id_lecon
							AND DATELECON BETWEEN TO_DATE(\''.$date.'\', \'DD/MM/YYYY HH24:MI:SS\') AND (TO_DATE(\''.$date.'\', \'DD/MM/YYYY HH24:MI:SS\') + 3599/86400)');
							
	$req->bindParam(':id_eleve', $idEleve, PDO::PARAM_INT);
	$req->bindParam(':id_lecon', $idLecon, PDO::PARAM_INT);
	$req->execute();
	$lecons = $req->fetchAll();

	return $lecons;
}

function verifDispoDateLeconVehicule($idLecon, $date, $numVehicule) {
	global $cnx;

	$req = $cnx->prepare('SELECT IDLECON
							FROM LECON_CONDUITE
							WHERE NUMVEHICULE = :num_vehicule
							AND IDLECON <> :id_lecon
							AND DATELECON BETWEEN TO_DATE(\''.$date.'\', \'DD/MM/YYYY HH24:MI:SS\') AND (TO_DATE(\''.$date.'\', \'DD/MM/YYYY HH24:MI:SS\') + 3599/86400)');
							
	$req->bindParam(':num_vehicule', $numVehicule, PDO::PARAM_INT);
	$req->bindParam(':id_lecon', $idLecon, PDO::PARAM_INT);
	$req->execute();
	$lecons = $req->fetchAll();

	return $lecons;
}

function ajouterLecon($date, $statut, $idSalarie, $idEleve, $numVehicule) {
	global $cnx;
	
	$req = $cnx->prepare('INSERT INTO LECON_CONDUITE
							VALUES (null, TO_DATE(\''.$date.'\', \'DD/MM/YYYY HH24:MI:SS\'), :statut, :id_salarie, :id_eleve, :num_vehicule)');
							
	$req->bindParam(':statut', $statut, PDO::PARAM_INT);
	$req->bindParam(':id_salarie', $idSalarie, PDO::PARAM_INT);
	$req->bindParam(':id_eleve', $idEleve, PDO::PARAM_INT);
	$req->bindParam(':num_vehicule', $numVehicule, PDO::PARAM_INT);
	$req->execute();
}


function modifierLecon($idLecon, $date, $statut, $idSalarie, $idEleve, $numVehicule) {
	global $cnx;
	
	$req = $cnx->prepare('UPDATE LECON_CONDUITE
							SET DATELECON = TO_DATE(\''.$date.'\', \'DD/MM/YYYY HH24:MI:SS\'),
							STATUTLECON = :statut,
							IDSALARIE = :id_salarie,
							IDELEVE = :id_eleve,
							NUMVEHICULE = :num_vehicule
							WHERE IDLECON = :id_lecon');
							
	$req->bindParam(':id_lecon', $idLecon, PDO::PARAM_INT);
	$req->bindParam(':statut', $statut, PDO::PARAM_STR);
	$req->bindParam(':id_salarie', $idSalarie, PDO::PARAM_INT);
	$req->bindParam(':id_eleve', $idEleve, PDO::PARAM_INT);
	$req->bindParam(':num_vehicule', $numVehicule, PDO::PARAM_INT);
	$req->execute();
}