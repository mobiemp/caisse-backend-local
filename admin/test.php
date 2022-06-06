<?php 
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


?>



    <style>
        .etiquette {display: none}

        @media print {

           @page { size: auto;  margin: 0mm; }
          .etiquette {
            display: block;
            text-align: center;

        }
        svg{
            position: absolute;
            top: 50%;
            left:17%;
        }
        .title{
            position: absolute;
            top: -5px;
            left: 20%;
            font-weight: 600;
            font-size: 13px;
            letter-spacing: 0;
        }
        .price{
            margin-top: 25px;
            margin-left: 110px;
            font-weight: 600;
            font-size: 32px;
        }
        .price span{
            font-size: 15px;
            font-weight: 600;
        }


    }
</style>

   
    <div class="etiquette">
        <div >
            <p class="title">TRAVEL BERKEY</p>
            <svg id="barcode2"
            jsbarcode-textmargin="1"
            ></svg>
            <p class="price">400<span>.90€</span></p>
        </div>
    </div>

    <script src="../lib/dist/JsBarcode.ean-upc.min.js"></script>


    <script type="text/javascript">



      JsBarcode("#barcode2", "8420499099989", {
              format:"EAN13",
              width:0.9,
              height:11,
              displayValue:true,
              fontSize:13,
              font:'cursive',

            });

        window.print();
  </script>

