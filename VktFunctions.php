<?php

ini_set('display_errors', 1); 
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

function sms($para, $mensagem) {
	
	$dados = array(
		'from' => SMS_DE,
		'text' => $mensagem,
		'to' => $para,
		'api_key' => SMS_API_KEY,
		'api_secret' => SMS_API_SECRET,
	);
	
	ob_start();
	$out = fopen('php://output', 'w');
	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, SMS_URL);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, TRUE);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($dados));
	
	// Debug
	curl_setopt($curl, CURLOPT_VERBOSE, true);
	curl_setopt($curl, CURLOPT_STDERR, $out);
	
	$retorno = curl_exec($curl);
	
	fclose($out);  
	$debug = ob_get_clean();
	
	return $retorno;

}

if (!function_exists('mb_str_pad')) {
	function mb_str_pad($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT, $encoding = NULL)
	{
		$encoding = $encoding === NULL ? 'UTF-8' : $encoding;
		$padBefore = $dir === STR_PAD_BOTH || $dir === STR_PAD_LEFT;
		$padAfter = $dir === STR_PAD_BOTH || $dir === STR_PAD_RIGHT;
		$pad_len -= mb_strlen($str, $encoding);
		$targetLen = $padBefore && $padAfter ? $pad_len / 2 : $pad_len;
		$strToRepeatLen = mb_strlen($pad_str, $encoding);
		$repeatTimes = ceil($targetLen / $strToRepeatLen);
		$repeatedString = str_repeat($pad_str, max(0, $repeatTimes)); // safe if used with valid unicode sequences (any charset)
		$before = $padBefore ? mb_substr($repeatedString, 0, floor($targetLen), $encoding) : '';
		$after = $padAfter ? mb_substr($repeatedString, 0, ceil($targetLen), $encoding) : '';
		return $before . $str . $after;
	}
}

if (!function_exists('stats_standard_deviation')) {
    function stats_standard_deviation(array $a, $sample = false) {
        $n = count($a);
        if ($n === 0) {
            trigger_error("The array has zero elements", E_USER_WARNING);
            return false;
        }
        if ($sample && $n === 1) {
            trigger_error("The array has only 1 element", E_USER_WARNING);
            return false;
        }
        $mean = array_sum($a) / $n;
        $carry = 0.0;
        foreach ($a as $val) {
            $d = ((double) $val) - $mean;
            $carry += $d * $d;
        };
        if ($sample) {
           --$n;
        }
        return sqrt($carry / $n);
    }
}

function shutdown() {
    $error = error_get_last();
    if ($error['type'] === E_ERROR) {
		ob_start();
		$error['time'] = date("Y-m-d H:i:s");
		print_r($error);
		echo "-------------------------------------------------------------------------------------\n\r";
		$err = ob_get_clean();
		@file_put_contents(__DIR__."/errors.txt", $err, FILE_APPEND);
    }
}
register_shutdown_function('shutdown');

function pr(){
	if ( func_num_args() > 0 ){
        foreach( func_get_args() as $d){
			echo"<pre class='depuracao'>";
			print_r($d);
			echo "</pre>";
		}
    }
}

function valorToUsa($v){
	$v = str_replace(".", "", trim($v));
	$v = str_replace(",", ".", $v);
	$v = substr($v, 0, (strrpos($v, ".") + 6));
	return $v;
}

function valorToBr($v){
	$v = str_replace(",", "", trim($v));
	$v = str_replace(".", ",", trim($v));
	$v = substr($v, 0, (strrpos($v, ",") + 6));
	return $v;
}

function getIP() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function getBrowser() {
    return $_SERVER['HTTP_USER_AGENT'];
}

/*
Função de Criptografar somente numero


	ex: ografia_vkt(123,'encripita');
		respotas: VET

	ex: ografia_vkt(VET,false);// decriptograda
		respotas: 123

*/

