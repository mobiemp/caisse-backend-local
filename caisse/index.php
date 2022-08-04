<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Caisse</title>
	<link rel="stylesheet" href="../lib/dist/plugins/daterangepicker/daterangepicker.css">
	<link rel="stylesheet" href="../lib/dist/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
	<link rel="stylesheet" href="../lib/dist/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css" />
	<link rel="stylesheet" href="../lib/dist/plugins/chart.js/Chart.min.css" />
	<link rel="stylesheet" href="../lib/dist/css/adminlte.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="../template/style.css" />
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-8">
<!--				<form action="../searchProduit.php" id="formSearchProduit" >-->
					<div class="input-group">
						<input type="search" class="form-control form-control-lg" id="searchArticle" placeholder="Type your keywords here">
						<div class="input-group-append">
							<button type="submit" class="btn btn-lg btn-default">
								<i class="fa fa-search"></i>
							</button>
						</div>
					</div>
<!--				</form>-->

				<div id="produits">
					<?php
					$json = file_get_contents('../jsons/panier.json');
					$parsedJson = json_decode($json,true);
					if(count($parsedJson)>0){
						foreach($parsedJson as $article){
							?>
							<div class="callout callout-info">
								<div class="row">
									<div class="col-md-4">
										<p><?php echo $article['titre'] ?></p>
									</div>
									<div class="col-md-2">
										<input type="text" style="width: 50px;" name="quantiteProduit" id="quantiteProduit-<?php echo $article['ref'] ?>"  value="<?php echo $article['qte'] ?>" />
									</div>
									<div class="col-md-1" ><p id="pu_euro-<?php echo $article['ref'] ?>"><?php echo $article['pu_euro'] ?> €</p></div>
									<div class="col-md-1"  id="montantEuro-<?php echo $article['ref'] ?>"><p class="montantEuro"><?php echo $article['pu_euro'] * $article['qte'] ?> €</p></div>
									<div class="col-md-2">
										<input type="text" style="width: 50px;" name="remiseProduit" id="remiseProduit-<?php echo $article['ref'] ?>" 
										value="<?php echo $article['remise'] ?>" /> %
									</div>
									<div class="col-md-1">
										<p id="montantRemise-<?php echo $article['ref'] ?>">
											<?php echo $article['remise'] > 0 ? $article['pu_euro'] * $article['qte'] * ($article['remise'] / 100) . " €" : "0.00€"; ?> 
										</p>
									</div>
								</div>
							</div>

							<?php
						}	
					}

					?>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-text-width"></i>
							TOTAL A PAYER
						</h3>
					</div>

					<div class="card-body">
						<p id="total">
							<?php 
								include('../DBConfig.php');
								$sql = "SELECT pu_euro,qte,remise FROM table_client_panier";
								$query = $conn->query($sql);
								$total = 0;
								if($query->num_rows>0){
									while($row = $query->fetch_assoc()){
										$pu_euro = $row['pu_euro'];
										$qte = $row['qte'];
										$remise = $row['remise'];
										$total += $remise > 0 ? $pu_euro * $qte * ($remise/100) : $pu_euro * $qte;
									}
									echo $total . "€";
								}	
								else{
									echo "0.00€";
								}
							?>
						</p>
					</div>

				</div>
			</div>
		</div>
		
	</div>
</body>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>
<script src="../lib/dist/js/jquery.js"  ></script>
<script src="../lib/dist/plugins/moment/moment.min.js"></script>
<script src="../lib/dist/plugins/daterangepicker/daterangepicker.js"></script>
<script src="../lib/dist/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="../lib/dist/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="../lib/dist/js/adminlte.min.js?v=3.2.0"></script>

<script type="text/javascript">
	var textValues = $('.montantEuro').map((i, el) => el.innerText.trim()).get();
	var total = 0
	textValues.forEach(function (item, index) {
		var montant = item.split(' ')[0]
		montant = parseFloat(montant)
		total += montant
	});
	$('#total').html(total.toFixed(2) + "€")

    $(document).ready(function()
    {

        $('#searchArticle').keydown(function (e) {
            // $('#print-button').css('display','none');
            if (e.which == 13) {
                var ref = $(this).val();
                var qte = 1
                console.log(ref)
                $.ajax({
                    url:"../searchProduit.php",
                    type:"POST",
                    contentType: "application/json",
                    data: JSON.stringify({"search": ref,"session":1}),
                    success:function(data){
                        $(document).ready(function(){
                            $("#produits").load(location.href + " #produits");
                            textValues.forEach(function (item, index) {
                                var montant = item.split(' ')[0]
                                montant = parseFloat(montant)
                                total += montant
                            });
                            $('#total').html(total.toFixed(2) + "€")
                            $('#searchArticle').val("")
                        })

                        // window.location.reload
                    }
                })

            }
        });
        // var barcode="";
        // $('#searchArticle').keydown(function(e)
        // {
        //     var code = (e.keyCode ? e.keyCode : e.which);
        //     if(code==13)// Enter key hit
        //     {
        //         alert(barcode);
        //     }
        //     else if(code==9)// Tab key hit
        //     {
        //         alert(barcode);
        //     }
        //     else
        //     {
        //         barcode=barcode+String.fromCharCode(code);
        //     }
        // });
    });






	$("input[type=text][name=quantiteProduit]").on("keypress", function(e) {
		if (e.which == 13) {
			var newQte = $(this).val()
			var id = $(this).attr('id')
			var ref = id.split('-')[1] 
			$.ajax({
				url:"../panier.php",
				type:"POST",
				contentType: "application/json",
				data: JSON.stringify({"updateQTE": newQte,"refQte":ref,"session":1}),
				success:function(data){
					window.location.reload()
				// var qte = data;
				// var pu_euro = $('#pu_euro-'+ref).text()
				// pu_euro = parseFloat(pu_euro)
				// qte = parseInt(qte)
				// var montant = pu_euro * qte
				// var remise = $('#remiseProduit-'+ref).val()
				// var montantRemise = pu_euro * qte * (remise/100)
				// $('#montantEuro-'+ref).html("<p>"+montant.toFixed(2)+" €</p>")
				// $('#montantRemise-'+ref).html("<p>"+montantRemise.toFixed(2)+" €</p>")
			}
		})
		}

		

	});


	$("input[type=text][name=remiseProduit]").on("keypress", function(e) {
		if (e.which == 13) {
			var newRemise = $(this).val()
			var id = $(this).attr('id')
			var ref = id.split('-')[1] 

			$.ajax({
				url:"../panier.php",
				type:"POST",
				contentType: "application/json",
				data: JSON.stringify({"ajoutRemise": newRemise,"refRemise":ref,"session":1}),
				success:function(data){
					console.log(data)
					window.location.reload()
				// var remise = data;
				// var pu_euro = $('#pu_euro-'+ref).text()
				// var qte = $('#quantiteProduit-'+ref).val()
				// pu_euro = parseFloat(pu_euro)
				// remise = parseInt(remise)
				// var montantRemise = pu_euro * qte * (remise / 100)
				// $('#montantRemise-'+ref).html("<p>"+montantRemise.toFixed(2)+" €</p>")

				// pu_euro = pu_euro - (pu_euro * (remise/100))
				// montantEuro = pu_euro*qte;
				// $('#pu_euro-'+ref).html("<p>"+pu_euro.toFixed(2)+"</p>")
				// $('#montantEuro-'+ref).html("<p>"+montantEuro.toFixed(2)+"</p>")

			}
		})
		}
		

	});
</script>
</html>
