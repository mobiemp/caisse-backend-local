
<div style="background-color: red;height:300px;width: 300px;">
    
</div>




// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

// require 'vendor/autoload.php';

// use Mike42\Escpos\Printer;
// use Mike42\Escpos\PrintConnectors\CupsPrintConnector;


// try {
//     $connector = null;
//     $connector = new CupsPrintConnector("EPSON_TM-T88VI");

//     $printer = new Printer($connector);
//     $printer -> text('test');
//     $printer -> cut();
//      $printer->pulse();
//      echo "ok";
//     $printer->close();
// } catch (Exception $e) {
//     echo json_encode("Impossible d'imprimer sur cette imprimante: " . $e->getMessage() . "\n");
// }