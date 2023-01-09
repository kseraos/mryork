<?php
session_cache_expire(480);
session_start();

require_once('auth.php');
ini_set('default_charset','UTF-8');
ini_set('short_open_tag','On');
ini_set('error_reporting','E_ALL & ~E_NOTICE & ~E_DEPRECATED');
ini_set('max_input_vars','10000');
@ini_set("max_execution_time", "300");

date_default_timezone_set('America/Manaus');

/* Dados Arquivos Autoload */
define("__SIS"	,dirname(__FILE__));
define("DS"	, DIRECTORY_SEPARATOR);
define("__DEBUG__",1);

function __autoload($class_name){
	//pr($class_name);
	$class = __SIS.DS.''.str_replace('\\',DS,$class_name).".class.php";
	//echo $class."<br>";
	//exit();
	if(!file_exists($class)){
		echo "<div class='errophp'>Arquivo de Classe não encontrado $class </div>";
		exit;
	}else{
		//pr("$class");
		try{
			include_once $class;
		}catch(Exception $e){
			echo "erro ao chamar a classe ou dentro da classe :".$e->getMessage();
		}
	}
}

/* Dados do usuario*/

	//print_r($_SESSION);
//echo __usuario_tipo_id;
	
//	print_r($_SESSION);

/* Variaveis de nomeclatura*/
	
	$semana_abreviado = array("Dom","Seg","Ter","Qua","Qui","Sex","Sab");
	$semana_extenso = array("Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado",);
	$mes_abreviado = array("Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez");
	$mes_extenso = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
	$bancos_codigos=array(001=>'Banco do Brasil',033=>'Banco Santander(Brasil) S.A.',237=>'Bradesco',104=>'Caixa Econômica Federal',409=>'Itaú Unibanco S/A',399=>"HSBC");
	global $semana_abreviado,$semana_extenso,$mes_abreviado,$mes_extenso;
	
	$arrBandeiras = array(
		"01" => "Visa",
		"02" => "Master",
		"03" => "Amex",
		"04" => "Sorocred",
		"05" => "Elo",
		"06" => "Diners",
		"99" => "Outro"
	);
	
	$arrCodigoBandeiras = array(
		"visa" => "01",
		"master" => "02",
		"amex" => "03",
		"sorocred" => "04",
		"elo" => "05",
		"diners" => "06",
		"outro" => "99"
	);
	

	
	global $semana_abreviado,$semana_extenso,	$mes_abreviado,	$mes_extenso,	$bancos_codigos;
	
	$conn = new \VktCrud;
	$config = $config['dados'][0];
	$root = $_SERVER['DOCUMENT_ROOT'];
	$base = preg_match("/^[[:alnum:]]/", $config['endereco_base']) ? $config['endereco_base'] : $root . $config['endereco_base'];
	if( !defined('__ROOT__') ) define('__ROOT__', $root);
	if( !defined('__BASE__') ) define('__BASE__', $base);


	