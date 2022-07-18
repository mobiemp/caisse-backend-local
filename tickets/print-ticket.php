<?php 
include ('../parametre.php');
include('../functions.php');



$magasin = ticketFormatString($magasin,40);

$ticket_entete=utf8_encode("
	    *********************
$magasin
        *********************
$adresse
        $adresse2

       Telephone: $numero_telephone
       SIRET: $siret
");
$ticket_corps = "
 Qte*PU   Designation     Mttc   TVA
--------------------------------------
";

$total_a_payer = str_repeat(" ", 10) ."TOTAL A PAYER TTC". str_repeat(" ",4) ;


$ticket_pied = "
      MERCI DE VOTRE VISITE
        ET A TRES BIENTOT
------------------------------------------

ECHANGE OU AVOIR SOUS 72H
MARCHANDISE AVEC EMBALLAGE D'ORIGINE 
INTACT ET TICKET DE CAISSE
";

$qte_prix_limit = 9;
$designiation_limit = 15;
$mttc_limit = 8;
$tva_limit = 4;



 ?>
