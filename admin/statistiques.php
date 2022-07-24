<?php


if(isset($_POST['changeDate'])){
	$date = $_POST['changeDate'];
	$dateFormated = strtotime(str_replace('-', '/', $date));
}
else{
	$dateFormated = date('d/m/Y');
	$date = date('Y-m-d');
}






$title = 'Statistiques';
$page = 'Statistiques';
$accueil = 'index.php';


include('../template/header.php');
include('../DBConfig.php');
include('../infos.php');

if(isset($_GET['startDate']) and isset($_GET['endDate'])){
	$startDate=str_replace('/','-',$_GET['startDate']);
	$startDate=date('Y-m-d',strtotime($startDate));
	$endDate=str_replace('/','-',$_GET['endDate']);
	$endDate=date('Y-m-d',strtotime($endDate));
	if(isset($_GET['id_caisse'])){
		$id_caisse = $_GET['id_caisse'];
		$sql = "SELECT * FROM table_client_ticket WHERE date > '$startDate' AND date < '$endDate' AND id_caisse = $id_caisse";
	}else{
		$sql = "SELECT * FROM table_client_ticket WHERE date > '$startDate' AND date < '$endDate'";
	}
}
else{
	if(isset($_GET['id_caisse'])){
		$id_caisse = $_GET['id_caisse'];
		$sql = "SELECT * FROM table_client_ticket WHERE date LIKE '$date%' AND id_caisse = $id_caisse ";
	}else{
		$sql = "SELECT * FROM table_client_ticket WHERE date LIKE '$date%' ";
	}
}
$query = $conn->query($sql);




$mois_fr = Array("", "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août",
	"septembre", "octobre", "novembre", "décembre");
list( $annee, $mois, $jour) = explode('-', $date );

$mois = $mois[0] == 0 ? $mois[1] : $mois;
$nameOfDay = date('D', strtotime($dateFormated));
$jours = array('Mon'=>'Lundi','Tue'=>'Mardi','Wed'=>'Mercredi','Thu'=>'Jeudi','Fri'=>'Vendredi','Sat'=>'Samedi','Sun'=>'Dimanche');
$fulldate = $jours[$nameOfDay]. " " . $jour . " ". $mois_fr[$mois] . " " . $annee;


$p_espece_euro = 0;
$p_cb = 0;
$p_cheque_euro = 0;
$ra = 0;
$ca = 0;
$ca_ht = 0;
$total_tva8 = 0; 
$total_tva2 = 0;
$total_tva1 = 0;
$cumul_tva = 0;

while($ticket = $query->fetch_assoc()){
	$total_euro_du = $ticket['total_euro_du'];
	$p_espece_euro += $ticket['p_espece_euro'] > $total_euro_du ? $ticket['p_espece_euro'] - $total_euro_du : $ticket['p_espece_euro'];
	$p_cb += $ticket['p_cb'];
	$p_cheque_euro += $ticket['p_cheque_euro'];
	$ra += $ticket['retourarticle'];
	$ca += $total_euro_du;

}


