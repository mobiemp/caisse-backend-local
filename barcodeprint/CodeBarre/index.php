<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>Code barre 128 + EAN</title>
	<style>
		@font-face 
		{
			font-family: 'Code128';
			src: url('code128.woff') format('woff');
		}
		
		@font-face 
		{
			font-family: 'CodeEAN';
			src: url('ean13.woff') format('woff');
		}		
		.barcode128 {
			font-family: 'Code128'; 
			font-size: 40px;
		}	
		
		.barcodeEAN {
			font-family: 'CodeEAN'; 
			font-size: 40px;
		}			
	</style>
  </head>
  <body>
	<?php
		/*
		https://fr.wikipedia.org/wiki/Code_128
		https://fr.wikipedia.org/wiki/Code-barres_EAN
		*/
		include('code128-EAN13.php');
		$NoArt = "782940199617";
		$codeEAN =CodeEAN13($NoArt);
	?> 	
	
	<br>
	<h1>Code barre EAN13</h1>
	<!-- <h2>Article N°:<?php echo $NoArt; ?></h2> -->
	<p class="barcodeEAN" id="barcode" ><?php echo $codeEAN ?></p>
	
  </body>
  </html>