function criptografia_vkt($numero,$encripta_decripta=NULL){
	
	//criptografia_vkt($numero,'encripita')
	$muda_1_po_1 = $numero."";
	$tamanho = strlen($muda_1_po_1);
	$chave = array(0=>"V",
					1=>"E",
					2=>"K",
					3=>"T",
					4=>"O",
					5=>"R",
					6=>"M",
					7=>"F",
					8=>"I",
					9=>"N");
	
	foreach($chave as $numero => $vkt){
		if($encripta_decripta=='encripita'){
			$muda_1_po_1 = str_replace($numero,$vkt,$muda_1_po_1);
		}else{
			
			$muda_1_po_1 = str_replace($vkt,$numero,$muda_1_po_1);
		}
	}
	
	return $muda_1_po_1;	

	
}



function dataToBr($d){// 2010-07-03 -> 03/07/2010

	$d1 = explode(" ",$d);
	if(count($d1)==2){
		$d2 = explode("-",$d1[0]);
	}else{
		$d2 = explode("-",$d);
	}
	if($d2[0]<1){$d2[0]='0000';}
	if($d2[1]<1){$d2[1]='00';}
	if($d2[2]<1){$d2[2]='00';}
	return $d2[2]."/".$d2[1]."/".$d2[0];
}

function horaToBr($d) {
	$d = explode(" ", $d);
	$d = preg_match("/^[0-9]{2}\:[0-9]{2}\:[0-9]{2}/", $d[1]) ? $d[1] : '00:00:00';
	return $d;
}

function dataToUsa($d){// 03/07/2010 -> 2010-07-03 
	
	$d2 = explode("/",$d);
	
	return $d2[2]."-".$d2[1]."-".$d2[0];
}
function qtdToBr($valor,$casas=3){
	$valor = floatval($valor);
	$nv = str_replace(',','',$valor);
	$nv = str_replace(' ','',$nv);
	if( strrpos($valor, '.') > -1 ) {
		$casasValor = strlen(preg_replace("/[0]+$/", "", substr($valor, strrpos($valor, '.')+1)));
	}
	if( $casasValor > $casas ) {
		return @number_format($nv,$casas,',','.');
	} else {
		return @number_format($nv,$casasValor,',','.');
	}
}
function qtdToUsa($valor,$casas=3){
	$nv = str_replace(' ','',$valor);
	$nv = str_replace('.','',$nv);
	$nv = str_replace(',','.',$nv);
	$nv = $nv*1;
	return number_format($nv,$casas,'.','');
}

function moedaToBr($valor){
	/*  
	Por: M?rio N?vo 14/04/2010
	
	Descri??o:Fun??o que transforma valores monetarios americanos troca ou coloca o ',' separador de milhar por '.' e coloca o separador de desena como ',' com 2 casas
	
	Entra:1111,111,111.00
	
	Sai:1111.111.111,00 
	*/
	$valor = floatval($valor);
	$nv = str_replace(',','',$valor);
	$nv = str_replace('','',$valor)*1;
	
	return @number_format($nv,2,',','.');
}
function n($valor){
	return moedaToBr($valor);
}

function moedaToUsa($valor){
		/*  
	Por: M?rio N?vo 14/04/2010
	
	Descri??o:Fun??o que transforma valores monetarios brasileiro remove ',' separador de milhar e troca   ','  por '.' como separador de desena 
	
	Entra:1111.111.111,00 
	Sai:    1111111111.00
	
	*/
	$nv = str_replace('.','',$valor);
	$nv = str_replace(',','.',$nv);
	
	return @number_format($nv,2,'.','')*1;
	
}

