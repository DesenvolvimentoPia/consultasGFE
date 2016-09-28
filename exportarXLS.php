<?php

date_default_timezone_set('America/Sao_Paulo');

/** Error reporting */
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);


/** PHPExcel */
include 'phpExcel/Classes/PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
include 'phpExcel/Classes/PHPExcel/Writer/Excel2007.php';

// Create new PHPExcel object
//echo date('H:i:s') . "Create new PHPExcel object\n";
$objPHPExcel = new PHPExcel();

// Set properties
//echo date('H:i:s') . " Set properties\n";
$objPHPExcel->getProperties()->setCreator("Thiago Santos");
$objPHPExcel->getProperties()->setLastModifiedBy("Thiago Santos");
$objPHPExcel->getProperties()->setTitle("Planilha GFE");
$objPHPExcel->getProperties()->setSubject("Planilha GFE");
$objPHPExcel->getProperties()->setDescription("Planilha GFE Exportada via Interface Gráfica Web.");




// Add some data
//echo date('H:i:s') . " Add some data\n";
$objPHPExcel->setActiveSheetIndex(0);



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

if($_GET['metodo'] == "estados"){

$letra = "BCDEFGHIJKLMNOPQRSTUVWXYZ";

$objPHPExcel->getDefaultStyle()
    ->getAlignment()
    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$styleSub = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FFFFFF')
    ),
    'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '4488DD')
    ));

$styleTot = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FFFFFF')
    ),
    'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '2266CC')
    ));

$styleTotal = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FFFFFF')
    ),
    'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '222222')
    ),
    'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
    );

$styleLinha = array(
    'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
    );

$styleLinha2 = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FFFFFF')
    ),
    'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '2266CC')
    ),
    'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
    );

for($n = 0; $n < 512; $n++) {
	$objPHPExcel->getActiveSheet()->getRowDimension($n)->setRowHeight(20);
}



$ano = substr($_GET['inicio'], 0, 4);
$mes = substr($_GET['inicio'], -4, -2);
$dia = substr($_GET['inicio'], -2);   

$ano1 = substr($_GET['final'], 0, 4);
$mes1 = substr($_GET['final'], -4, -2);
$dia1 = substr($_GET['final'], -2);   


	$OperacaosAgrupadas = 2;
	$notasAgrupadas = 2;
	$primeiroResultado = 2;
	$j = 1;
	$sub = 0;
	$subtotal = 0;

