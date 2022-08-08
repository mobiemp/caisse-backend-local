<?php


session_start();
session_destroy();

$base_url = $_SERVER['SERVER_NAME'];
$url = "http://".$base_url."/caisse-backend/caisse/";
header("Location: $url");
