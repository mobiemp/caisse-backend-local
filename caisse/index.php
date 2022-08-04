<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Caisse</title>
    <link rel="stylesheet" href="../lib/dist/plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="../lib/dist/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="../lib/dist/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css"/>
    <link rel="stylesheet" href="../lib/dist/plugins/chart.js/Chart.min.css"/>
    <link rel="stylesheet" href="../lib/dist/css/adminlte.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
          integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="../template/style.css"/>
</head>
<body style="background-color: rgb(242, 242, 242);">
<!--<div class="container">-->
<div class="row" style="height: 100vh;overflow: hidden">
    <div class="col-md-1" style="background-color: rgb(48, 52, 86);">

    </div>
    <div class="col-md-8 " style="padding: 30px;">
        <!--				<form action="../searchProduit.php" id="formSearchProduit" >-->
        <div class="input-group" style="margin-bottom: 30px">
            <input type="search" class="form-control form-control-lg" id="searchArticle"
                   placeholder="Scanner un article">
            <div class="input-group-append">
                <button type="submit" class="btn btn-lg btn-default">
                    <i class="fa fa-barcode"></i>
                </button>
            </div>
        </div>
        <!--				</form>-->

        <div id="produits" class="card"
             style="padding-bottom: 30px;background-color: rgb(242, 247, 251);height: 80%;overflow-y:auto">
            <div class="card-header" style="background-color: rgb(70, 130, 180);color: #ffffff">
                <div class="row">
                    <div class="col-md-10">
                        <h2 style="font-size: 25px;" class="card-title">
                            <i class="fas fa-shopping-cart"></i>
                            CADDIE
                        </h2>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-block bg-gradient-danger">Client Suivant</button>
                    </div>
                </div>
            </div>
            <div class="row" style="margin:15px 30px 0 30px;border-bottom: 1px solid lightgrey">
                <div class="col-md-4">
                    <p>Désignation</p>
                </div>
                <div class="col-md-2">
                    <p>QTE</p>
                </div>
                <div class="col-md-1">
                    <p>PU €</p>
                </div>
                <div class="col-md-1">
                    <p>Montant (€)</p>
                </div>
                <div class="col-md-2">
                    <p>Remise (%)</p>
                </div>
                <div class="col-md-1">
                    <p>Remise (€)</p>
                </div>

            </div>
            <?php
            $json = file_get_contents('../jsons/panier.json');
            $parsedJson = json_decode($json, true);
            if (count($parsedJson) > 0) {
                foreach ($parsedJson as $article) {
                    ?>
                    <div class="callout callout-info" style="margin:15px 30px 0 30px">
                        <div class="row">
                            <div class="col-md-4">
                                <p style="font-family: " Tahoma"><?php echo strtoupper($article['titre']) ?><i
                                        class="fa fa-trash text-red" style="cursor:pointer;"
                                        onclick="deleteArticle(this.id)"
                                        id="deleteProduit-<?php echo $article['ref'] ?>"></i></p>
                            </div>
                            <div class="col-md-2">
                                <input type="text" style="width: 50px;" name="quantiteProduit"
                                       id="quantiteProduit-<?php echo $article['ref'] ?>"
                                       value="<?php echo $article['qte'] ?>"/>
                            </div>
                            <div class="col-md-1"><p
                                        id="pu_euro-<?php echo $article['ref'] ?>">
                                    <?php echo $article['remise'] > 0 ? $article['pu_euro'] - ($article['pu_euro']  * ($article['remise']/100)) :  $article['pu_euro']   ?>
                                    €</p></div>
                            <div class="col-md-1" id="montantEuro-<?php echo $article['ref'] ?>"><p
                                        class="montantEuro"><?php echo $article['remise'] > 0 ? $article['pu_euro'] * $article['qte'] - ($article['pu_euro'] * $article['qte'] * ($article['remise']/100)) :  $article['pu_euro'] * $article['qte']  ?>€</p>
                            </div>
                            <div class="col-md-2">
                                <input type="text" style="width: 50px;" name="remiseProduit"
                                       id="remiseProduit-<?php echo $article['ref'] ?>"
                                       value="<?php echo $article['remise'] ?>"/> %
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
    <div class="col-md-3" style="padding:50px">
        <h3 style="font-family: 'Tahoma' ;font-weight: 600">TOTAL A PAYER</h3>
        <div class="card">
            <!--                <div class="card-header">-->
            <!--                    <h3 class="card-title">-->
            <!--                        <i class="fas fa-text-width"></i>-->
            <!--                        TOTAL A PAYER-->
            <!--                    </h3>-->
            <!--                </div>-->

            <div class="card-body" style="border:3px solid #ffffff ;border-radius:10px; background-color: rgb(0, 0, 139);color:#ffffff;
                padding: 0 15px !important;">
                <p id="total" class="float-right" style="font-size: 50px;font-weight:800;font-family: 'Tahoma' ">
                    <?php
                    include('../DBConfig.php');
                    $sql = "SELECT pu_euro,qte,remise FROM table_client_panier";
                    $query = $conn->query($sql);
                    $total = 0;
                    if ($query->num_rows > 0) {
                        while ($row = $query->fetch_assoc()) {
                            $pu_euro = $row['pu_euro'];
                            $qte = $row['qte'];
                            $remise = $row['remise'];
                            $total += $remise > 0 ? $pu_euro * $qte * ($remise / 100) : $pu_euro * $qte;
                        }
                        echo $total . "€";
                    } else {
                        echo "0.00€";
                    }
                    ?>
                </p>
            </div>

        </div>

        <div class="card">
            <!--                <div class="card-header">-->
            <!--                    <h3 class="card-title">-->
            <!--                        <i class="fas fa-text-width"></i>-->
            <!--                        TOTAL A PAYER-->
            <!--                    </h3>-->
            <!--                </div>-->

            <div class="card-body" style="border: 1px solid steelblue;border-radius: 10px">
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-block btn-primary btn-lg btnCaisse" data-toggle="modal"
                                data-target="#modal-espece" id="paiementEspece">
                            Espèces
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-block btn-primary btn-lg btnCaisse" style="background-color: " data-toggle="modal"
                                data-target="#modal-cb" id="paiementEspece">
                            CB
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-block btn-primary btn-lg btnCaisse" data-toggle="modal"
                                data-target="#modal-cheques" id="paiementEspece">
                            Chèques
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-block btn-primary btn-lg btnCaisse">Retour article</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-block btn-primary btn-lg btnCaisse" data-toggle="modal"
                                data-target="#modal-divers" id="paiementEspece">
                            Divers
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-block btn-primary btn-lg btnCaisse">Vider</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-block btn-primary btn-lg btnCaisse" data-toggle="modal"
                                data-target="#modal-remise" id="paiementEspece">
                            Remise
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-block btn-primary btn-lg btnCaisse">Total caisse</button>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>


<!-- MODAL -->
<div class="modal fade" id="modal-espece" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Paiement Espèce</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-center" style="font-size: 18px"> Choisir le montant ou payer <span
                            style="font-weight: 600" id="montantEspece"></span> € en espèce</p>
                <div class="col-md-12 text-center">
                    <input type="text" id="inputMontantEspece" style="width:100px;"/> <span> €</span>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="btnPaiementEspece">Payer</button>
            </div>
        </div>

    </div>

</div>


<!--</div>-->
</body>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF"
        crossorigin="anonymous"></script>
<script src="../lib/dist/js/jquery.js"></script>
<script src="../lib/dist/plugins/moment/moment.min.js"></script>
<script src="../lib/dist/plugins/daterangepicker/daterangepicker.js"></script>
<script src="../lib/dist/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="../lib/dist/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="../lib/dist/js/adminlte.min.js?v=3.2.0"></script>

<script type="text/javascript">

    // CALCUL DU TOTAL PANIER
    // var textValues = $('.montantEuro').map((i, el) => el.innerText.trim()).get();
    // var total = 0
    // textValues.forEach(function (item, index) {
    //     var montant = item.split(' ')[0]
    //     montant = parseFloat(montant)
    //     total += montant
    // });
    // $('#total').html(total.toFixed(2) + "€")


    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    // VIDER LE PANIER
    function clearPanier() {
        $.ajax({
            url: "../panier/videPanier.php",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({"clear": true}),
            success: function (data) {
                if (data == 1) {
                    Toast.fire({
                        icon: 'success',
                        title: "Achat validé !"
                    })
                    $('#produits').html("")

                }
            }
        })
    }
    function getTotal(){
        var total = $('#total').text()
        total = total.replace(/\s/g, '');
        total = total.replace('€', '');
        return total;
    }

    // PAIEMENTS ESPECE
    $('#paiementEspece').click(function () {
        var total = getTotal();
        $('#inputMontantEspece').val(total)
        $('#montantEspece').html(total)
    })

    $('#btnPaiementEspece').click(function () {
        var montantEspece = $('#inputMontantEspece').val();
        var total = getTotal()
        if (montantEspece == total) {
            $.ajax({
                url: "../panier/panier_temp.php",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    "espece": montantEspece,
                    "cb": 0,
                    "cheques": 0,
                    "ticket_restaurant": 0,
                    "totalTemp": total
                }),
                success: function (data) {
                    console.log(data)
                    if (data == 2) {
                        clearPanier()
                        window.location.reload()
                    }
                }
            })
        }

    })


    $(document).ready(function () {

        $('#searchArticle').keydown(function (e) {
            // $('#print-button').css('display','none');
            if (e.which == 13) {
                var ref = $(this).val();
                var qte = 1
                $.ajax({
                    url: "../searchProduit.php",
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({"search": ref, "session": 1}),
                    success: function (data) {
                        var result = JSON.parse(data)
                        $('#total').html(result.total.toFixed(2) + " €")
                        $('#searchArticle').val("")
                        // GENERE PRODUIT
                        var increment = false;
                        $("input[name='quantiteProduit']").each(function () {
                            var id = $(this).attr('id');
                            var refForm = id.split('-')[1]
                            if (refForm === result.data.ref) {
                                increment = true;
                                return false;
                            }
                        });

                        var remise = parseInt(result.data.remise) > 0 ? parseFloat(result.data.pu_euro) * parseFloat(result.data.qte) * (parseInt(result.data.remise) / 100) : "0.00€"
                        if (increment === false) {
                            $('#produits').append(
                                '<div class="callout callout-info" style="margin:15px 30px 0 30px">\n' +
                                '<div class="row">' +
                                '<div class="col-md-4">' +
                                '<p>' +
                                result.data.titre +
                                '<i class="fa fa-trash text-red" style="cursor:pointer;" onclick="deleteArticle(this.id)" id="deleteProduit-' + result.data.ref + '"></i>' +
                                '</p>' +
                                '</div>' +
                                '<div class="col-md-2">' +
                                '<input type="text" style="width: 50px;" name="quantiteProduit" id="quantiteProduit-' + result.data.ref + '" value="' + result.data.qte + '" />' +
                                '</div>' +
                                '<div class="col-md-1">' +
                                '<p>' +
                                result.data.pu_euro +
                                '€</p>' +
                                '</div>' +
                                '<div class="col-md-1">' +
                                '<p>' +
                                parseFloat(result.data.pu_euro) * parseFloat(result.data.qte) +
                                '€</p>' +
                                '</div>' +
                                '<div class="col-md-2">' +
                                '<input type="text" style="width: 50px;" name="remiseProduit" id="remiseProduit-' + result.data.ref + '" value="' + result.data.remise + '" /><span>%</span>' +
                                '</div>' +
                                '<div class="col-md-1">' +
                                '<p>' +
                                remise +
                                '</p>' +
                                '</div>' +
                                '</div>' +
                                '</div>'
                            )
                        } else {
                            var newQte = parseInt($('#quantiteProduit-' + result.data.ref).val())
                            var updatedQTE = parseInt(result.data.qte)
                            var actualMontantEuro = $('#montantEuro-' + result.data.ref).text()
                            actualMontantEuro = parseFloat(actualMontantEuro)
                            var newMontantEuro = parseFloat(result.data.pu_euro) * updatedQTE + actualMontantEuro;
                            $('#montantEuro-' + result.data.ref).html(newMontantEuro.toFixed(2) + "€")
                            $('#quantiteProduit-' + result.data.ref).val(newQte + updatedQTE)
                        }


                    }
                })

            }
        });
    });

    function deleteArticle(id) {
        var ref = id.split('-')[1]

        $.ajax({
            url: "../panier.php",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({"deleteArticle": ref, "session": 1}),
            success: function (data) {

                var result = JSON.parse(data)
                console.log(result)
                if (result.response !== 1) {
                    Toast.fire({
                        icon: 'error',
                        title: result.message
                    })
                } else {
                    Toast.fire({
                        icon: 'success',
                        title: "Article supprimé du panier"
                    })
                    window.location.reload()

                }
            }
        })
    }

    $("input[type=text][name=quantiteProduit]").on("keypress", function (e) {
        if (e.which == 13) {
            var newQte = $(this).val()
            var id = $(this).attr('id')
            var ref = id.split('-')[1]
            $.ajax({
                url: "../panier.php",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({"updateQTE": newQte, "refQte": ref, "session": 1}),
                success: function (data) {
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

$(document).ajaxComplete(function(){
    $("input[type=text][name=remiseProduit]").on("keypress", function (e) {
        if (e.which == 13) {
            var newRemise = $(this).val()
            var id = $(this).attr('id')
            var ref = id.split('-')[1]

            $.ajax({
                url: "../panier.php",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({"ajoutRemise": newRemise, "refRemise": ref, "session": 1}),
                success: function (data) {
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
})
    $("input[type=text][name=remiseProduit]").on("keypress", function (e) {
        if (e.which == 13) {
            var newRemise = $(this).val()
            var id = $(this).attr('id')
            var ref = id.split('-')[1]

            $.ajax({
                url: "../panier.php",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({"ajoutRemise": newRemise, "refRemise": ref, "session": 1}),
                success: function (data) {
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
