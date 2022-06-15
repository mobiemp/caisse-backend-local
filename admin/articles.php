<?php
include('../DBConfig.php');
include('../functions.php');
if (isset($_POST['entrerBarcode'])) {
	$codebarre = $_POST['entrerBarcode'];
	$check = checkIfBarcodeExist($codebarre, $conn, 'entrer');
	if ($check === 'false') {
		echo $check;
	} else {
		echo 0;
	}
	exit;
}



$title = 'Gestions des articles';
$page = 'Gestion des articles';
$accueil = 'index.php';
include('../template/header.php');
include('../codebarre/barcode.php');


?>

<!--Etiquette template-->
<div class="etiquette" style="width: 500px;height: 300px;margin: auto">
	<div>
		<p id="titleEtiquette" class="title"></p>
		<p id="prixEntier" class="price"><span id="prixDecimal"></span></p>
		<svg id="barcode2" jsbarcode-textmargin="1"></svg>
	</div>

</div>
<!--Fin etiquette template -->

<div class="content-wrapper" style="min-height: 823px;">
	<?php include('../template/info-page.php') ?>
	<div class="content">
		<div class="container">
			<div class="row ">
				<!-- <a class="btn btn-app float-right" href="" >
						<i class="fas fa-barcode"></i>
						ENTRER CODE FOURNISSEUR
					</a> -->
				<div class="col-md-6">
					<form action="searchArticle.php">
						<div class="input-group">
							<input type="search" class="form-control form-control-lg" id="searchArticle" placeholder="Rechercher un article">
							<div class="input-group-append">
								<button type="submit" class="btn btn-lg btn-default">
									<i class="fa fa-search"></i>
								</button>
							</div>
						</div>
					</form>
				</div>
				<div class="col-md-3 offset-3">
					<a class="btn btn-app float-right" href="" data-toggle="modal" data-target="#entrerGencodeModal">
						<i class="fas fa-barcode"></i>
						ENTRER GENCODE
					</a>
					<a class="btn btn-app float-right" id="creer" href="" onclick='creategencode(event,this.id)'>
						<i class="fas fa-barcode"></i>
						CREER GENCODE
					</a>
				</div>
			</div>
			<div class="row">
				<div class="col-12">

					<?php
					$sql = "SELECT * FROM table_client_catalogue ORDER BY datemodif DESC LIMIT 15";
					$catalogue = mysqli_query($conn, $sql);
					$nbarticle = $catalogue->num_rows;
					?>
					<div class="card">
						<div class="card-header">
							<?php if ($nbarticle > 0) { ?>
								<h3 class="card-title" style="font-weight: 600;">Voici les <span style="font-weight: 600;"><?php echo $nbarticle ?> derniers article(s) modifié(s)</span></h3>
							<?php	} else { ?>
								<h3 class="card-title" style="font-weight: 600;">Aucun article à été modifié.</h3>
							<?php	} ?>
						</div>

						<div class="card-body">
							<div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
								<div class="row">
									<div class="col-sm-12 col-md-6">
										<!-- <div class="dt-buttons btn-group flex-wrap">
											<button class="btn btn-secondary buttons-copy buttons-html5" tabindex="0" aria-controls="example1" type="button"><span>Copier</span></button>
											<button class="btn btn-secondary buttons-csv buttons-html5" tabindex="0" aria-controls="example1" type="button"><span>CSV</span></button>
											<button class="btn btn-secondary buttons-excel buttons-html5" tabindex="0" aria-controls="example1" type="button"><span>Excel</span></button>
											<button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="example1" type="button"><span>PDF</span></button>
											<button class="btn btn-secondary buttons-print" tabindex="0" aria-controls="example1" type="button"><span>Imprimer</span></button>
											<div class="btn-group">
												<button class="btn btn-secondary buttons-collection dropdown-toggle buttons-colvis" tabindex="0" aria-controls="example1" type="button" aria-haspopup="true">
													<span>Column visibility</span>
													<span class="dt-down-arrow"></span>
												</button>
											</div> 
										</div> -->
									</div>
									<!-- <div class="col-sm-12 col-md-6">
										<div id="example1_filter" class="dataTables_filter">
											<label>Rechercher:<input type="search" class="form-control form-control-sm" placeholder="" aria-controls="example1"></label>
										</div>
									</div> -->
								</div>
								<div class="row">
									<div class="col-sm-12">
										<table id="example1" class="table table-bordered table-striped dataTable dtr-inline" aria-describedby="example1_info">
											<thead>
												<tr>
													<a>
														<th class="sorting sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">Gencode</th>
													</a><a>
														<a>
															<th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Désignation</th>
														</a>
														<a>
															<th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending">Stock</th>
														</a>
														<a>
															<th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Achat € HT</th>
														</a>
														<a>
															<th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Vente € TTC</th>
														</a>
														<a>
															<th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Promo € TTC</th>
														</a>
														<a>
															<th rowspan="1" colspan="1" st>Étiquettes</th>
														</a>
												</tr>
											</thead>
											<?php

											if ($catalogue->num_rows > 0) {
												while ($row = $catalogue->fetch_assoc()) {
													$tva = ($row['code_tva'] == 8 ? 8.5 : ($row['code_tva'] == 2  ? 2.1 : ($row['code_tva'] == 1 ? 1.05 : 0)));
											?>
													<tr class="odd">
														<td class="dtr-control sorting_1" tabindex="0"><?php echo $row['ref'] ?></td>
														<td><a href="article.php?gencode=<?php echo $row['ref'] ?>"><?php echo $row['titre'] ?></a></td>
														<td><?php echo $row['stock'] ?></td>
														<td><?php echo round($row['prixttc_euro'] - ($row['prixttc_euro'] * ($tva / 100)), 2) ?></td>
														<td><?php echo $row['prixttc_euro'] ?></td>
														<td><?php echo $row['prixttc_promo_euro'] ?></td>
														<td style="text-align: center;">
															<a href="#" onclick="imprimeEtiquettes('<?php echo $row['ref'] ?>','<?php echo $row['titre'] ?>',<?php echo $row['prixttc_euro'] ?>);return false;">
																<i class="fa fa-print"></i>
															</a>

														</td>
													</tr>
											<?php
												}
											}
											?>
											</tbody>
											<tfoot>
												<tr>
													<th rowspan="1" colspan="1">Gencode</th>
													<th rowspan="1" colspan="1">Désignation</th>
													<th rowspan="1" colspan="1">Stock(s)</th>
													<th rowspan="1" colspan="1">Achat € HT</th>
													<th rowspan="1" colspan="1">Vente € TTC</th>
													<th rowspan="1" colspan="1">Promo € TTC</th>
													<th rowspan="1" colspan="1">Étiquettes</th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
							</div>
						</div>

					</div>

					<hr style="margin: 50px 0">
					<!-- TABLE ARTICLE AJOUTES -->
					<?php
					$sql = "SELECT * FROM table_client_catalogue ORDER BY dateajout DESC LIMIT 15";
					$catalogue = mysqli_query($conn, $sql);
					$nbarticle = $catalogue->num_rows;
					?>
					<div class="card">
						<div class="card-header">
							<?php if ($nbarticle > 0) { ?>
								<h3 class="card-title" style="font-weight: 600;">Voici les <span style="font-weight: 600;"><?php echo $nbarticle ?> derniers article(s) ajouté(s)</span></h3>
							<?php	} else { ?>
								<h3 class="card-title" style="font-weight: 600;">Aucun article trouvés </h3>
							<?php	} ?>
						</div>

						<div class="card-body">
							<div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
								<div class="row">
									<!-- <div class="col-sm-12 col-md-6">
										<div class="dt-buttons btn-group flex-wrap">
											<button class="btn btn-secondary buttons-copy buttons-html5" tabindex="0" aria-controls="example1" type="button"><span>Copier</span></button>
											<button class="btn btn-secondary buttons-csv buttons-html5" tabindex="0" aria-controls="example1" type="button"><span>CSV</span></button>
											<button class="btn btn-secondary buttons-excel buttons-html5" tabindex="0" aria-controls="example1" type="button"><span>Excel</span></button>
											<button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="example1" type="button"><span>PDF</span></button>
											<button class="btn btn-secondary buttons-print" tabindex="0" aria-controls="example1" type="button"><span>Imprimer</span></button>
											<div class="btn-group">
												<button class="btn btn-secondary buttons-collection dropdown-toggle buttons-colvis" tabindex="0" aria-controls="example1" type="button" aria-haspopup="true">
													<span>Column visibility</span>
													<span class="dt-down-arrow"></span>
												</button>
											</div> 
										</div>
									</div> -->
									<!-- <div class="col-sm-12 col-md-6">
										<div id="example1_filter" class="dataTables_filter">
											<label>Rechercher:<input type="search" class="form-control form-control-sm" placeholder="" aria-controls="example1"></label>
										</div>
									</div> -->
								</div>
								<div class="row">
									<div class="col-sm-12">
										<table id="example1" class="table table-bordered table-striped dataTable dtr-inline" aria-describedby="example1_info">
											<thead>
												<tr>
													<th class="sorting sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">Gencode</th>
													<th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Désignation</th>
													<th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending">Stock</th>
													<th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Achat € HT</th>
													<th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Vente € TTC</th>
													<th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Promo € TTC</th>
												</tr>
											</thead>
											<?php

											if ($catalogue->num_rows > 0) {
												while ($row = $catalogue->fetch_assoc()) {
													$tva = ($row['code_tva'] == 8 ? 8.5 : ($row['code_tva'] == 2  ? 2.1 : ($row['code_tva'] == 1 ? 1.05 : 0)));
											?>
													<tr class="odd">
														<td class="dtr-control sorting_1" tabindex="0"><?php echo $row['ref'] ?></td>
														<td><a href="article.php?gencode=<?php echo $row['ref'] ?>"><?php echo $row['titre'] ?></a></td>
														<td><?php echo $row['stock'] ?></td>
														<td><?php echo round($row['prixttc_euro'] - ($row['prixttc_euro'] * ($tva / 100)), 2) ?></td>
														<td><?php echo $row['prixttc_euro'] ?></td>
														<td><?php echo $row['prixttc_promo_euro'] ?></td>
													</tr>
											<?php
												}
											}
											?>
											</tbody>
											<tfoot>
												<tr>
													<th rowspan="1" colspan="1">Gencode</th>
													<th rowspan="1" colspan="1">Désignation</th>
													<th rowspan="1" colspan="1">Stock(s)</th>
													<th rowspan="1" colspan="1">Achat € HT</th>
													<th rowspan="1" colspan="1">Vente € TTC</th>
													<th rowspan="1" colspan="1">Promo € TTC</th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
							</div>
						</div>

					</div>
					<!-- FIN ARTICLE AJOUTES -->
					<hr style="margin: 50px 0">
					<!-- DEBUT ARTICLE PROMO -->
					<?php
					$sql = "SELECT * FROM table_client_catalogue WHERE prixttc_promo_euro > 0 ORDER BY datemodif DESC LIMIT 15";
					$catalogue = mysqli_query($conn, $sql);
					$nbarticle = $catalogue->num_rows;
					?>
					<div class="card">
						<div class="card-header">
							<?php if ($nbarticle > 0) { ?>
								<h3 class="card-title" style="font-weight: 600;">Voici les <span style="font-weight: 600;"><?php echo $nbarticle ?> article(s) en promotion actuellement:</span></h3>
							<?php	} else { ?>
								<h3 class="card-title" style="font-weight: 600;">Il n'y a auncun article en promo actuellement</h3>
							<?php	} ?>
						</div>

						<div class="card-body">
							<div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
								<div class="row">
									<!-- <div class="col-sm-12 col-md-6">
										<div class="dt-buttons btn-group flex-wrap">
											<button class="btn btn-secondary buttons-copy buttons-html5" tabindex="0" aria-controls="example1" type="button"><span>Copier</span></button>
											<button class="btn btn-secondary buttons-csv buttons-html5" tabindex="0" aria-controls="example1" type="button"><span>CSV</span></button>
											<button class="btn btn-secondary buttons-excel buttons-html5" tabindex="0" aria-controls="example1" type="button"><span>Excel</span></button>
											<button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="example1" type="button"><span>PDF</span></button>
											<button class="btn btn-secondary buttons-print" tabindex="0" aria-controls="example1" type="button"><span>Imprimer</span></button>
											<div class="btn-group">
												<button class="btn btn-secondary buttons-collection dropdown-toggle buttons-colvis" tabindex="0" aria-controls="example1" type="button" aria-haspopup="true">
													<span>Column visibility</span>
													<span class="dt-down-arrow"></span>
												</button>
											</div> 
										</div>
									</div> -->
									<!-- <div class="col-sm-12 col-md-6">
										<div id="example1_filter" class="dataTables_filter">
											<label>Rechercher:<input type="search" class="form-control form-control-sm" placeholder="" aria-controls="example1"></label>
										</div>
									</div> -->
								</div>
								<div class="row">
									<div class="col-sm-12">
										<table id="example1" class="table table-bordered table-striped dataTable dtr-inline" aria-describedby="example1_info">
											<thead>
												<tr>
													<th class="sorting sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">Gencode</th>
													<th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Désignation</th>
													<th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending">Stock</th>
													<th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Achat € HT</th>
													<th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Vente € TTC</th>
													<th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Promo € TTC</th>
												</tr>
											</thead>
											<?php

											if ($catalogue->num_rows > 0) {
												while ($row = $catalogue->fetch_assoc()) {
													$tva = ($row['code_tva'] == 8 ? 8.5 : ($row['code_tva'] == 2  ? 2.1 : ($row['code_tva'] == 1 ? 1.05 : 0)));
											?>
													<tr class="odd">
														<td class="dtr-control sorting_1" tabindex="0"><?php echo $row['ref'] ?></td>
														<td><a href="article.php?gencode=<?php echo $row['ref'] ?>"><?php echo $row['titre'] ?></a></td>
														<td><?php echo $row['stock'] ?></td>
														<td><?php echo round($row['prixttc_euro'] - ($row['prixttc_euro'] * ($tva / 100)), 2) ?></td>
														<td><?php echo $row['prixttc_euro'] ?></td>
														<td><?php echo $row['prixttc_promo_euro'] ?></td>
													</tr>
											<?php
												}
											}
											?>
											</tbody>
											<tfoot>
												<tr>
													<th rowspan="1" colspan="1">Gencode</th>
													<th rowspan="1" colspan="1">Désignation</th>
													<th rowspan="1" colspan="1">Stock(s)</th>
													<th rowspan="1" colspan="1">Achat € HT</th>
													<th rowspan="1" colspan="1">Vente € TTC</th>
													<th rowspan="1" colspan="1">Promo € TTC</th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
							</div>
						</div>

					</div>
					<!-- FIN ARTICLE PROMO -->
				</div>

			</div>

		</div>
	</div>