/**
	Criada por : Mário Nôvo
	Data: 20/02/2014
	
	Cria pasta e subnivel depois da pasta uploads

	Ex: $pasta = "../uploads/2/nivel1/nivel2/nivel3/nivel4";
	
	criará as seguintes pastas:
	
		../uploads/2
		../uploads/2/nivel1
		../uploads/2/nivel1/nivel2/
		../uploads/2/nivel1/nivel2/nivel3/
		./uploads/2/nivel1/nivel2/nivel3/nivel4
		


*/
function ciradorDePastas($pasta){
	$pastas = explode("/",$pasta);
	
	
	$pasta_concatenada='';
	
	
	foreach($pastas as $pasta){
		$pasta_concatenada .= $pasta.'/';
		$info = strpos($pasta_concatenada, "uploads/")+8;
		$tbm_pst = strlen($pasta_concatenada);
		if($tbm_pst>$info ){
			if(!is_dir($pasta_concatenada)){
				mkdir($pasta_concatenada);
				chmod($pasta_concatenada,0755);
			}
		}
		
	}
}

//function config_envia_email ($smtp, $porta, $senha, $usuario, $remetente, $remetentenome, $destinatario, $destinatarionome, $assunto, $mensagem, $debug) {

//dados_smtp - host, porta
//dados_remetente - email, usuario, senha
//dados_destinatario - email, nome
//dados_mensagem - assunto, mensagem 
function config_envia_email($dados_smtp,$dados_rementente,$dados_destinatario,$dados_mensagem) {
	
	require '../nucleo/bibliotecas_externas/phpMailer/class.smtp.php';
	require '../nucleo/bibliotecas_externas/phpMailer/class.phpmailer.php';
	
	$mail = new PHPMailer(true);	// the true param means it will throw exceptions on errors, which we need to catch
	$mail->IsSMTP(); 				// telling the class to use SMTP
	$mail->CharSet = "UTF-8";
	//$mail->SMTPDebug  = true;		// enables SMTP debug information (for testing)
	$mail->SMTPAuth  	 = true;		// enable SMTP authentication
	$mail->SMTPKeepAlive = true;  
	$mail->SMTPSecure = "tls";		// sets the prefix to the servier
	$mail->Host       = $dados_smtp['host'];	// sets GMAIL as the SMTP server  "smtp.gmail.com"
	$mail->Port       = $dados_smtp['porta'];// set the SMTP port for the GMAIL server  465
	$mail->Username   = $dados_smtp['username'];// GMAIL username
	$mail->Password   = $dados_smtp['password'];	// GMAIL password
	$mail->SetFrom($dados_rementente['email'], $dados_rementente['nome']);
	$mail->AddReplyTo($dados_rementente['email'], $dados_rementente['nome']);
	try {
		$mail->Subject  = $dados_mensagem['assunto'];
		//$mail->AltBody  = $dados[texto]; // optional, comment out and test
		$mail->MsgHTML($dados_mensagem['mensagem']);
		
		if(is_array($dados_destinatario['email'])){		
			foreach($dados_destinatario['email'] as $email){
				$mail->AddAddress($email, $dados_destinatario['nome']);
			}
		}else{
			$mail->AddAddress($dados_destinatario['email'],  $dados_destinatario['nome']);
		}
		//$mail->AddAttachment($arquivo);
		
		//$mail->Send();
		
		if(!$mail->Send()) {
			return 0;
		} else {
			return 1;
			//guarda_envio($dados);
			
		}
		
		//$mail->ClearAddresses();
		//$mail->ClearAttachments();
	} catch (phpmailerException $e) {
	  //echo $e->errorMessage(); //Pretty error messages from PHPMailer
	  return false ;
	} catch (Exception $e) {
	  //echo $e->getMessage(); //Boring error messages from anything else!
	}
	$mail->SmtpClose();
}
/* 
Inicio da fun??o escrever valor por estenso
para escrever por estenso dei um echo na fun??o
Ex: echo numero($numero,"moeda") ; 
caso nao deseje escrever valores reais trocar moeda por 0
*/

function unidade($unidade){
	$num_unidade = array("","um","dois","tr&ecirc;s","quatro","cinco","seis","sete","oito","nove");
	if(substr($unidade,0,1) == 0){
		$num_unidade = array("00" => "","01" => "um","02" => "dois","03" => "tr&ecirc;s","04" => "quatro","05" => "cinco","06" => "seis","07" => "sete","08" => "oito","09" => "nove");
	}
	return $num_unidade[$unidade];
}

