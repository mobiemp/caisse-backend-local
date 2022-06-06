<?php

if (isset($_GET['date'])) {
    $date = $_GET['date'];
}
$title = 'Cloture de caisse';
$page = 'Cloture de caisse';
$accueil = '../index.php';
include('../template/header.php');
include('../DBConfig.php');
?>

<div class="content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h2 style="text-align:center;padding:50px 0;">Calcul de la somme restant en espèce le <?php echo $date ?></h2>

                <div class="card card-danger" id="stats">
                    <div class="card-header">
                        <h3 style="width:100%;text-align: center;">Encaissé hors de la webcaisse</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-3 ">
                            <div class="col-4 text-right text-danger"><label for="cb-ca" style="font-size:17px;">Par carte bancaire : </label></div>
                            <div class="col-6"><input type="number" name="cb-bancaire"/><span class="euro">€</span></div>
                        </div>
                        <div class="form-row" style="margin-bottom: 20px;">
                            <div class="col-4 text-right text-danger"><label for="cb-ca" style="font-size:17px;" >Par chèques : </label></div>
                            <div class="col-6"><input type="number" name="cheque"/><span class="euro">€</span></div>
                        </div>

                        <div class="form-row">
                            <div class="col-4 text-right"><label for="sous-total" style="font-size:22px;font-weight: 800" >SOUS TOTAL : </label></div>
                            <div class="col-6"><input type="number" name="sous-total"/><span class="euro">€</span></div>
                        </div>
                    </div>
                </div>

                <div class="card card-navy" id="stats">
                    <div class="card-header">
                        <h3 style="width:100%;text-align: center;">Dans le systeme webcaisse</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-3 ">
                            <div class="col-4 text-right text-navy"><label for="cb-ca" style="font-size:17px;">Espèces euro : </label></div>
                            <div class="col-6"><input type="number" name="espece"/><span class="euro">€</span></div>
                        </div>
                        <div class="form-row" style="margin-bottom: 20px;">
                            <div class="col-4 text-right text-danger"><label for="cb-ca" style="font-size:19px;font-weight: 800" >Sous-total espèces : </label></div>
                            <div class="col-6"><input type="number" name="sous-total-espece"/><span class="euro">€</span></div>
                        </div>

                        <div class="form-row">
                            <div class="col-4 text-right text-navy"><label for="cheque-euro"  >Chèque euro : </label></div>
                            <div class="col-6"><input type="number" name="cheque-euro"/><span class="euro">€</span></div>
                        </div>
                        <div class="form-row">
                            <div class="col-4 text-right text-navy"><label for="cb-ca"  >Carte bancaire CB-CA: </label></div>
                            <div class="col-6"><input type="number" name="cb-ca-2"/><span class="euro">€</span></div>
                        </div>


                    </div>
                </div>

                <div class="card card-secondary" id="stats">
                    <div class="card-header">
                        <h3 style="width:100%;text-align: center;">Résultats</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-3 ">
                            <div class="col-4 text-right text-danger"><label for="cb-ca" style="font-size:22px;font-weight: 800">Espèces à trouver : </label></div>
                            <div class="col-6"><input type="number" name="espece"/><span class="euro">€</span></div>
                        </div>
                    </div>
                </div>

                <div class="form-row" style="padding:20px;0">
                    <div class="col-6"><button type="button" class="btn btn-primary btn-block"><i class="fa fa-print"></i> Imprimer la page</button></div>
                    <div class="col-6"><button type="button" class="btn btn-success btn-block"> Cloturer la caisse</button></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../template/footer.php') ?>



<?php include('../template/script.php') ?>

</body>

</html>
