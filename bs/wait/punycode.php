<?php

// Include the class
require_once('idna_convert.class.php');
// Instantiate it
$IDN = new idna_convert();
// The input string, if input is not UTF-8 or UCS-4, it must be converted before
//$input = utf8_encode('rotkäppchensalon.de');
$input = 'rotkäppchensalon.de?sfasfas=fasqww#ggfd';
// Encode it to its punycode presentation
$output = $IDN->encode($input);
// Output, what we got now
echo $output;

// include_once "class-punycode.php";
// echo Punycode::urlencode("rotkäppchen.de");
