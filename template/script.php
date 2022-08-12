
<script src="../lib/dist/js/jquery.slim.min.js" ></script> 
<script src="../lib/dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../lib/dist/plugins/js/jquery.js"  ></script>
<script src="../lib/dist/plugins/moment/moment.min.js"></script>
<script src="../lib/dist/plugins/daterangepicker/daterangepicker.js"></script>
<script src="../lib/dist/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="../lib/dist/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="../lib/dist/js/adminlte.min.js?v=3.2.0"></script>


<script src="../lib/dist/JsBarcode.ean-upc.min.js"></script>
<script type="text/javascript">
    function imprimeEtiquettes(gencode,titre,prix,colisage){
        JsBarcode("#barcode2", gencode, {
            format:"EAN13",
            width:1.5,
            height:18,
            displayValue:true,
            fontSize:18,
            font:'cursive',
        });
        prix = prix.toFixed(2).toString();
        var splittedPrice = prix.split('.');
        var prixEntier = splittedPrice[0];
        var prixDecimal = splittedPrice[1];
        $('#titleEtiquette').html('') ;
        $('#prixEntier').text('');

        $('#prixEntier').append(prixEntier).append('<span>.'+prixDecimal+'€</span>');
        $('#titleEtiquette').append(titre);
        $('#colisage').append(colisage)
        window.print();
    }


</script>