</div>


<!-- CREER GENCODE Modal -->
<div class="modal fade" id="entrerGencodeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Entrer un gencode</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="text" name="entrerBarcode" id="entrerBarcode" />
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
				<button type="button" class="btn btn-primary" onclick='creategencode(event,"entrer")'>Confirmer</button>
			</div>
		</div>
	</div>
</div>


<?php include('../template/footer.php') ?>
<?php include('../template/script.php') ?>

<script type="text/javascript">
	function creategencode(event, action) {
		event.preventDefault();
		if (action === 'creer') {
			<?php
			$barcode = EAN13::create();
			$existGencode = checkIfBarcodeExist($barcode, $conn, 'creer');
			?>
			let valid = '<?php echo $existGencode; ?>';
			let barcode = '<?php echo $barcode;	?>';
			if (valid === 'false') {
				window.location.href = "http://localhost/caisse-backend/admin/articleAjout.php?action=" + action + "creer&gencode=" + barcode
			} else {
				alert('Ce gencode existe déja dans la base de donnée.\nVous ne pouvez pas créer deux fois le même gencode.');
			}

		} else if (action === 'entrer') {
			var barcode = $('#entrerBarcode').val();
			$.ajax({
				type: 'POST',
				data: {
					entrerBarcode: barcode
				},
				success: function(res) {
					console.log(res)
					if (res === 'false') {
						window.location.href = "http://localhost/caisse-backend/admin/articleAjout.php?action=" + action + "entrer&gencode=" + barcode
					} else {
						alert('Ce gencode existe déja dans la base de donnée.\nVous ne pouvez pas créer deux fois le même gencode.');
					}
				}

			})

		}
	}
	// var searchRequest = null;
	// var minlength = 4;
	// $('#searchArticle').keyup(function() {
	// 	var that = this,
	// 		value = $(this).val();
	// 	if (value.length >= minlength) {
	// 		if (searchRequest != null)
	// 			searchRequest.abort();
	// 		searchRequest = $.ajax({
	// 			type: "POST",
	// 			data: {
	// 				'search_keyword': value
	// 			},
	// 			dataType: "json",
	// 			success: function(response) {
	// 				console.log(response)
	// 				if (value == $(that).val()) {
	// 					$('.search-title').append(response.titre)
	// 				}
	// 			}
	// 		});
	// 	}
	// })
</script>
</body>

</html>