<?php
/**
 * IBATP — Traitement du formulaire de contact
 * Fonctionne sur l'hébergement web mutualisé OVHcloud (fonction mail() native).
 *
 * À PERSONNALISER : remplacez ibatp.fr par votre nom de domaine.
 */

$destinataire = 'contact@ibatp.fr';   // adresse qui reçoit les demandes
$expediteur   = 'contact@ibatp.fr';   // doit être une adresse de VOTRE domaine (exigence OVH anti-spam)

// --------------------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

function retour_erreur() {
    header('Location: index.html?envoi=erreur#contact');
    exit;
}

function propre($valeur, $max) {
    $valeur = trim((string) $valeur);
    $valeur = str_replace(array("\r", "\n", "%0a", "%0d"), ' ', $valeur); // anti-injection d'en-têtes
    return mb_substr($valeur, 0, $max, 'UTF-8');
}

// Pot de miel anti-robots : un humain laisse ce champ vide
if (!empty($_POST['site_web'])) {
    header('Location: merci.html');
    exit;
}

$nom       = propre($_POST['nom'] ?? '', 120);
$societe   = propre($_POST['societe'] ?? '', 120);
$email     = propre($_POST['email'] ?? '', 160);
$telephone = propre($_POST['telephone'] ?? '', 40);
$mission   = propre($_POST['mission'] ?? '', 60);
$message   = mb_substr(trim((string) ($_POST['message'] ?? '')), 0, 5000, 'UTF-8');
$rgpd      = !empty($_POST['rgpd']);

if ($nom === '' || $message === '' || !$rgpd || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    retour_erreur();
}

$sujet = 'Nouvelle demande d\'étude — ' . $mission . ' — ' . $nom;
$sujet_encode = '=?UTF-8?B?' . base64_encode($sujet) . '?=';

$corps  = "Nouvelle demande reçue depuis le site IBATP\n";
$corps .= "==========================================\n\n";
$corps .= "Nom       : $nom\n";
$corps .= "Société   : " . ($societe !== '' ? $societe : '-') . "\n";
$corps .= "E-mail    : $email\n";
$corps .= "Téléphone : " . ($telephone !== '' ? $telephone : '-') . "\n";
$corps .= "Mission   : $mission\n\n";
$corps .= "Projet :\n$message\n\n";
$corps .= "------------------------------------------\n";
$corps .= "Envoyé le " . date('d/m/Y à H:i') . " — consentement RGPD donné.\n";
$corps .= "Répondez directement à ce message pour écrire au demandeur.\n";

$entetes  = "From: Site IBATP <$expediteur>\r\n";
$entetes .= "Reply-To: $email\r\n";
$entetes .= "MIME-Version: 1.0\r\n";
$entetes .= "Content-Type: text/plain; charset=UTF-8\r\n";
$entetes .= "Content-Transfer-Encoding: 8bit\r\n";

if (mail($destinataire, $sujet_encode, $corps, $entetes)) {
    header('Location: merci.html');
} else {
    retour_erreur();
}
exit;
