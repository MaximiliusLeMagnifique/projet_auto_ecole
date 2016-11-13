<?php

function getEleves() {
	global $cnx;

	$req = $cnx->prepare('SELECT IDELEVE, NOM, PRENOM
							FROM ELEVE
							ORDER BY NOM');
	$req->execute();
	$eleves = $req->fetchAll();

	return $eleves;
	
}

function getAchatsTicketsEleve($idEleve) {
	global $cnx;
	
	$req = $cnx->prepare('SELECT IDACHAT, QUANTITE, TO_CHAR(DATEACHAT, \'DD/MM/YYYY HH24:MI:SS\') AS DATEACHAT, MOYENPAIEMENT
							FROM ACHAT_TICKET
							WHERE IDELEVE = :id_eleve');
	
	$req->bindParam(':id_eleve', $idEleve, PDO::PARAM_INT);
	$req->execute();
	$achatsTickets = $req->fetchAll();

	return $achatsTickets;
}

function getEleve($idEleve) {
	global $cnx;
	
	$req = $cnx->prepare('SELECT NOM, PRENOM
							FROM ELEVE
							WHERE IDELEVE = :id_eleve');
	
	$req->bindParam(':id_eleve', $idEleve, PDO::PARAM_INT);
	$req->execute();
	$eleve = $req->fetch(PDO::FETCH_ASSOC);

	return $eleve;
}

function getAchatTicket($idAchat) {	
	global $cnx;
	
	$req = $cnx->prepare('SELECT QUANTITE, TO_CHAR(DATEACHAT, \'DD/MM/YYYY HH24:MI:SS\') AS DATEACHAT, MOYENPAIEMENT
							FROM ACHAT_TICKET
							WHERE IDACHAT = :id_achat');
	
	$req->bindParam(':id_achat', $idAchat, PDO::PARAM_INT);
	$req->execute();
	$achatTickets = $req->fetch(PDO::FETCH_ASSOC);

	return $achatTickets;
}

function creerAchatTickets($date, $quantite, $moyenPaiement, $idEleve) {
	global $cnx;
	
	$req = $cnx->prepare('INSERT INTO ACHAT_TICKET
							VALUES(null, '.$quantite.', TO_DATE(\''.$date.'\', \'DD/MM/YYYY HH24:MI:SS\'), \''.$moyenPaiement.'\', '.$idEleve.')');
	
	$req->execute();
}

function modifierAchatTickets($date, $quantite, $moyenPaiement, $idAchat) {
	global $cnx;
	
	$req = $cnx->prepare('UPDATE ACHAT_TICKET
							SET DATEACHAT = TO_DATE(\''.$date.'\', \'DD/MM/YYYY HH24:MI:SS\'), QUANTITE = '.$quantite.', MOYENPAIEMENT = \''.$moyenPaiement.'\'
							WHERE IDACHAT = '.$idAchat);
	$req->execute();
}



function supprimerAchatTickets($idAchat) {
	global $cnx;
	
	$req = $cnx->prepare('DELETE FROM ACHAT_TICKET
							WHERE IDACHAT = :id');
	$req->bindParam(':id', $idAchat, PDO::PARAM_INT);
	$req->execute();
}