if(isset($_GET['startDate']) and isset($_GET['endDate'])){
	$startDate=str_replace('/','-',$_GET['startDate']);
	$startDate=date('Y-m-d',strtotime($startDate));
	$endDate=str_replace('/','-',$_GET['endDate']);
	$endDate=date('Y-m-d',strtotime($endDate));
	if(isset($_GET['id_caisse'])){
		$id_caisse = $_GET['id_caisse'];
		$commandes = $conn->query("SELECT * FROM table_client_commandes WHERE  date > '$startDate' AND date < '$endDate' AND id_caisse = $id_caisse");
	}else{
		$commandes = $conn->query("SELECT * FROM table_client_commandes WHERE  date > '$startDate' AND date < '$endDate'");
	}
	
	$fulldate = "Du " . $_GET['startDate'] . " au " . $_GET['endDate'];
}
else{
	if (isset($_GET['id_caisse'])) {
		$id_caisse = $_GET['id_caisse'];
		$commandes = $conn->query("SELECT * FROM table_client_commandes WHERE  date LIKE '$date%' AND id_caisse = $id_caisse ");

	}else{
		$commandes = $conn->query("SELECT * FROM table_client_commandes WHERE  date LIKE '$date%' ");
	}
	
}
// $commandes = $conn->query("SELECT * FROM table_client_commandes WHERE  date LIKE '$date%' ");
while($commande = $commandes->fetch_assoc()){
	$ca_ht += $commande['taux_tva'] != 0.00 ? $commande['pu_euro'] / (1 + $commande['taux_tva'] /100) : $commande['pu_euro'];
	if($commande['taux_tva'] == 8.50){
		$total_tva8 += $commande['pu_euro'] - ($commande['pu_euro'] / (1+8.5 / 100 ));
	}
	else if($commande['taux_tva'] == 2.10){
		$total_tva2 += $commande['pu_euro'] -  ($commande['pu_euro'] / ( 1+ 2.10 / 100 ));
	}
	else if($commande['taux_tva'] == 1.05){
		$total_tva1 += $commande['pu_euro'] - ($commande['pu_euro'] / ( 1+1.05 / 100 ));
	}
	
}

$cumul_tva = $total_tva8 + $total_tva2 + $total_tva1 

