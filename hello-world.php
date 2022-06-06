<?php 
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require 'vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;


$connector = null;
$connector = new WindowsPrintConnector("Receipt Printer");
$printer = new Printer($connector);
$printer -> text('hello');
$printer -> cut();
$printer->close();
 ?>