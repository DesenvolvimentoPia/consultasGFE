<?php
// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename='.$_GET['metodo'].'-'.date("Y-m-d-H-i-s").'.csv');

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
$resposta = $oSoapClient->readFretesGFEFuncao($_GET['inicio'], $_GET['final'], $_GET['metodo'], 'thiago.santos', 'tpiasl2k16*');

for($i = 0; $i < count($resposta); $i++) {

	$z = 0;

	if($i == 0) {	
		fputcsv($output, array_keys($resposta[$i]), ";");
	}


$formatted_array = array_map(function($num){ if(is_double($num)) return number_format($num,2, '.', ''); else return $num; }, $resposta[$i]);

	foreach($resposta[$i] as $x => $x_value) {
		if($z == 0) $linha = $resposta[$i][$x];
		else $linha .= ", ".$resposta[$i][$x];
		$z++;
	}

	fputcsv($output, $formatted_array, ";");
}