// $CA_TTC = $p 
?>
<div class="content-wrapper" style="min-height: 823px;">
	<?php include('../template/info-page.php') ?>
	<div class="content">
		<div class="container">
			<div class="row">
				<div class="col-md-4">
					<h5 class="bg-info" style="font-weight: 600;text-align: center;padding: 5px 0;">FILTRE D'AFFICHAGE : </h5>
					<p style="font-size:18px;text-align: center;text-decoration:underline" ><a href="" id="AllCaisse" >Toutes les caisses</a></p>
					<p style="font-size:18px;text-align: center;text-decoration:underline">Caisse n° 
						<?php 
							for($i = 1 ; $i <= $nbcaisse ; $i++ ){
								echo "<a href='' id='caisse-$i' onclick=sortByCaisse(this.id,event);>".$i."</a> ";
							}
						 ?>
					 </p>
					 <div class="mb-5"></div>
					<h5 class="bg-info" style="font-weight: 800;text-align: center;padding: 5px 0;">CALENDRIER</h5>
					<div class="form-row mb-3">

						<input type="date" style="width: 94%" class="hide-replaced" id="date" value="<?php echo $date; ?>"/>
					</div>

                    <div class="form-group">
                        <h5 class="bg-info" style="font-weight: 800;text-align: center;padding: 5px 0;">Choisir une période </h5>
                        <div class="input-group">
                        	<div class="input-group-prepend">
                        		<span class="input-group-text">
                        			<i class="far fa-calendar-alt"></i>
                        		</span>
                        	</div>
                        	<input type="text" class="form-control float-right" id="reservation">
                        </div>
                    </div>

					
					<div class="mb-5"></div>
					<p style="font-size:24px;text-align: center;text-decoration:underline;font-weight: 600;"><a href="cloture-caisse.php?date=<?php echo $dateFormated; ?>" target="_blank" >Faire la cloture de caisse du <?php echo $dateFormated ?></a></p>
					</div>
					<div class="col-md-6 col-sm-6 offset-md-1">
						<div class="card card-warning" id="stats">
							<div class="card-header">
								<h3 style="width:100%;"> <?php echo isset($_GET['id_caisse']) ? "Caisse " . $_GET['id_caisse'] : "Toutes les caisses" ?> 

									<span style="font-size: 20px;font-weight: normal;text-align: right;"> - <?php echo $fulldate ?></span></h3>


							</div>
							<div class="card-body">
								<div class="row border-bottom mb-3">
									<div class="col-md-8">
										<p class="stats-info">Espèces euro</p>
									</div>

									<div class="col-md-4">
										<p class="stats-montant"><?php echo $p_espece_euro == "" ? "0.00" : $p_espece_euro ?> €</p>
									</div>
								</div>
								<div class="row">
									<div class="col-md-8">
										<p class="stats-info">Chèques € vers.norm</p>
									</div>

									<div class="col-md-4">
										<p class="stats-montant">0.00€</p>
									</div>
								</div>
								<div class="row">
									<div class="col-md-8">
										<p class="stats-info">Chèques € vers.+tard</p>
									</div>

									<div class="col-md-4">
										<p class="stats-montant">0.00€</p>
									</div>
								</div>
								<div class="row border-bottom mb-3">
									<div class="col-md-8">
										<p class="stats-info">Chèques euro total</p>
									</div>

									<div class="col-md-4">
										<p class="stats-montant"><?php echo $p_cheque_euro == "" ? "0.00" : $p_cheque_euro ?> €</p>
									</div>
								</div>
								<div class="row">
									<div class="col-md-8">
										<p class="stats-info">Carte bancaire CB-CA</p>
									</div>

									<div class="col-md-4">
										<p class="stats-montant"><?php echo $p_cb == "" ? "0.00" : $p_cb ?> €</p>
									</div>
								</div>
								<!-- <div class="row border-bottom mb-3">
									<div class="col-md-8">
										<p class="stats-info">Carte bancaire CB-BR</p>
									</div>

									<div class="col-md-4">
										<p class="stats-montant">0.00 €</p>
									</div>
								</div> -->
								<div class="row border-bottom mb-3">
									<div class="col-md-8">
										<p class="stats-info">Carte fidélité</p>
									</div>

									<div class="col-md-4">
										<p class="stats-montant">0.00 €</p>
									</div>
								</div>
							<!-- <div class="row">
								<div class="col-md-8">
									<p class="stats-info">A crédit</p>
								</div>

								<div class="col-md-4">
									<p class="stats-montant">0.00 €</p>
								</div>
							</div> -->
							<!-- <div class="row">
								<div class="col-md-8">
									<p class="stats-info">Chèque déjeuner</p>
								</div>

								<div class="col-md-4 ">
									<p class="stats-montant">0.00 €</p>
								</div>
							</div> -->
							<!-- <div class="row">
								<div class="col-md-8">
									<p class="stats-info">Frais généraux</p>
								</div>

								<div class="col-md-4 ">
									<p class="stats-montant">0.00 €</p>
								</div>
							</div> -->
							<!-- <div class="row">
								<div class="col-md-8">
									<p class="stats-info">Reg fournisseurs</p>
								</div>

								<div class="col-md-4 ">
									<p class="stats-montant">0.00 €</p>
								</div>
							</div>
							<div class="row border-bottom mb-3">
								<div class="col-md-8">
									<p class="stats-info">Avance sur salaire</p>
								</div>

								<div class="col-md-4 ">
									<p class="stats-montant">0.00 €</p>
								</div>
							</div> -->
							<!-- <div class="row">
								<div class="col-md-8">
									<p class="stats-info text-danger">Déconsigne</p>
								</div>

								<div class="col-md-4 ">
									<p class="stats-montant text-danger">0.00 €</p>
								</div>
							</div> -->
							<div class="row">
								<div class="col-md-8">
									<p class="stats-info text-danger">Remise bon client</p>
								</div>

								<div class="col-md-4 ">
									<p class="stats-montant text-danger">0.00 €</p>
								</div>
							</div>
							<div class="row">
								<div class="col-md-8">
									<p class="stats-info text-danger">Retour article</p>
								</div>

								<div class="col-md-4 ">
									<p class="stats-montant text-danger"><?php echo $ra == "" ? "0.00" : $ra ?> €</p>
								</div>
							</div>
							<!-- <div class="row">
								<div class="col-md-8">
									<p class="stats-info text-danger">Démarque</p>
								</div>

								<div class="col-md-4 ">
									<p class="stats-montant text-danger">0.00 €</p>
								</div>
							</div> -->
							<div class="row border-bottom mb-3">
								<div class="col-md-8">
									<p class="stats-info text-danger">Chargement carte fidélité</p>
								</div>

								<div class="col-md-4 ">
									<p class="stats-montant text-danger">0.00 €</p>
								</div>
							</div>

							<div class="row bg-warning border-bottom mb-3 align-middle pt-2 pb-2">
								<div class="col-md-6 ">
									<p class="font-weight-bold m-0" style="font-family: 'Tahoma';font-size: 20px;">Chiffre d'affaire*</p>
								</div>

								<div class="col-md-6 ">
									<p class="text-danger m-0" style="font-family: 'Tahoma';font-size: 24px;font-weight: 800;text-align: right;"><?php echo number_format((float)$ca, 2, '.', ''); ?>  € <span style="font-weight: normal;">TTC</span></p>
								</div>
							</div>
							<div class="row mt-3">
								<div class="col-md-8">
									<p class="stats-info text-danger font-weight-bold">Taxes</p>
								</div>
							</div>
							<div class="row">
								<div class="col-md-8">
									<p class="stats-info">C.A HT</p>
								</div>
								<div class="col-md-4">
									<p class="stats-montant "><?php echo number_format((float)$ca_ht, 2, '.', ''); ?> €</p>
								</div>
							</div>
							<!-- <div class="row">
								<div class="col-md-8">
									<p class="stats-info">Total TVA 0%</p>
								</div>
								<div class="col-md-4">
									<p class="stats-montant "><?php echo number_format((float)$total_tva0,2,'.',''); ?> €</p>
								</div>
							</div> -->
