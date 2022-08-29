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
    <style>
        @media print {
            .content-wrapper,footer{
                display: none;
            }
            @page { size: auto;  margin: 0mm; }
            .etiquette {
                display: block;
                text-align: center;
            }
            svg{
                position: absolute;
                bottom: -10px;
                left:10px;
            }
            .title{
                position: absolute;
                top: 0px;
                left: 14px;
                margin-top:1px;
                font-weight: 800;
                font-size: 22px;
                text-transform: uppercase;
                font-family: "Tahoma";
                letter-spacing: -2px;
            }
            .price {
            position: absolute;
            top: 25%;
            left:25%;
            margin-top: 0px;
            margin-left: 0px;
            font-weight: 800;
            font-size: 80px;

            font-family: "PT Sans monospace";
            letter-spacing: -5px;
        }

        .price span {
            font-size: 30px;
            letter-spacing: -2px;
            font-weight: 600;
            font-family: "Open Sans monospace";
        }


        }
    </style>
    <!--Etiquette template-->
    <div class="etiquette" style="width: 200px;height: 140px;margin: auto">
        <div>
            <p id="titleEtiquette" class="title"><br></p>

            <p id="prixEntier" class="price"><span id="prixDecimal"></span></p>
            <svg id="barcode2" jsbarcode-textmargin="1"></svg>
        </div>

    </div>
    <!--Fin etiquette template -->
    <div class="content-wrapper" style="min-height: 823px;">
        <?php include('../template/info-page.php') ?>
        <div class="content">

            <div class="modal fade" id="modal-categorie" style="display: none;" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Déplacer dans la catégorie</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <select class="form-control" id="famille" size="0" name="famille">
                                    <option value="0">Choisir la famille</option>
                                    <?php
                                    $sql = 'SELECT * FROM table_client_categorie ORDER BY id ASC';
                                    $familles = $conn->query($sql);
                                    $nbligne = $familles->num_rows;
                                    $parent = [];
                                    $child = [];
                                    if ($nbligne > 0) {
                                        while ($famille = $familles->fetch_assoc()) {
                                            if ($famille['id_parent'] == 0) {
                                                $parent[] = $famille;
                                            } else {
                                                $child[] = $famille;
                                            }
                                        }
                                    }
                                    foreach ($parent as $cat) {
                                        ?>
                                        <option class="optionGroup" value="<?php echo $cat['id_categorie']; ?>"><?php echo htmlspecialchars($cat['nomcategorie'], ENT_QUOTES, 'UTF-8'); ?></option>
                                        <?php
                                        foreach ($child as $subcat) {
                                            if ($subcat['id_parent'] == $cat['id_categorie']) {
                                                ?><option value="<?php echo	$subcat['id'] ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo  htmlspecialchars($subcat['nomcategorie'], ENT_QUOTES, 'UTF-8');  ?></option><?php }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-primary" id="btnAddGroupCat">Confirmer</button>
                        </div>
                    </div>

                </div>
            </div>

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
                <div class="col-md-3">

                    <div class="form-group">
                        <label>Action de groupe</label>
                        <select id="choixAction" class="form-control">
                            <option value="add_cat">Déplacer dans la catégorie</option>
                            <option value="supp">Supprimer</option>
                            <!--                            <option>option 3</option>-->
                            <!--                            <option>option 4</option>-->
                            <!--                            <option>option 5</option>-->
                        </select>
                    </div>


                </div>
                <div class="col-md-3">
                    <input type="submit" style="margin-top: 27px;" name="btnAction" id="btnAction" class="btn btn-lg btn-success" value="Valider" />
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
                                                        <th class="sorting sorting_asc" tabindex="0" aria-controls="example1"
                                                            rowspan="1" colspan="1" aria-sort="ascending"
                                                            aria-label="Rendering engine: activate to sort column descending">
                                                            <input type="checkbox" name="select-all-search" id="select-all-search" />
                                                        </th>
                                                    </a>
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
                                                        <a>
                                                            <th class="sorting sorting_asc" tabindex="0"
                                                                aria-controls="example1" rowspan="1" colspan="1"
                                                                aria-sort="ascending"
                                                                aria-label="Rendering engine: activate to sort column descending">
                                                                Action
                                                            </th>
                                                        </a>
                                                </tr>
                                                </thead>
                                                <?php

                                                if (isset($nbarticle) && $nbarticle > 0) {
                                                    while ($row = $query->fetch_assoc()) {
                                                        $tva = ($row['code_tva'] == 8 ? 8.5 : ($row['code_tva'] == 2  ? 2.1 : ($row['code_tva'] == 1 ? 1.05 : 0)));
                                                        ?>
                                                        <tr class="odd">
                                                            <td class="center"><input type="checkbox" name="produitCheckbox[]"
                                                                                      class="form-check-input" id="<?php echo $row['num'] ?>"
                                                            </td>
                                                            <td class="dtr-control sorting_1" tabindex="0"><?php echo $row['ref'] ?></td>
                                                            <td><a href="article.php?gencode=<?php echo $row['ref'] ?>"><?php echo $row['titre'] ?></a></td>
                                                            <td><?php echo $row['stock'] ?></td>
                                                            <td><?php echo round($row['prixttc_euro'] - ($row['prixttc_euro'] * ($tva / 100)), 2) ?></td>
                                                            <td><?php echo $row['prixttc_euro'] ?></td>
                                                            <td><?php echo $row['prixttc_promo_euro'] ?></td>
                                                            <td style="text-align: center;">
                                                                <a href="#" onclick="imprimeEtiquettes('<?php echo $row['ref'] ?>','<?php echo $row['titre'] ?>',<?php echo $row['prixttc_euro'] ?>,'<?php echo $row['package'] ?>');return false;">
                                                                    <i class="fa fa-print"></i>
                                                                </a>

                                                            </td>
                                                            <td>
                                                                <i class="fa fa-trash" style="color:red;cursor:pointer;"
                                                                   onclick="deleteArticleAdmin('<?php echo $row['ref'] ?>')"
                                                                ></i></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <th rowspan="1" colspan="1">#</th>
                                                    <th rowspan="1" colspan="1">Gencode</th>
                                                    <th rowspan="1" colspan="1">Désignation</th>
                                                    <th rowspan="1" colspan="1">Stock(s)</th>
                                                    <th rowspan="1" colspan="1">Achat € HT</th>
                                                    <th rowspan="1" colspan="1">Vente € TTC</th>
                                                    <th rowspan="1" colspan="1">Promo € TTC</th>
                                                    <th rowspan="1" colspan="1">Étiquettes</th>
                                                    <th rowspan="1" colspan="1">Action</th>
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

<?php include('../template/footer.php') ?>



<?php include('../template/script.php') ?>