for($i = 0; $i < count($resposta); $i++) {

	$z = 0;
	$j++;
	$m = $j-1;

$formatted_array = array_map(function($num){ if(is_double($num)) return number_format($num,2, '.', ''); else return $num; }, $resposta[$i]);


	foreach($resposta[$i] as $x => $x_value) {

		if($x != "tipoOperacao") $objPHPExcel->getActiveSheet()->SetCellValue($letra[$z].$j, $resposta[$i][$x]);
		else {
			$objPHPExcel->getActiveSheet()->SetCellValue($letra[$z].$j, intval($resposta[$i][$x]));
			$objPHPExcel->getActiveSheet()->getStyle($letra[$z].$j)->applyFromArray($styleLinha);
		}

		if ($x == "estado") $objPHPExcel->getActiveSheet()->getStyle($letra[$z].$j)->applyFromArray($styleLinha);

		if($x == "tipoNota" && isset($tipoNotaAtual) && $tipoNotaAtual != $resposta[$i][$x]) {
			$tipoNotaAtual = $resposta[$i][$x];
			$merge = $m - $subtotal;
			if($merge  < $notassAgrupadas) $merge = $notassAgrupadas;
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($letra[$z].$notasAgrupadas.':'.$letra[$z].$merge);
			$objPHPExcel->getActiveSheet()->getStyle($letra[$z].$notasAgrupadas.':'.$letra[$z].$merge)->applyFromArray($styleLinha);
			$notasAgrupadas = $j;

		}

		else if($x == "tipoNota" && !isset($tipoNotaAtual)) $tipoNotaAtual = $resposta[$i][$x];


		if($x == "tipoOperacao" && isset($tipoOperacaoAtual) && $tipoOperacaoAtual != $resposta[$i][$x]) {
			$subtotal++;
			$tipoOperacaoAtual = $resposta[$i][$x];
			$merge = $m - $subtotal;
			if($merge  < $OperacaosAgrupadas) $merge = $OperacaosAgrupadas;
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells($letra[$z].$OperacaosAgrupadas.':'.$letra[$z].$merge);
			$objPHPExcel->getActiveSheet()->getStyle($letra[$z].$OperacaosAgrupadas.':'.$letra[$z].$merge)->applyFromArray($styleLinha);
			$OperacaosAgrupadas = $j;




		}

		else if($x == "tipoOperacao" && !isset($tipoOperacaoAtual)) $tipoOperacaoAtual = $resposta[$i][$x]; 


		if($i == count($resposta)-1) {

			if($x == "tipoNota" && $tipoNotaAtual == $resposta[$i-1][$x]) {
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells($letra[$z].$notasAgrupadas.':'.$letra[$z].$j);
			}	
		}



		$z++;
	}


		if($tipoOperacaoAtual != $resposta[$i+1]['tipoOperacao'] && strstr($resposta[$i]['tipoNota'], "NFS") ) {
			$j++;
			$ultimo = $j-1;
			$objPHPExcel->getActiveSheet()->SetCellValue("C".$j, "Total Logística ".$tipoOperacaoAtual);
			$objPHPExcel->getActiveSheet()->SetCellValue("E".$j, "=SUM(E".$primeiroResultado.":E".$ultimo.")");
			$objPHPExcel->getActiveSheet()->SetCellValue("F".$j, "=SUM(F".$primeiroResultado.":F".$ultimo.")");
			$objPHPExcel->getActiveSheet()->SetCellValue("G".$j, "=SUM(G".$primeiroResultado.":G".$ultimo.")");
			$objPHPExcel->getActiveSheet()->SetCellValue("H".$j, "=G".$j."/E".$j);
			$objPHPExcel->getActiveSheet()->SetCellValue("I".$j, "=G".$j."/F".$j);
			$objPHPExcel->getActiveSheet()->SetCellValue("J".$j, "=F".$j."/E".$j);

			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C'.$j.':D'.$j);

			$objPHPExcel->getActiveSheet()->getStyle('C'.$j.':J'.$j)->applyFromArray($styleSub);
			$objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(25);

			$arraySub[$sub] = $j;
			$sub++;

			$primeiroResultado = $j+1;
		}

		if($tipoNotaAtual != $resposta[$i+1]['tipoNota'] && strstr($resposta[$i]['tipoNota'], "NFTS") ) {
		}

		else if($tipoNotaAtual != $resposta[$i+1]['tipoNota']) {
			$j++;
			$ultimo = $j-1;
			$objPHPExcel->getActiveSheet()->SetCellValue("B".$j, "Total NFS");
			$objPHPExcel->getActiveSheet()->SetCellValue("E".$j, "=E".$arraySub[0]."+E".$arraySub[1]);
			$objPHPExcel->getActiveSheet()->SetCellValue("F".$j, "=F".$arraySub[0]."+F".$arraySub[1]);
			$objPHPExcel->getActiveSheet()->SetCellValue("G".$j, "=G".$arraySub[0]."+G".$arraySub[1]);
			$objPHPExcel->getActiveSheet()->SetCellValue("H".$j, "=G".$j."/E".$j);
			$objPHPExcel->getActiveSheet()->SetCellValue("I".$j, "=G".$j."/F".$j);
			$objPHPExcel->getActiveSheet()->SetCellValue("J".$j, "=F".$j."/E".$j);
			$arrayTot[0] = $j;

			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.$j.':D'.$j);

			$objPHPExcel->getActiveSheet()->getStyle('B'.$j.':J'.$j)->applyFromArray($styleTot);
			$objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(25);

			$primeiroResultado = $j+1;
		}


}





$j++;
$ultimo = $j-1;
$objPHPExcel->getActiveSheet()->SetCellValue("B".$j, "Total NFTS");
$objPHPExcel->getActiveSheet()->SetCellValue("E".$j, "=SUM(E".$primeiroResultado.":E".$ultimo.")");
$objPHPExcel->getActiveSheet()->SetCellValue("F".$j, "=SUM(F".$primeiroResultado.":F".$ultimo.")");
$objPHPExcel->getActiveSheet()->SetCellValue("G".$j, "=SUM(G".$primeiroResultado.":G".$ultimo.")");
$objPHPExcel->getActiveSheet()->SetCellValue("H".$j, "=G".$ultimo."/E".$ultimo);
$objPHPExcel->getActiveSheet()->SetCellValue("I".$j, "=G".$ultimo."/F".$ultimo);
$objPHPExcel->getActiveSheet()->SetCellValue("J".$j, "=F".$ultimo."/E".$ultimo);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.$j.':D'.$j);
$objPHPExcel->getActiveSheet()->getStyle('B'.$j.':D'.$j)->applyFromArray($styleLinha2);

$objPHPExcel->getActiveSheet()->getStyle('D'.$j.':J'.$j)->applyFromArray($styleTot);
$objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(25);


$arrayTot[1] = $j;

$j++;
$ultimo = $j-1;
$objPHPExcel->getActiveSheet()->SetCellValue("A".$j, "Total do Periodo");
$objPHPExcel->getActiveSheet()->SetCellValue("E".$j, "=E".$arrayTot[0]."+E".$arrayTot[1]);
$objPHPExcel->getActiveSheet()->SetCellValue("F".$j, "=F".$arrayTot[0]."+F".$arrayTot[1]);
$objPHPExcel->getActiveSheet()->SetCellValue("G".$j, "=G".$arrayTot[0]."+G".$arrayTot[1]);
$objPHPExcel->getActiveSheet()->SetCellValue("H".$j, "=G".$ultimo."/E".$ultimo);
$objPHPExcel->getActiveSheet()->SetCellValue("I".$j, "=G".$ultimo."/F".$ultimo);
$objPHPExcel->getActiveSheet()->SetCellValue("J".$j, "=F".$ultimo."/E".$ultimo);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$j.':D'.$j);

