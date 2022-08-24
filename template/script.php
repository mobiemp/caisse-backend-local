
<script src="../lib/dist/js/jquery.slim.min.js" ></script> 
<script src="../lib/dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- <script src="../lib/dist/js/jquery.js"  ></script> -->
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
            width:1.8,
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

        if(titre.length<6){
            $('#titleEtiquette').css('fontSize','26px')
        }
        if(titre.length>15 && titre.length<=20){
            $('#titleEtiquette').css('fontSize','22px')
        }
        if(titre.length>20 && titre.length<=28){
            $('#titleEtiquette').css('fontSize','20px')
        }
        if(titre.length>28){
            $('#titleEtiquette').css('fontSize','18px')
        }
        console.log(prix.length)
        if(prix.length==4){
            $('.price').css('left','25%')
        }
        if(prix.length==5){
            $('.price').css('left','20%')
        }
        if(prix.length==6){
            $('.price').css('left','18%')
        }

        $('#prixEntier').append(prixEntier).append('<span>.'+prixDecimal+'€</span>');
        if(colisage != ""){
            $('#titleEtiquette').append(titre).append('<span style="position:absolute;left:0;top:0px;font-size:18px;font-weight:normal;letter-spacing:0"><br>'+colisage+'</span>')
        }else{
            $('#titleEtiquette').append(titre);
        }
        window.print();
    }


</script>
