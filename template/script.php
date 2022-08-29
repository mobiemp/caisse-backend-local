
<script src="../lib/dist/js/jquery.slim.min.js" ></script> 
<script src="../lib/dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- <script src="../lib/dist/js/jquery.js"  ></script> -->
<script src="../lib/dist/plugins/moment/moment.min.js"></script>
<script src="../lib/dist/plugins/daterangepicker/daterangepicker.js"></script>
<script src="../lib/dist/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="../lib/dist/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="../lib/dist/js/adminlte.min.js?v=3.2.0"></script>


<!-- <script src="../lib/dist/JsBarcode.ean-upc.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script type="text/javascript">
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
    $('#select-all-search').click(function(event) {
        if(this.checked) {
            // Iterate each checkbox
            $(':checkbox').each(function() {
                this.checked = true;
            });
        } else {
            $(':checkbox').each(function() {
                this.checked = false;
            });
        }
    });


    $('#btnAction').click(function () {
        var ids = []
        $('input[name="produitCheckbox[]"]:checked').each(function () {
            ids.push($(this).attr('id'))
        });
        var choix = $('#choixAction').val();
        console.log(choix)
        if(choix === "supp"){
            if(ids.length>0){
                $.ajax({
                    url: "../admin/request.php",
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({"deleteGroup": ids}),
                    success: function (data) {
                        console.log(data)
                        var result = JSON.parse(data)
                        if (result.response !== 1) {
                            Toast.fire({
                                icon: 'error',
                                title: result.message
                            })
                        } else {
                            Toast.fire({
                                icon: 'success',
                                title: result.message
                            })
                            setTimeout(() => window.location.reload(), 500);
                        }
                    }
                })
            }else{
                Toast.fire({
                    icon: 'error',
                    title: 'Aucun produit n\'a été sélectionné'
                })
            }
        }else if(choix==='add_cat'){
            if(ids.length>0){
                $('#modal-categorie').modal('show');
                $('#btnAddGroupCat').click(function () {
                    var catID = $('#famille').val()
                    $.ajax({
                        url: "../admin/request.php",
                        type: "POST",
                        contentType: "application/json",
                        data: JSON.stringify({"addCatGroup": ids,'cat':catID}),
                        success: function (data) {
                            console.log(data)
                            var result = JSON.parse(data)
                            if (result.response !== 1) {
                                Toast.fire({
                                    icon: 'error',
                                    title: result.message
                                })
                            } else {
                                Toast.fire({
                                    icon: 'success',
                                    title: result.message
                                })
                                setTimeout(() => window.location.reload(), 500);
                            }
                        }
                    })
                })
            }else{
                Toast.fire({
                    icon: 'error',
                    title: 'Aucun produit n\'a été sélectionné'
                })
            }

        }

    })





    function deleteArticleAdmin(ref,redirect='false') {

        $.ajax({
            url: "../admin/request.php",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({"deleteArticleAdmin": ref}),
            success: function (data) {
                console.log(data)
                var result = JSON.parse(data)
                if (result.response !== 1) {
                    Toast.fire({
                        icon: 'error',
                        title: result.message
                    })
                } else {
                    Toast.fire({
                        icon: 'success',
                        title: result.message
                    })
                    console.log(redirect)
                    if(redirect=="true"){
                        setTimeout(() => window.location.href="articles.php", 500);
                    }else{
                        setTimeout(() => window.location.reload(), 500);
                    }


                }
            }
        })
    }
    function imprimeEtiquettes(gencode,titre,prix,colisage){
        JsBarcode("#barcode2", gencode, {
            format:"EAN13",
            width:1.5,
            height:30,
            displayValue:true,
            fontSize:15,
            font:'monospace',
           // flat: true
        });

        prix = prix.toFixed(2).toString();
        var splittedPrice = prix.split('.');
        var prixEntier = splittedPrice[0];
        var prixDecimal = splittedPrice[1];
        $('#titleEtiquette').html('') ;
        $('#prixEntier').text('')

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
            $('.price').css('left','23%')
            JsBarcode("#barcode2", gencode, {
            format:"EAN13",
            width:1.5,
            height:40,
            displayValue:true,
            fontSize:15,
            font:'monospace',
            //flat: true
        });
        }
        if(prix.length==5){
            $('.price').css('left','20%')
        }
        if(prix.length==6){
            $('.price').css('left','19%')
            $(".price").css({
    			fontSize: 60
			});
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