$objPHPExcel->getActiveSheet()->getStyle('A'.$j.':J'.$j)->applyFromArray($styleTotal);
$objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(32);

$j--;

$objPHPExcel->getActiveSheet()->SetCellValue("A2", "De ".$dia."/".$mes."/".$ano." até ".$dia1."/".$mes1."/".$ano1);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:A'.$j);


$meses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
$objPHPExcel->getActiveSheet()->SetCellValue("A1", $meses[$mes-1]);
$objPHPExcel->getActiveSheet()->SetCellValue("B1", "Esp Docto");
$objPHPExcel->getActiveSheet()->SetCellValue("C1", "Tipo Oper");
$objPHPExcel->getActiveSheet()->SetCellValue("D1", "UF");
$objPHPExcel->getActiveSheet()->SetCellValue("E1", "Peso Bruto (KG)");
$objPHPExcel->getActiveSheet()->SetCellValue("F1", "Faturamento");
$objPHPExcel->getActiveSheet()->SetCellValue("G1", "Custo Frete");
$objPHPExcel->getActiveSheet()->SetCellValue("H1", "Custo/KG");
$objPHPExcel->getActiveSheet()->SetCellValue("I1", "Custo %");
$objPHPExcel->getActiveSheet()->SetCellValue("J1", "Preço Médio");

$objPHPExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleLinha);



}

else {

	$letra = "BCDEFGHIJKLMNOPQRSTUVWXYZ";

$objPHPExcel->getDefaultStyle()
    ->getAlignment()
    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$styleSub = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FFFFFF')
    ),
    'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '4488DD')
    ));

$styleTot = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FFFFFF')
    ),
    'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '2266CC')
    ));

$styleTotal = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FFFFFF')
    ),
    'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '222222')
    ),
    'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
    );

$styleLinha = array(
    'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
    );

$styleLinha2 = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FFFFFF')
    ),
    'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '2266CC')
    ),
    'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
    );

for($n = 0; $n < 512; $n++) {
	$objPHPExcel->getActiveSheet()->getRowDimension($n)->setRowHeight(20);
}



$ano = substr($_GET['inicio'], 0, 4);
$mes = substr($_GET['inicio'], -4, -2);
$dia = substr($_GET['inicio'], -2);   

$ano1 = substr($_GET['final'], 0, 4);
$mes1 = substr($_GET['final'], -4, -2);
$dia1 = substr($_GET['final'], -2);   


	$OperacaosAgrupadas = 2;
	$notasAgrupadas = 2;
	$primeiroResultado = 2;
	$j = 1;
	$sub = 0;
	$subtotal = 0;

for($i = 0; $i < count($resposta); $i++) {

	$z = 0;
	$j++;
	$m = $j-1;

$formatted_array = array_map(function($num){ if(is_double($num)) return number_format($num,2, '.', ''); else return $num; }, $resposta[$i]);

		foreach($resposta[$i] as $x => $x_value) {
			$objPHPExcel->getActiveSheet()->SetCellValue($letra[$z].$j, $resposta[$i][$x]);
			$z++;
		}
	}


for($i = 2; $i < $j + 1; $i++) {
	$objPHPExcel->getActiveSheet()->SetCellValue("A".$i, "De ".$dia."/".$mes."/".$ano." até ".$dia1."/".$mes1."/".$ano1);
}

$meses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
$objPHPExcel->getActiveSheet()->SetCellValue("A1", $meses[$mes-1]);
$objPHPExcel->getActiveSheet()->SetCellValue("B1", "Código Trans.");
$objPHPExcel->getActiveSheet()->SetCellValue("C1", "Nome Transportadora");
$objPHPExcel->getActiveSheet()->SetCellValue("D1", "Unidade");
$objPHPExcel->getActiveSheet()->SetCellValue("E1", "Operação");
$objPHPExcel->getActiveSheet()->SetCellValue("F1", "Nota");
$objPHPExcel->getActiveSheet()->SetCellValue("G1", "Faturamento");
$objPHPExcel->getActiveSheet()->SetCellValue("H1", "Custo/KGPeso Bruto (Kg)");
$objPHPExcel->getActiveSheet()->SetCellValue("I1", "Custo Frete");


}
		
// Save Excel 2007 file
//echo date('H:i:s') . " Write to Excel2007 format\n";
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save("xls/".$_GET['metodo'].'-'.date("Y-m-d-H-i-s")."arquivo.xls");

// //Echo done
//echo date('H:i:s') . " Done writing file.\r\n";

header("location: xls/".$_GET['metodo'].'-'.date("Y-m-d-H-i-s")."arquivo.xls");