function desena($desena){
	if($desena > 9 && $desena < 20){
		$num_desena = array(10 => "dez",11 => "onze",12 => "doze",13 => "treze",14=> "quatorze",15 => "quinze",16 => "dezesseis",17 => "dezessete",18 => "dezeoito",19 => "dezenove");
		return $num_desena[$desena];
	}elseif($desena > 19){
		$decimal = substr($desena,0,1);
		$unidade =substr($desena,-1);
		$num_desena = array("","","vinte","trinta","quarenta","cinquenta","sessenta","setenta","oitenta","noventa") ;
		if(substr($desena,-1) == "0"){$e = "";}else{$e = " e ";}
		return $num_desena[$decimal].$e.unidade($unidade);
	}
}

function centena($centena){
	if($centena == 100){
		return "cem";
	}else{
		$centensa = substr($centena,0,1) ;
		$desena =substr($centena,-2) ;

		$num_centena = array("","cento","duzentos","trezentos","quatrocentos","quinhentos","seiscentos","setecentos","oitocentos","novecentos",);
		if($desena < 10){
			if($desena == "00"){
				return $num_centena[$centensa];
			}else{
				$desena =substr($centena,-1) ;
				if($ventena>0){
					$e ='e';
				}
				return $num_centena[$centensa]." $e  ".unidade($desena);
			}
		}else{
				if($ventena>0){
					$e ='e';
				}
			return $num_centena[$centensa]." $e  ".desena($desena);
		}
	}
	
}

function milhar($milhar){
	$centena = substr($milhar,-3);
	$milhar = str_replace("$centena", "", $milhar);

	$tamanho = strlen($milhar);

	if(substr($centena,-3) == "000"){$e = "";}else{$e = "e ";}

	if($tamanho == 1){
		return unidade($milhar)." mil $e".centena($centena);
	}
	if($tamanho == 2){ 
		return desena($milhar)." mil $e".centena($centena);
	}
	if($tamanho == 3){
		return centena($milhar)." mil $e".centena($centena);
	}
}

function escreve($numero){
	if($numero < 10){
		return unidade($numero);
	}
	if($numero > 9 && $numero < 100){
		return desena($numero);
	}
	if($numero > 100 && $numero < 1000){
		return centena($numero);
	}
	if($numero > 999 && $numero < 1000000){
		return milhar($numero);
	}
}

function numero($valor,$tipo){
	if(preg_match('/./i', "$valor")){
			$valor = str_replace(".", "", $valor); 
		}
	if(preg_match('/,/i', "$valor")){
			$valor = str_replace(",", ".", $valor); 
		}
	list($numero,$centavos) = explode(".",$valor,2);

	if($tipo == "moeda"){
			
		if($numero > 0){
			$real = " reais ";

			if($centavos > 1){
				$real = " reais e ";
				$esc_centavos = " centavos";
			}elseif($centavos == "01"){
				$real = " reais";
				$esc_centavos = " centavo";
			}else{
				$esc_centavos = "";
			}
		}
		if($numero == 1){
			$real = " real";
			if($centavos > 0){
				$real = " real e ";
				$esc_centavos = " centavos";
			}
			if($centavos == "01"){
				$real = " real ";
				$esc_centavos = " centavo";
			}

		}
			return escreve($numero).$real.escreve($centavos).$esc_centavos;
	}else{
		return escreve($numero);
	}
}

/*
fim do escreve numero
*/

 function curl_info($url){
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_HEADER, 1);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
	
	$content = curl_exec( $ch );
	$info = curl_getinfo( $ch );

	return $info;
}

function httpPost($url, $data){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); // Em segundos 
	curl_setopt($curl, CURLOPT_TIMEOUT, 300); // Em segundos
    $response = curl_exec($curl);
	$info = curl_info($curl);
    curl_close($curl);
    return $response;
}

