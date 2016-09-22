<?php
// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');


ini_set("soap.wsdl_cache_enabled", "0");
ini_set("default_socket_timeout", "120");

ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
 

// A seguir você devera informar a URL do webservice.
$oSoapClient = new SoapClient("http://lanpiasoa/wsdl/server/readFretesGFEService.wsdl");
 
$aOptions = array (
       "start_debug"=> "1",
       "debug_port"=> "10000",
       "debug_host"=> "localhost",
       "debug_stop"=> "1",
       "trace"=> "1");
 
foreach($aOptions as $key => $val) {
        $oSoapClient->__setCookie($key,$val);
}

//$resposta = $oSoapClient->readTicketSuporteFuncao("28", "a", "a", "a", "a", "a", "a", 'thiago.santos', 'tpiasl2k16*');
$resposta = $oSoapClient->readFretesGFEFuncao($_GET['inicio'], $_GET['final'], $_GET['metodo'], 'thiago.santos', 'tpiasl2k16*')


// output the column headings
if($_GET['metodo'] == "transportadora") fputcsv($output, array('Column 1', 'Column 2', 'Column 3'));
else fputcsv($output, array('Column 1', 'Column 2', 'Column 3'));


// loop over the rows, outputting them
while ($row = mysql_fetch_assoc($rows)) fputcsv($output, $row);