<!--							<div class="row">-->
<!--								<div class="col-md-8">-->
<!--									<p class="stats-info">Total TVA 1.05%</p>-->
<!--								</div>-->
<!--								<div class="col-md-4">-->
<!--									<p class="stats-montant ">--><?php //echo number_format((float)$total_tva1, 2, '.', ''); ?><!-- €</p>-->
<!--								</div>-->
<!--							</div>-->
<!--							<div class="row">-->
<!--								<div class="col-md-8">-->
<!--									<p class="stats-info">Total TVA 2.1%</p>-->
<!--								</div>-->
<!--								<div class="col-md-4">-->
<!--									<p class="stats-montant ">--><?php //echo number_format((float)$total_tva2, 2, '.', ''); ?><!-- €</p>-->
<!--								</div>-->
<!--							</div>-->
<!--							<div class="row">-->
<!--								<div class="col-md-8">-->
<!--									<p class="stats-info">Total TVA 8.5%</p>-->
<!--								</div>-->
<!--								<div class="col-md-4">-->
<!--									<p class="stats-montant ">--><?php //echo number_format((float)$total_tva8, 2, '.', ''); ?><!-- €</p>-->
<!--								</div>-->
<!--							</div>-->
<!--							<div class="row">-->
<!--								<div class="col-md-8">-->
<!--									<p class="stats-info font-weight-bold">CUMUL TVA</p>-->
<!--								</div>-->
<!--								<div class="col-md-4">-->
<!--									<p class="stats-montant ">--><?php //echo number_format((float)$cumul_tva,2,'.',''); ?><!-- €</p>-->
<!--								</div>-->
<!--							</div>-->

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<?php include('../template/footer.php') ?>



<?php include('../template/script.php') ?>
<style>
	body {
		font-family: 'Tahoma';
	}
	.stats-info{
		font-family: 'Tahoma';
		font-size: 18px;
	}
	.stats-montant{
		font-family: 'Tahoma';
		font-size: 18px;
		font-weight: 600;
		text-align: right;
	}
	body > div > div.content-wrapper > div.content > div > div > div.col-md-3.offset-md-1 > div > input.ws-date.ws-inputreplace.hide-replaced.hide-inputbtns.wsshadow-1649417515035.user-success{
		visibility: hidden;
	}