function enviaEmail($destinatarioNome,$destinatarioEmail,$assunto,$conteudo){

	require_once(realpath(dirname(__FILE__))."/../../v2.02/nucleo/bibliotecas_externas/phpMailer/class.phpmailer.php");

	$mail = new PHPMailer(true);	// the true param means it will throw exceptions on errors, which we need to catch
	$mail->IsSMTP(); 				// telling the class to use SMTP

	$mail->SMTPDebug  = false;		// enables SMTP debug information (for testing)
	$mail->SMTPAuth  	 = true;		// enable SMTP authentication
	$mail->SMTPKeepAlive = true;  
	// $mail->SMTPSecure = "tls";		// sets the prefix to the servier
	$mail->Host       = 'mail.vekttor.com';	// sets GMAIL as the SMTP server  "smtp.gmail.com"
	$mail->Port       = '587'; // set the SMTP port for the GMAIL server  465
	$mail->Username   = 'fechamento@vekttor.com';// GMAIL username
	$mail->Password   = 'eEeXqxyDGuL2';	// GMAIL password
	$mail->SetFrom('fechamento@vekttor.com', 'iComanda');
	$mail->AddReplyTo('fechamento@vekttor.com', 'iComanda');
	
	$emails = preg_split("/(,|;|\\n|\\r)/", preg_replace("/[[:blank:]]+/", "", $destinatarioEmail));

	for( $i = 0; $i < count($emails); $i++ ){
		
		try {
			
			if( !filter_var($emails[$i], FILTER_VALIDATE_EMAIL) ){ continue; }
			
			$mail->Subject  = $assunto;
			$mail->AltBody  = $conteudo; // optional, comment out and test
			$mail->MsgHTML($conteudo);
			$mail->AddAddress(trim($emails[$i]), $destinatarioNome);
			
			if(!$mail->Send()) {
				throw new phpmailerException();
			}
	
			$mail->ClearAddresses();
			$mail->ClearAttachments();
			
		} catch (phpmailerException $e) {
			return false;
		}
		
	}
	
	return true;
	
}

function debug($descricao, $filename = false){
	global $time, $total, $antes, $agora, $debug, $debug_file;
	if( empty($antes) ){
		$agora = $antes = microtime(true);
	}else{
		$agora = microtime(true);
	}
	$time = $agora - $antes;
	$total += $time;
	if ( $filename ) {
		$debug_file = $filename;
	}
	if ( !$debug ) {
		$debug = "====================== START ======================\n\n";
	}
	if ( $descricao === true ) {
		$debug .= "Total: " . number_format($total, 2) . " seconds.\n\n======================= END =======================\n\n";
		if (!is_dir(dirname(__SIS) . DS . "uploads" . DS . "logs")) {
			mkdir(dirname(__SIS) . DS . "uploads" . DS . "logs", 0777, true);
		}
		if ( is_writeable(dirname(__SIS) . DS . "uploads" . DS . "logs") ) {
			file_put_contents(dirname(__SIS) . DS . "uploads" . DS . "logs" . DS . ($debug_file ?: "debug") . ".log", $debug, FILE_APPEND);
		} else {
			echo dirname(__SIS) . DS . "uploads" . DS . "logs" . ": Sem permissão de escrita.";
		}
		unset($debug);
		unset($debug_file);
	}else{
		$debug .= $descricao . " - " . number_format($time, 2) . " seconds.\n";
	}
	$antes = $agora;
}

function str_pad_unicode($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT) {
			$str_len = mb_strlen($str);
			$pad_str_len = mb_strlen($pad_str);
			if (!$str_len && ($dir == STR_PAD_RIGHT || $dir == STR_PAD_LEFT)) {
				$str_len = 1; // @debug
			}
			if (!$pad_len || !$pad_str_len || $pad_len <= $str_len) {
				return $str;
			}
		   
			$result = null;
			$repeat = ceil($str_len - $pad_str_len + $pad_len);
			if ($dir == STR_PAD_RIGHT) {
				$result = $str . str_repeat($pad_str, $repeat);
				$result = mb_substr($result, 0, $pad_len);
			} else if ($dir == STR_PAD_LEFT) {
				$result = str_repeat($pad_str, $repeat) . $str;
				$result = mb_substr($result, -$pad_len);
			} else if ($dir == STR_PAD_BOTH) {
				$length = ($pad_len - $str_len) / 2;
				$repeat = ceil($length / $pad_str_len);
				$result = mb_substr(str_repeat($pad_str, $repeat), 0, floor($length))
							. $str
							   . mb_substr(str_repeat($pad_str, $repeat), 0, ceil($length));
			}
		   
			return $result;
		}

