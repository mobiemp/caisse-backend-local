<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include '../DBConfig.php';
if (isset($_GET['date'])) {
    $date = $_GET['date'];
    $date = str_replace('/', '-', $date);
    $date = date('Y-m-d',strtotime($date));
    $sql = "SELECT p_espece_euro, p_cb, p_cheque_euro FROM table_client_ticket WHERE date like '%$date%' ";
    
    $tickets = $conn->query($sql);

    $cb_count = 0;
    $espece_count = 0;
    $cheques_count = 0;

    $total_espece = 0;
    $total_cb = 0;
    $total_cheques = 0;
    while($ticket = $tickets->fetch_assoc()){
        $total_espece+=$ticket['p_espece_euro'];
        $total_cb+=$ticket['p_cb'];
        $total_cheques+=$ticket['p_cheque_euro'];

        if($ticket['p_cb'] > 0){
            $cb_count += 1;
        }
        if($ticket['p_espece_euro'] > 0){
            $espece_count += 1;
        }
        if($ticket['p_cheque_euro'] > 0){
            $cheques_count += 1;
        }
    }
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

                <!-- <div class="card card-danger" id="stats">
                    <div class="card-header">
                        <h3 style="width:100%;text-align: center;">Encaissé hors de la webcaisse</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-3 ">
                            <div class="col-4 text-right text-danger"><label for="cb-ca" style="font-size:17px;">Par carte bancaire : </label></div>
                            <div class="col-6"><input type="number" name="cb-bancaire" /><span class="euro">€</span>  </div>
                        </div>
                        <div class="form-row" style="margin-bottom: 20px;">
                            <div class="col-4 text-right text-danger"><label for="cb-ca" style="font-size:17px;" >Par chèques : </label></div>
                            <div class="col-6"><input type="number" name="cheque" /><span class="euro">€</span>  </div>
                        </div>

                        <div class="form-row">
                            <div class="col-4 text-right"><label for="sous-total" style="font-size:22px;font-weight: 800" >SOUS TOTAL : </label></div>
                            <div class="col-6"><input type="number" name="sous-total"/><span class="euro">€</span></div>
                        </div>
                    </div>
                </div> -->

                <div class="card card-navy" id="stats">
                    <div class="card-header">
                        <h3 style="width:100%;text-align: center;">Dans le systeme webcaisse</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-3 ">
                            <div class="col-4 text-right text-navy"><label for="cb-ca" style="font-size:17px;">Espèces euro : </label></div>
                            <div class="col-6"><input type="number" name="espece" value="<?php echo isset($total_espece) ? $total_espece : 0 ?>" /><span class="euro">€</span>  <span>(<?php echo isset($espece_count) ? $espece_count : 0 ?>)</span></div>
                        </div>
                        <div class="form-row" style="margin-bottom: 20px;">
                            <div class="col-4 text-right text-danger"><label for="cb-ca" style="font-size:19px;font-weight: 800" >Sous-total espèces : </label></div>
                            <div class="col-6"><input type="number" name="sous-total-espece"/><span class="euro">€</span></div>
                        </div>

                        <div class="form-row">
                            <div class="col-4 text-right text-navy"><label for="cheque-euro"  >Chèque euro : </label></div>
                            <div class="col-6"><input type="number" name="cheque-euro" value="<?php echo isset($total_cheques) ? $total_cheques : 0 ?>"  /><span class="euro">€</span>  <span>(<?php echo isset($cheques_count) ? $cheques_count : 0 ?>)</span></div>
                        </div>
                        <div class="form-row">
                            <div class="col-4 text-right text-navy"><label for="cb-ca"  >Carte bancaire CB: </label></div>
                            <div class="col-6"><input type="number" name="cb-ca-2" value="<?php echo isset($total_cb) ? $total_cb : 0 ?>" /><span class="euro">€</span>  <span>(<?php echo isset($cb_count) ? $cb_count : 0 ?>)</span></div>
                        </div>


                    </div>
                </div>

                <div class="card card-secondary" id="stats">
                    <div class="card-header">
                        <h3 style="width:100%;text-align: center;">Résultats</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row mb-3 ">
                            <div class="col-4 text-right text-danger"><label for="espece" style="font-size:22px;font-weight: 800">Espèces à trouver : </label></div>
                            <div class="col-6"><input type="number" name="espece" value="<?php echo isset($total_espece) ? $total_espece : 0 ?>" /><span class="euro">€</span></div>
                        </div>
                    </div>
                </div>

                <div class="form-row" style="padding:20px;0">
                    <div class="col-6"><button type="button" class="btn btn-primary btn-block" onclick="display()"><i class="fa fa-print"></i> Imprimer la page</button></div>
                    <div class="col-6"><button type="button" class="btn btn-success btn-block"> Cloturer la caisse</button></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../template/footer.php') ?>



<?php include('../template/script.php') ?>

<script type="text/javascript">
    function display() {
            window.print();
         }

</script>
</body>

</html>
