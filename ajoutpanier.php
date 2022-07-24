<?php 
header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');



$panier = file_get_contents('jsons/panier.json');
echo $panier;

// <font color="#333333"><b>R&eacute;partition du CA par taux de
//                TVA</b><br>
//                CA pour TVA=0%...... </font> <? echo $tva_ca0; ?><!-- &euro; <font color="#333333">( -->
<!--                --><?// echo $n_tva0; ?><!-- )</font> </font><font size='1' face="Arial, Helvetica, sans-serif" color="#FF0000">7070</font><font face="Courier New, Courier, mono" size="2"><br>-->
<!--                <font color="#333333"> CA pour TVA=1.05%... </font> --><?// echo $tva_ca1; ?><!-- -->
<!--                &euro; <font color="#333333">( --><?// echo $n_tva1; ?><!-- )</font> </font><font size='1' face="Arial, Helvetica, sans-serif" color="#FF0000">707105</font><font face="Courier New, Courier, mono" size="2"><br>-->
<!--                <font color="#333333">CA pour TVA=2.1%.... </font> --><?// echo $tva_ca2; ?><!-- -->
<!--                &euro; <font color="#333333">( --><?// echo $n_tva2; ?><!-- )</font> </font><font size='1' face="Arial, Helvetica, sans-serif" color="#FF0000">707210</font><font face="Courier New, Courier, mono" size="2"><br>-->
<!--                <font color="#333333">CA pour TVA=8.5%.... </font> --><?// echo $tva_ca8; ?><!-- -->
<!--                &euro; <font color="#333333">( --><?// echo $n_tva8; ?><!-- )</font> </font><font size='1' face="Arial, Helvetica, sans-serif" color="#FF0000">70785</font><font face="Courier New, Courier, mono" size="2"><br>-->
<!--                <br>-->
<!--                <font color="#333333"><b>Divers stats</b><br>-->
<!--                </font><font face="Courier New, Courier, mono" size="2"><font color="#333333"> -->
<!--                </font></font> <font face="Courier New, Courier, mono" size="2"><font color="#333333">Reste -->
<!--                exo.............. </font> -->
<!--                --><?//
//				$exec_vi=0;
//				if ($virement_dans_ca!=1) $exec_vi=$vi;
//				$reste_exo=rounder($taxes_ca_ht-$b11-$b11_splomb-$c11-$webcaisse_melange-$d11-$tabac-$timbres-rounder($presse/1.0105)-rounder($march_210/1.0210)-rounder($march_85_total/1.085)-rounder($march_85_tele/1.085)-rounder($march_85_lav/1.085)+$exec_vi);
//
//
//				//ADD BY YANNICK
//
//				if (!isset($date_min) || (isset($make_ebp) && $date_min == ""))
//				{
//				   $condidate = "date = '$datedujour'";
//				}
//				else
//				{
//				   $date_min = ereg_replace("([[:digit:]]{2})/([[:digit:]]{2})/([[:digit:]]{4})","\\3-\\2-\\1", $date_min);
//				   $date_max = ereg_replace("([[:digit:]]{2})/([[:digit:]]{2})/([[:digit:]]{4})","\\3-\\2-\\1", $date_max);
//				   $condidate = "date > '$date_min' AND date < '$date_max'";
//				}
//
//
//				$select = "SELECT restexo FROM table_client_mareux_restexo WHERE $condidate";
//				$query = mysql_query($select);
//				$total = 0;
//
//				while ($tab = mysql_fetch_array($query))
//				{
//					$total += $tab["restexo"];
//				}
//
//				echo $reste_exo+$total;
//				$reste_exo = $reste_exo+$total;
//
//
//				$var2=rounder($reste_exo+$timbres+$tabac+$d11+$webcaisse_melange+$c11+$b11_splomb+$b11);
//				?>
<!--                &euro;</font><br>-->
<!--                <font face="Courier New, Courier, mono" size="2"><font color="#333333">Presse................. -->
<!--                </font> -->
<!--                --><?// $temp_presse_ht=rounder($presse/1.0105);
//				 echo rounder($presse/1.0105); ?>
<!--                &euro; HT</font><br>-->
<!--                <font face="Courier New, Courier, mono" size="2"><font color="#333333">Marchandises -->
<!--                2.10%..... </font> -->
<!--                --><?// $temp_march_210_total= rounder($march_210/1.0210);
//				echo rounder($march_210/1.0210); ?>
<!--                &euro; HT</font><br>-->
<!--                <font face="Courier New, Courier, mono" size="2"><font color="#333333">Marchandises -->
<!--                8.5%(&sup2;)... </font> -->
<!--                --><?// $temp_march_85_total= rounder($march_85_total/1.085);
//				echo rounder($march_85_total/1.085); ?>
<!--                &euro;</font> HT<br>-->
<!--                <font face="Courier New, Courier, mono" size="2"><font color="#333333">Cartes -->
<!--                t&eacute;l&eacute;phoniques...</font> -->
<!--                --><?// $temp_march_85_tele=rounder($march_85_tele/1.085);
//				echo rounder($march_85_tele/1.085); ?>
<!--                &euro; HT</font>-->
<!--                </font></p>-->
<!--         --><?//
//if ($aff_ca_boutique_perso_elfstesuzanne==1){
//echo "<p><font face='Courier New, Courier, mono' size='2'><font face='Courier New, Courier, mono' size='2'><font color='#333333'>CA
//                Boutique............ </font>";
//
//                 $temp_caperso=rounder($ca_jour-$tabac-$d11-$b11-$b11_splomb-$c11-$march_85_tele/1.085-$march_85_lav/1.085);
//echo rounder($temp_caperso);
//echo " &euro;</font></font></p>";
//}
//?>
<!---->
<?//
//if ($famille_systeme_sfr_caissier>0){
//echo "<p><font face='Courier New, Courier, mono' size='2'><font face='Courier New, Courier, mono' size='2'><font color='#333333'>Cartes SFR............. </font>";
//echo $cartesfr_vendu;
//echo " &euro; ( ".$ncartesfr_vendu." )</font></font></p>";
 ?>