function zip($arquivos = array(), $destino = '') {
	
	$arquivosValidos = array();
	
	if(is_array($arquivos)) {
		foreach($arquivos as $arquivo) {
			if(file_exists($arquivo)) {
				$arquivosValidos[] = $arquivo;
			}
		}
	}
	
	if(count($arquivosValidos)) {
		
		$zip = new \ZipArchive();
		$res = $zip->open($destino, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
		if( $res !== true ){ pr("Erro ao criar arquivo zip: " . $res); return false; }
		
		foreach($arquivosValidos as $arquivo) {
			$nome = substr($arquivo, strrpos($arquivo, DIRECTORY_SEPARATOR)+1);
			$zip->addFile($arquivo, $nome);
		}
		
		$zip->close();

		return file_exists($destino);
		
	}else{
		pr("Erro ao criar arquivo zip: Nenhum arquivo selecionado.");
		return false;
	}
	
}

function formatarImagem($pathToImage, $newPathToImage, $thumbWidth = 180, $thumbHeight = 180) {
    $result = 'Failed';
    if (is_file($pathToImage)) {
        
		$info = pathinfo($pathToImage);

        $extension = strtolower($info['extension']);
        if (in_array($extension, array('jpg', 'jpeg', 'png', 'gif'))) {
			
            switch ($extension) {
                case 'jpg':
                    $img = imagecreatefromjpeg("{$pathToImage}");
                    break;
                case 'jpeg':
                    $img = imagecreatefromjpeg("{$pathToImage}");
                    break;
                case 'png':
                    $img = imagecreatefrompng("{$pathToImage}");
                    break;
                case 'gif':
                    $img = imagecreatefromgif("{$pathToImage}");
                    break;
                default:
                    $img = imagecreatefromjpeg("{$pathToImage}");
            }
            // load image and get image size

            $width = imagesx($img);
            $height = imagesy($img);

            // calculate thumbnail size
            $new_width = $thumbWidth;
            $new_height = floor($height * ( $thumbWidth / $width ));

			switch ($extension) {
				 case 'jpg':
				 case 'jpeg':
	    	        // create a new temporary image
    	    	    $tmp_img = imagecreatetruecolor($new_width, $new_height);
					break;
				case 'png':
					imagealphablending($img, true);
					imagesavealpha($img, true);
					break;
			}
	
            // copy and resize old image into new image
            imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				
            // save thumbnail into a file
			switch ($extension) {
               case 'jpg':
                    imagejpeg($img, $newPathToImage);
                    break;
                case 'jpeg':
                    imagejpeg($img, $newPathToImage);
                    break;
                case 'png':
                    imagepng($img, $newPathToImage);
                    break;
                case 'gif':
                    imagegif($img, $newPathToImage);
                    break;
                default:
                    imagejpeg($img, $newPathToImage);
			}
					
            $result = $newPathToImage;
			
        } else {
            $result = 'Failed|Not an accepted image type (JPG, PNG, GIF).';
        }
    } else {
        $result = 'Failed|Image file does not exist.';
    }
    return $result;
}

function hashSenha($senha) {
	$salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
	$salt = base64_encode($salt);
	$salt = str_replace('+', '.', $salt);
	$hash = crypt($senha, '$2y$10$' . $salt . '$');
	return $hash;
}

function checkSenha($senha, $hash) {
	return crypt($senha, $hash) === $hash;
}

?>