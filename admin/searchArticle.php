<?php

include('../DBConfig.php');
include('../functions.php');

if (isset($_POST['searchArticle'])) {
    $search = $_POST['searchArticle'];
    $sql = "SELECT * FROM table_client_catalogue WHERE titre like '%$search%' OR ref like '%$search%'";
    $query = $conn->query($sql);
    $nbarticle = $query->num_rows;
}

$title = $page = 'Rechercher un article';
$accueil = 'index.php';
include('../template/header.php');


?>
<div class="content-wrapper" style="min-height: 823px;">
    <?php include('../template/info-page.php') ?>
    <div class="content">
        <div class="container">

            <div class="row">
                <div class="col-md-6">
                    <form action="" method="POST">
                        <div class="input-group">
                            <input type="search" class="form-control form-control-lg" id="searchArticle" name="searchArticle" placeholder="Rechercher un article">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-lg btn-default">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-2 ml-auto">
                    <button type="button" class="btn btn-block btn-dark" onclick="window.location.href='articles.php' ">Retour</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header">
                            <?php if (isset($nbarticle) && $nbarticle > 0) { ?>
                                <h3 class="card-title" style="font-weight: 600;"><span style="font-weight: 600;"><?php echo $nbarticle ?> résultats trouvés.</span></h3>
                            <?php    } else { ?>
                                <h3 class="card-title" style="font-weight: 600;">Aucun articles trouvé.</h3>
                            <?php    } ?>
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

                                                if (isset($nbarticle) && $nbarticle > 0) {
                                                    while ($row = $query->fetch_assoc()) {
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
                    
                </div>
            </div>



        </div>
    </div>
</div>

<?php include('../template/footer.php') ?>



<?php include('../template/script.php') ?>