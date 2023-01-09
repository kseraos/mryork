<?php

unset($_SERVER['HTTP_X_REQUESTED_WITH']);
function santirariza_var($value){
	$sanitizeRules = array('DELETE FROM ','DROP TABLE ','HOW TABLE ',' * ',' -- ','= ','join','join');
	return (!get_magic_quotes_gpc()) ? str_replace("'","`",(str_ireplace($sanitizeRules,"",$value))) : str_ireplace($sanitizeRules,"",$value);
}
function _antiSqlInjection($Target){
	$arraSanitized=array();
	foreach($Target as $key => $value){
		if(is_array($value)){
			 $arraSanitized[$key] = _antiSqlInjection($value);
		}else{
			$arraSanitized[santirariza_var($key)] = santirariza_var($value);
		}
	}
	return $arraSanitized;
}
$_GET = _antiSqlInjection($_GET);
$_POST = _antiSqlInjection($_POST);
$_REQUEST = _antiSqlInjection($_REQUEST);
require "VktFunctions.php"; // Funções que auxiliao o desenvolvimento 
require "VktConfig.php"; // COnfigura;cões basicas do sistema


require "Vkt.class.php"; // Classe basic da vekttor, validacao , menu, login e assuntos refentes a vekttos, pagamento
require "cadastroCliente/cadastroCliente.class.php";

$Vkt = new Vkt();
$Ctrl =$_REQUEST["Ctrl"];



if(is_string($Ctrl)){
    
    require "cadastroCliente/cadastroClienteCtrl.class.php";
    
}else{
    
     include "indexView.php";

}

$Vkt->close();