</style>

<script src="../js-webshim/minified/polyfiller.js"></script>
<script type="text/javascript">

	function sortByCaisse(id,event){
		event.preventDefault();
		var id_caisse = $('#'+id).text();
		if (window.location.href.indexOf("startDate") > -1) {
			window.location =  "&id_caisse=" + id_caisse
		}else{
			window.location =  "?id_caisse=" + id_caisse
		}
		
	}
	
	$('#AllCaisse').click(function(e){
		e.preventDefault()
		window.location.href = "statistiques.php";
	});


	
	$('#reservation').daterangepicker({
		locale: {
            format: 'DD/MM/YYYY',
            "applyLabel": "Valider",
        	"cancelLabel": "Annuler",
        	"fromLabel": "De",
        	"toLabel": "A",
            "daysOfWeek": [
            "Dim",
            "Lun",
            "Mar",
            "Mer",
            "Jeu",
            "Ven",
            "Sam"
        ],
        "monthNames": [
            "Janvier",
            "Février",
            "Mars",
            "Avril",
            "Mai",
            "Juin",
            "Juillet",
            "Août",
            "Septembre",
            "Octobre",
            "Novembre",
            "Décembre"
        ],
        }
	}).on('apply.daterangepicker', function (e, picker) {
	    var startDate = picker.startDate.format('DD/MM/YYYY');
	    var endDate = picker.endDate.format('DD/MM/YYYY');
	    $.ajax({
	    	url:"statistiques.php?startDate="+startDate+"&endDate="+endDate,
	    	type:"GET",
	    	success:function(response){
	    		window.location.href="statistiques.php?startDate="+startDate+"&endDate="+endDate
	    		console.log(response)
	    	}
	    })

	})


	webshim.setOptions('forms-ext', {
		replaceUI: 'auto',
		types: 'date',
		date: {
			startView: 2,
			inlinePicker: true,
			classes: 'hide-inputbtns'
		}
	});
	webshim.setOptions('forms', {
		lazyCustomMessages: true
	});
//start polyfilling
webshim.polyfill('forms forms-ext');

//only last example using format display
$(function () {
	$('#date').css('visibility','hidden');
	// $('.format-date').each(function () {
		// var $display = $('#date', this);
		$('#date').on('change', function (e) {
			// e.preventDefault();
			var date = $(this).val();
			console.log(date)
			$.ajax({
				type:'POST',
				data: {
					changeDate: date
				},
				success: function(result){
					$('#stats').html('');
					
					if (window.location.href.indexOf("startDate") > -1) {
						window.location.href = "statistiques.php";
					}else{
						var res  = $(result).find('#stats').appendTo('#stats');
					}
					
				}
			});
            //webshim.format will automatically format date to according to webshim.activeLang or the browsers locale
            // var localizedDate = webshim.format.date($.prop(e.target, 'value'));
            // $display.html(localizedDate);
        });
	// });
});

    // //Date range as a button
    // $('#daterange-btn').daterangepicker(
    //     {
    //         locale:{
    //             "customRangeLabel": "Personnalisée",
    //         },
    //         ranges   : {
    //             'Aujourd\'hui'       : [moment(), moment()],
    //             'Hier'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
    //             'Les 7 derniers jours' : [moment().subtract(6, 'days'), moment()],
    //             'Les 30 derniers jours': [moment().subtract(29, 'days'), moment()],
    //             'Ce mois'  : [moment().startOf('month'), moment().endOf('month')],
    //             'Le mois dernier'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    //         },
    //         startDate: moment().subtract(29, 'days'),
    //         endDate  : moment()
    //     },
    //     function (start, end) {
    //         $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
    //     }
    // )

    //Timepicker
    $('#timepicker').datetimepicker({
        format: 'LT'
    })


</script>


</body>

</html>
