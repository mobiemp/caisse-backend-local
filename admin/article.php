<?php
include('../DBConfig.php');
include('../functions.php');
if(isset($_POST['famille']) && isset($_POST['designation'])){
    $famille = $_POST['famille'];
    $gencode = htmlspecialchars($_POST['gencode']);
    $designation = htmlspecialchars($_POST['designation']);
    $stock_actuel = (int) htmlspecialchars($_POST['stock_actuel']);
    $stock_alerte = (int) htmlspecialchars($_POST['stock_alerte']);
    $codetva = $_POST['codetva'];
    $colisage = $_POST['colisage']  ;
    $quantite = (float) htmlspecialchars($_POST['quantite']);
    $unite = $_POST['unite'];
    $prix_variable = $_POST['prix_variable'];
    $marge = (float) htmlspecialchars($_POST['marge']);
    $mode_prix_3 = (float) htmlspecialchars($_POST['mode_prix_3']);
    $mode_prix_2 = (float) htmlspecialchars($_POST['mode_prix_2']);
    $mode_prix_1_achat = (float) htmlspecialchars($_POST['mode_prix_1_achat']);
    $mode = (int) substr($_POST['mode'],-1);
    $prix = (float) ($mode == 1 ? $mode_prix_1_achat : ($mode == 2 ? $mode_prix_2 : $mode_prix_3 	));
    $promottc = htmlspecialchars($_POST['promottc']);
    $promottc = (float) $promottc;
    $promo_debut = date('Y-m-d',strtotime($_POST['promo_debut']));
    $promo_fin = date('Y-m-d',strtotime($_POST['promo_fin']));
    $datemodif =  date('Y-m-d H:i:s');
    $dateajout = $_POST['dateajout'];

    if ($famille == 0) {
        echo json_encode(array('response' => 0, 'message' => 'Vous devez choisir une famille.' , 'element' => 'famille'));
        exit;
    }
    if($designation == ''){
        echo json_encode(array('response' => 0, 'message' => 'Vous devez indiquer un label article valide.' , 'element' => 'designation'));
        exit;
    }
    if($mode == 3){
        if($mode_prix_3 == 0 or $mode_prix_3 == '' ){
            echo json_encode(array('response'=>3,'message' => 'Vous devez indiquer un prix', 'type' => 'mode_prix_3_achat_ht' ));
            exit;
        }
    }
    if ($mode == 2) {
        if($mode_prix_2 == 0 or $mode_prix_2 == '' ){
            echo json_encode(array('response'=>3,'message' => 'Vous devez indiquer un prix', 'type' => 'mode_prix_2_achat_ht' ));
            exit;
        }
    }
    if ($mode == 1) {
        if($mode_prix_1_achat == 0 or $mode_prix_1_achat == '' ){
            echo json_encode(array('response'=>3,'message' => 'Vous devez indiquer un prix', 'type' => 'mode_prix_1_achat_ht' ));
            exit;
        }
    }



    $id_produit = random_strings(12);

    $sql = "UPDATE table_client_catalogue SET 
    `cath`= $famille, 
    `id` = '$id_produit',
    `ref` = '$gencode',
    `titre` = '$designation',
    `prixttc_euro` = $prix,
    `prixttc_promo_euro` = $promottc,
    `code_tva` = $codetva,
    `promo_debut` = '$promo_debut',
    `promo_fin` = '$promo_fin',
    `choix_mode_prix` = $mode,
    `mode_prix_1_achat_ht` = $mode_prix_1_achat,
    `mode_prix_1_marge` = $marge,
    `mode_prix_2_fixe_ht`=$mode_prix_2,
    `mode_prix_3_fixe_ttc`=$mode_prix_3,
    `dateajout`='$dateajout',
    `datemodif`='$datemodif',
    `accueil` = 0,
    `stock`=$stock_actuel,
    `stock_alerte`=$stock_alerte,
    `unite`=$unite,
    `qte_unite`=$quantite,
    `package`='$colisage',
    `prix_variable`=$prix_variable,
    `img` = NULL,
    `send_web` = 1 WHERE ref = '$gencode' " ;
    $updateArticle = $conn->query($sql);
    if ($updateArticle) {
        $sql = "UPDATE table_client_variable SET modif_serveur_modif_catalogue = '$dateajout' WHERE num = 1";
        $updateSync = $conn->query($sql);
        if($updateSync){
            echo json_encode(array('response' => 1, 'message' => 'Article mise jour dans le catalogue !' ));
            exit;
        }
    }
    else{
        // echo json_encode(array('response' => 2, 'message' => 'Une erreur c\'est produite.' , 'error' => $sql ));
        echo $sql;
        exit;
    }

    exit;
}
if (isset($_GET['gencode'])) {
    $gencode = $_GET['gencode'];
    $sql = "SELECT * from table_client_catalogue WHERE ref = '$gencode' ";
    $query = $conn->query($sql);
    if ($query->num_rows == 1) {
        $resultat = $query->fetch_all(MYSQLI_ASSOC);
        $article = $resultat[0];
        $title = $page = $article['titre'];
    }


    $accueil = 'index.php';
    include('../template/header.php');

    ?>
   <style>
    @media print {
        .content-wrapper, footer {
            display: none;
        }

        @page {
            size: auto;
            margin: 0mm;
        }

        .etiquette {
            display: block;
            text-align: center;
        }

        svg {
            position: absolute;
            bottom: -5px;
            left: 10px;
        }

        .title {
            position: absolute;
            top: 0px;
            left: 14px;
            margin-top: 1px;
            font-weight: 800;
            font-size: 22px;
            text-transform: uppercase;
            font-family: "Tahoma";
            letter-spacing: -2px;
        }

        .price {
            position: absolute;
            top: 25%;
            left:23%;
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
            <p id="titleEtiquette" class="title"></p>
            <p id="colisage"></p>
            <p id="prixEntier" class="price"><span id="prixDecimal"></span></p>
            <svg id="barcode2" jsbarcode-textmargin="1"></svg>
        </div>

    </div>
    <!--Fin etiquette template -->

    <div class="content-wrapper" style="min-height: 823px;">
        <?php include('../template/info-page.php') ?>
        <div class="content">
            <div class="container">
                <div class="col-lg-9 col-md-12 col-sm-12 mx-auto">
                    <!-- form user info -->
                    <form autocomplete="off"  class="form" role="form">
                        <div class="card card-olive">
                            <div class="card-header">
                                <h3 class="mb-0">Identite article</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label form-control-label">Famille</label>
                                    <div class="col-lg-9" id="famille-select">
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
                                                <option class="optionGroup" value="<?php echo $cat['id_categorie']; ?>"
                                                    <?php echo $article['cath'] == $cat['id_categorie'] ? "selected" : "" ?>
                                                ><?php echo utf8_encode($cat['nomcategorie']); ?></option>
                                                <?php
                                                foreach ($child as $subcat) {
                                                    if ($subcat['id_parent'] == $cat['id_categorie']) {
                                                        ?><option value="<?php echo $subcat['id'] ?>"
                                                        <?php echo $article['cath'] == $subcat['id_categorie'] ? "selected" : "" ?>
                                                        >&nbsp;&nbsp;&nbsp;&nbsp;<?php echo utf8_encode($subcat['nomcategorie']); ?></option><?php }
                                                }
                                            }
                                            ?>
                                        </select>
                                        <!-- <span class="text-muted mt-3" style="cursor:pointer;" onclick="toggleFamille()">Creer une famille</span>
                                        <div class="row famille" id="familleBlock" style="margin: 10px 0 0 2px;display: none;">
                                            <input type="text" name="creerFamille" id="inputFamille" style="margin-right:10px" />
                                            <button type="button" class="btn btn-default btn-sm" id="creerFamille" disabled="disabled">Créer une famille</button>
                                        </div> -->


                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label form-control-label">Gencode</label>
                                    <div class="col-lg-3">
                                        <input class="form-control" type="text" name="gencode" id="gencode" value="<?php echo $article['ref'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label form-control-label">Désignation</label>
                                    <div class="col-lg-9">
                                        <input class="form-control" type="text" id="designation" name="designation" value="<?php echo $article['titre'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label form-control-label">Stock</label>
                                    <div class="col-lg-2">
                                        <input class="form-control" type="text"  id="stock_actuel" name="stock_actuel" value="<?php echo $article['stock'] ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label form-control-label">Stock Alerte</label>
                                    <div class="col-lg-2">
                                        <input class="form-control" type="text"  name="stock_alerte" id="stock_alerte" value="<?php echo $article['stock_alerte'] ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label form-control-label">Code TVA</label>
                                    <div class="col-lg-2">
                                        <select class="form-control" size="0" name="code_tva" id="codetva">
                                            <option value="null">Choisir</option>
                                            <option value="0" <?php echo $article['code_tva'] == 0 ? "selected" : "" ?>>0.0 % Exo</option>
                                            <option value="1" <?php echo $article['code_tva'] == 1 ? "selected" : "" ?>>1.5 %</option>
                                            <option value="2" <?php echo $article['code_tva'] == 2 ? "selected" : "" ?>>2.1 %</option>
                                            <option value="8" <?php echo $article['code_tva'] == 8 ? "selected" : "" ?>>8.5 %</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label form-control-label">Colisage</label>
                                    <div class="col-lg-9">
                                        <input class="form-control" type="text" name="package" id="package" value="<?php echo $article['package'] ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label form-control-label">Quantité + Unité</label>
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <input class="form-control" type="number" name="qteUnite" id="quantite" step="0.01" value="<?php echo $article['qte_unite'] ?>">
                                            </div>
                                            <div class="col-lg-6">
                                                <select class="form-control" id="unite" name="unite">
                                                    <option value="0" <?php echo $article['unite'] == 0 ? "selected" : "" ?>>Désactivé</option>
                                                    <option value="1" <?php echo $article['unite'] == 1 ? "selected" : "" ?>>Kg</option>
                                                    <option value="2" <?php echo $article['unite'] == 2 ? "selected" : "" ?>>Litre</option>
                                                    <option value="3" <?php echo $article['unite'] == 3 ? "selected" : "" ?>>Mètre</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label form-control-label">Prix Variable</label>
                                    <div class="col-lg-3">
                                        <select class="form-control" name="prix_variable" id="prix_variable">
                                            <option value="0" <?php echo $article['prix_variable'] == 0 ? "selected" : "" ?>>Désactivé</option>
                                            <option value="1" <?php echo $article['prix_variable'] == 1 ? "selected" : "" ?>>Activé, en euros</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- <div class="form-group row">
											<label class="col-lg-3 col-form-label form-control-label"></label>
											<div class="col-lg-9">
												<input class="btn btn-secondary" type="reset" value="Cancel">
												<input class="btn btn-primary" type="button" value="Save Changes">
											</div>
										</div> -->
                            </div>
                        </div>
                        <div class="margin"></div>

                        <div class="card card-olive">
                            <div class="card-header">
                                <h3 class="mb-0">Fiche prix normal</h3><br>
                                <p style="color:#FFFFFF">Choisissez le mode de calcul du prix de vente normal (hors-promo) de l'article.
                            </div>
                            <div class="card-body">

                                <div class="form-group row" style="margin-top: 30px;">
                                    <div class="col-lg-8">
                                        <div class="form-check">
                                            <input class="form-check-input position-static" type="radio" name="mode" id="mode1" aria-label="..." <?php echo $article['mode_prix_1_achat_ht'] != 0.00 ?  "checked" : ""  ?>>
                                            <label class="form-check-label" for="mode1" style="margin-left: 15px;font-weight: 600;font-size: 17px;">
                                                À partir du prix € HT d'achat fournisseur et du taux de marge
                                            </label>
                                        </div>

                                    </div>
                                    <div class="col-lg-4">
                                        <input type="text"  style="border: 1px solid #ced4da;
													border-radius: 0.25rem;
													box-shadow: inset 0 0 0 transparent;margin: 0 0 10px 0;" name="mode_prix_1_achat_ht" id="mode_prix_1_achat_ht" value="<?php echo $article['mode_prix_1_achat_ht'] ?>"> <span style="font-weight: 600;">€ HT</span>
                                        <input type="text" style="border: 1px solid #ced4da;
													border-radius: 0.25rem;
													box-shadow: inset 0 0 0 transparent" name="mode_prix_1_marge" id="mode_prix_1_marge" value="<?php echo $article['mode_prix_1_marge'] ?>"> <span style="font-weight: 600;"> <span style="font-weight: 600;">%</span>
                                    </div>
                                </div>
                                <hr style="margin-top: 1rem;
											margin-bottom: 1rem;
											border: 0;
											border-top: 1px solid rgba(0, 0, 0, 0.1);" />
                                <div class="form-group row" style="margin-top: 30px;">
                                    <div class="col-lg-8">
                                        <div class="form-check">
                                            <input class="form-check-input position-static" style="border: 1px solid #ced4da;
													border-radius: 0.25rem;
													box-shadow: inset 0 0 0 transparent;margin: 0 0 10px 0;" type="radio" name="mode" id="mode2" aria-label="..." <?php echo $article['mode_prix_2_fixe_ht'] != 0.00 ?  "checked" : ""  ?>>
                                            <label class="form-check-label" for="mode1" style="margin-left: 15px;font-weight: 600;font-size: 17px;">
                                                Fixée en € HT:
                                                <span style="font-weight:normal;font-size: 14px;">Le calcul € TTC se fait automatiquement <br>avec le code TVA</span>
                                            </label>
                                        </div>

                                    </div>
                                    <div class="col-lg-4">
                                        <input type="text"  name="mode_prix_2_achat_ht" id="mode_prix_2_achat_ht" style="border: 1px solid #ced4da;
													border-radius: 0.25rem;
													box-shadow: inset 0 0 0 transparent;margin: 0 0 10px 0;" value="<?php echo $article['mode_prix_2_fixe_ht'] ?>"> <span style="font-weight: 600;">€ HT</span>
                                    </div>
                                </div>
                                <hr style="margin-top: 1rem;
											margin-bottom: 1rem;
											border: 0;
											border-top: 1px solid rgba(0, 0, 0, 0.1);" />
                                <div class="form-group row" style="margin-top: 30px;">
                                    <div class="col-lg-8">
                                        <div class="form-check">
                                            <input class="form-check-input position-static" style="border: 1px solid #ced4da;
													border-radius: 0.25rem;
													box-shadow: inset 0 0 0 transparent;margin: 0 0 10px 0;" type="radio" name="mode" id="mode3" aria-label="..." <?php echo $article['mode_prix_3_fixe_ttc'] != 0.00 ?  "checked" : ""  ?>>
                                            <label class="form-check-label" for="mode1" style="margin-left: 15px;font-weight: 600;font-size: 17px;">
                                                Fixée en € TTC:
                                            </label>
                                        </div>

                                    </div>
                                    <div class="col-lg-4">
                                        <input type="text"  name="mode_prix_3_achat_ht" id="mode_prix_3_achat_ht" value="<?php echo $article['mode_prix_3_fixe_ttc'] ?>"> <span style="font-weight: 600;">€ TTC</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-olive">
                            <div class=" card-header">
                                <h3 class="mb-0">FICHE PRIX PROMO (FALCULTATIF)</h3><br>
                                <p style="color:#ffffff">Remplissez ce formulaire si vous souhaitez programmer un prix promo.<br>Laisser le montant à 0€ pour ne pas programmer de promotion</p>
                            </div>
                            <div class="card-body">

                                <!-- <iframe src="test.php" style="display:none;" name="frame"></iframe>
													<input type="button" onclick="frames['frame'].print()" value="printletter"> -->
                                <div class="row" style="justify-content: center;margin-top: 20px;">
                                    <label class="col-lg-12 col-form-label form-control-label text-center" style="font-size:20px">Prix <span style='color:red;'>PROMO TTC EURO</span> à appliquer:</label>
                                </div>
                                <div class="form-group row" style="justify-content: center;">
                                    <div class="col-lg-4">
                                        <input type="text" style="border: 1px solid #ced4da;
													border-radius: 0.25rem;
													box-shadow: inset 0 0 0 transparent;margin: 0 0 10px 0;" name="promottc" id="promottc" value="<?php echo $article['prixttc_promo_euro'] ?>">
                                        <span style="font-weight: 600;font-size: 18px;color:red">€ TTC</span>
                                    </div>
                                </div>
                                <div class="row text-left">
                                    <div class="col-lg-6" style="    text-align: right;">
                                        <span style="font-weight:600">du</span> <input type="text" style="border: 1px solid #ced4da;
													border-radius: 0.25rem;
													box-shadow: inset 0 0 0 transparent;margin: 0 0 10px 0;" name="promo_debut" id="promo_debut" value="<?php echo $article['promo_debut']; ?>" />
                                    </div>
                                    <div class="col-lg-6">
                                        <span style="font-weight:600">au</span> <input type="text" style="border: 1px solid #ced4da;
													border-radius: 0.25rem;
													box-shadow: inset 0 0 0 transparent;margin: 0 0 10px 0;" name="promo_fin" id="promo_fin" value="<?php echo $article['promo_fin']; ?>" /> <span style="font-weight:600">inclus</span>.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-olive">
                            <div class="card-header" style="text-align: center;">
                                <h3 class="mb-0">IMPRIMER ETIQUETTE</h3>
                            </div>
                            <div class="row" style="padding: 20px 0;margin-left: 20px">
                                <div class="col-lg-6">
                                    <input type="checkbox"  name="print_prix_normal" checked value="0.00" id="prixNormal"> Imprimer l'étiquette <span style="font-weight: 600;">prix normal</span>

                                </div>
                                <div class="col-lg-6">
                                    <input type="checkbox" name="print_prix_promo" id="prixPromo"> Imprimer l'étiquette <span style="font-weight: 600;">prix promo</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-groupt row" style="padding: 30px 0;justify-content: space-around;">
                            <!--                            <div class="col-md-4">-->
                            <!--                                    <i class="fas fa-envelope"></i> Imprimer Étiquettes-->
                            <!--                                </a>-->
                            <!--                            </div>-->
                            <div class="col-md-8 mx-auto">
                                <button type="button" onClick="window.location.href='articles.php';" class="btn btn-dark btn-lg">Annuler</button>
                                <button type="submit" class="btn btn-primary btn-lg" id="editBtn"  name="save">Enregistrer</button>
                                <button type="button" class="btn btn-outline-danger btn-lg" onclick="deleteArticleAdmin('<?php echo $article['ref'] ?>','true')"  >Supprimer le produit</button>
                            </div>
                        </div>
                    </form>
                </div><!-- /form user info -->


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
    <script src="../lib/dist/JsBarcode.ean-upc.min.js"></script>
    <!--    <style type="text/css">-->
    <!--				@media print {-->
    <!--					@page { size: auto;  margin: 0mm; }-->
    <!--					.etiquette {-->
    <!--						display: block;-->
    <!--						size: 30mm 21mm;-->
    <!--						margin: auto;-->
    <!--						padding: 0;-->
    <!--						text-align: center;-->
    <!---->
    <!--					}-->
    <!--					svg{-->
    <!--						position: absolute;-->
    <!--						top: 25%;-->
    <!--						left: 17%;-->
    <!--					}-->
    <!--					h5{-->
    <!--						margin-right: 50px;-->
    <!--						font-weight: 800;-->
    <!--					}-->
    <!--					.margin {-->
    <!--						margin: 50px 0;-->
    <!--					}-->
    <!---->
    <!--					.optionGroup {-->
    <!--						font-weight: bold;-->
    <!--						font-style: italic;-->
    <!--					}-->
    <!---->
    <!--					.optionChild {-->
    <!--						padding-left: 15px;-->
    <!--					}-->
    <!--					#familleBlock {-->
    <!--						display:none;-->
    <!--					}-->
    <!--                }-->
    <!--				</style>-->
    <script type="text/javascript">

        $("#editBtn").click(function(event) {
            event.preventDefault();
            var famille = $('#famille').val();
            var designation = $('#designation').val();
            var gencode = $('#gencode').val();
            var stock_actuel = $('#stock_actuel').val();
            var stock_alerte = $('#stock_alerte').val();
            var codetva = $('#codetva').val();
            var colisage = $('#colisage').val() == "" ? null : $('#colisage').val();
            var quantite = $('#quantite').val();
            var unite = $('#unite').val();
            var prix_variable = $('#prix_variable').val();
            var mode_choice = $('input[type=radio][name=mode]:checked').attr('id');
            var marge = $('#mode_prix_1_marge').val();
            var mode_prix_3 = $('#mode_prix_3_achat_ht').val();
            var mode_prix_2 = $('#mode_prix_2_achat_ht').val();
            var mode_prix_1_achat = $('#mode_prix_1_achat_ht').val();
            var promottc = $('#promottc').val();
            var promo_debut = $('#promo_debut').val();
            var promo_fin = $('#promo_fin').val();
            var dateajout = '<?php echo $article['dateajout'] ?>'
            var submitData = {
                promo_debut:promo_debut,
                promo_fin:promo_fin,
                famille:famille,
                designation:designation,
                gencode:gencode,
                stock_actuel:stock_actuel,
                stock_alerte:stock_alerte,
                codetva:codetva,
                colisage:colisage,
                quantite:quantite,
                unite:unite,
                prix_variable:prix_variable,
                marge:marge,
                mode:mode_choice,
                mode_prix_3:mode_prix_3,
                mode_prix_2:mode_prix_2,
                mode_prix_1_achat:mode_prix_1_achat,
                promottc:promottc,
                dateajout:dateajout
            };
            $.ajax({
                url:'article.php',
                type: "POST",
                data: submitData,
                success: function(result,statusText,jqXHR) {
                    console.log(result);
                    var response = JSON.parse(result);

                    if (response.response === 0 && response.element === 'famille') {
                        $('#famille').css('border-color','red');
                        document.getElementById("famille").scrollIntoView();
                        $("#famille").addClass("swalDefaultSuccess");
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        })
                    }
                    else if(response.response === 0 && response.element === 'designation'){
                        $('#designation').css('border-color','red');
                        document.getElementById("designation").scrollIntoView();
                        $("#designation").addClass("swalDefaultSuccess");
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        })
                    }
                    else if(response.response === 3){
                        var type = response.type;
                        $('#'+type).css('border-color','red');
                        document.getElementById(type).scrollIntoView();
                        $('#'+type).addClass('swalDefaultSuccess');
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        })
                    }
                    else if(response.response === 1){
                        // $('#'+type).addClass('swalDefaultSuccess');

                        var prix = (mode_prix_3 > 0 ? mode_prix_3 : (mode_prix_2 > 0 ? mode_prix_2 : (mode_prix_1_achat > 0 ?  mode_prix_1_achat : 0 )))
                        var package = $('#package').val()
                        var prix = parseFloat(prix);
                        if($('#prixNormal').is(':checked')){
                            imprimeEtiquettes(gencode,designation,prix,package)
                        }
                        else if($('#prixPromo').is(':checked')){
                            promottc = parseFloat(promottc)
                            imprimeEtiquettes(gencode,designation,promottc,package)

                        }
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        })
                        // window.setTimeout( function(){
                        // 	window.location = "articles.php";
                        // }, 1000 );
                    }
                    else {
                        $("#creerFamille").addClass("swalDefaultError");
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        })
                    }

                }
            });
        });

        // JsBarcode("#barcode2", "9780199532179", {
        // 	format:"EAN13",
        // 	width:1.3,
        // 	height:30,
        // 	displayValue:true,
        // 	fontSize:13,
        // });
        // window.print();
        function toggleFamille() {
            var x = document.getElementById("familleBlock");
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });



        $('.famille input').on('keyup', function() {
            let empty = false;

            $('').each(function() {
                empty = $(this).val().length == 0;
            });

            if (empty)
                $('.famille button').attr('disabled', 'disabled');
            else
                $('.famille button').attr('disabled', false);
        });

    </script>
<?php } ?>
