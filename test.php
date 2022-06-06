<?php 
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


?>

<!DOCTYPE html>
<html>

<head>
    <title>
        HTML | DOM Window Print() method
    </title>

    <script type="text/javascript">
    </script>
    <style>
        /*.etiquette {display: none}*/

        @media print {
            body * { display:none; }
          .etiquette {
            display: block;
            size: 30mm 21mm;
            margin: 0;
            padding: 0;
        }
        html, body { 
            position: relative;
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 97%;
            margin: 0;
            padding: 0;
        }
        svg {
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
        }
    }</style>
</head>
<body>

<p>dazdazdazdazdazd</p>
    
    <div class="etiquette">
        <div class="col-md-8">
            <h3>TITRE ARTICLE</h3>
            <img id="barcode2"/>
        </div>
        <div class="col-md-4">
            <p>9<span>90</span>euro</p>
        </div>
    </div>

    <script src="lib/dist/JsBarcode.ean-upc.min.js"></script>


    <script type="text/javascript">



      JsBarcode("#barcode2", "9780199532179", {
              format:"EAN13",
              width:1.3,
              height:30,
              displayValue:true,
              fontSize:13,
            });
        window.print();
  </script>
</body>

<html>
