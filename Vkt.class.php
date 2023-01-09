<?PHP
/*

controle de acesso e login

PAGINACAO

MOEDA TO USA

MOEDA TO BR

DATA TO USA

DATA TO BR

CALC DATA


crud basico

(pensar em crud extendido)


getExtensao($file)

Uload progress

Escrever por extenso numero

limitador decimal

removeAcentos

Envia SMS

Envia Email (de acordo com a onfiguraçao padrao)
*/

//namespaces \;

class Vkt extends \VktCrud{

	public $vkt_id = __VKT_ID;
	
	function __construct(){
	}
	
	function setHistoricoUsuario($d){
		$action = preg_split('/(?=[A-Z_])/', $_REQUEST['action'], -1, PREG_SPLIT_NO_EMPTY);
		$action = ucwords(implode(" ", $action));
		$historico = array();
		$historico['tela'] = $_SESSION['telas'][$_SESSION['ultima_tela_id']];
		$historico['acao'] = !empty($d['acao']) ? $d['acao'] : $_REQUEST['action'];
		$historico['data_hora'] = date("Y-m-d H:i:s");
		$historico['dados'] = json_encode($_REQUEST);
		$historico['ip'] = getIP();
		$historico['navegador'] = getBrowser();
		$historico['origem_id'] = $d['origem_id'];
		$historico['origem_tipo'] = $d['origem_tipo'];
		$historico['origem_interacao'] = $d['origem_interacao'];
		$historico['origem_interacao_motivo'] = $d['origem_interacao_motivo'];
		$historico['origem_autorizacao'] = $d['origem_autorizacao'];
		$historico['descricao'] = !empty($d['descricao']) ? $d['descricao'] : $action;
		
		if ( $historico['origem_autorizacao'] > 0 ) {
			$autorizacao = parent::query(" SELECT id, nome FROM usuario WHERE cliente_vekttor_id = '".__VKT_ID."' AND id = '".intval($historico['origem_autorizacao'])."' ");
			$autorizacao = $autorizacao->fetch(\PDO::FETCH_ASSOC);
			$historico['descricao'] = __usuario_nome . ' (autorizado por ' . $autorizacao['nome'] . ') ' . $historico['descricao'];
		} else {
			$historico['descricao'] = __usuario_nome . ' ' . $historico['descricao'];
		}
		
		parent::query("
			INSERT INTO usuario_historico
			SET
				vkt_id = '".__VKT_ID."'
				, usuario_id = '".__usuario_id."'
				, usuario_nome = '".__usuario_nome."'
				, tela = '".$historico['tela']."'
				, acao = '".$historico['acao']."'
				, descricao = '".$historico['descricao']."'
				, data_hora = '".$historico['data_hora']."'
				, dados = '".$historico['dados']."'
				, ip = '".$historico['ip']."'
				, navegador = '".$historico['navegador']."'
				, origem_id = '".$historico['origem_id']."'
				, origem_tipo = '".$historico['origem_tipo']."'
				, origem_interacao = '".$historico['origem_interacao']."'
				, origem_interacao_motivo = '".$historico['origem_interacao_motivo']."'
				, origem_autorizacao = '".$autorizacao['id']."'
		");
	}
	
	function sec_session_start() {
		$session_name = 'sec_session_id';   // Estabeleça um nome personalizado para a sessão
		$secure = SECURE;
		// Isso impede que o JavaScript possa acessar a identificação da sessão.
		$httponly = true;
		// Assim você força a sessão a usar apenas cookies. 
	   if (ini_set('session.use_only_cookies', 1) === FALSE) {
			header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
			exit();
		}
		// Obtém params de cookies atualizados.
		$cookieParams = session_get_cookie_params();
		session_set_cookie_params($cookieParams["lifetime"],
			$cookieParams["path"], 
			$cookieParams["domain"], 
			$secure,
			$httponly);
		// Estabelece o nome fornecido acima como o nome da sessão.
		session_name($session_name);
		session_start();            // Inicia a sessão PHP 
		session_regenerate_id();    // Recupera a sessão e deleta a anterior. 
	}	
	
	/* Controle de acesso */
	function retorna_modulo($modulo_id){
		$t="SELECT 
					m.*
				FROM
					usuario_tipo_modulo as u
						JOIN sis_modulos as m on m.id=u.modulo_id
				WHERE
					u.usuario_tipo_id='".__usuario_tipo_id."'
					
				AND
					m.id='$modulo_id'
				";
		$q=$this->buscarTodos($t);
		return $q;
	}
	/* Controle de acesso */
	function retorna_modulo_geral($modulo_id, $usuario_id = 0){
		//Controla acesso de Modulos pelo Administrador/Financeiro
		// $modulos = __usuario_tipo_id == 8 ? '':' JOIN modulo_administrador as md on md.modulo_id=utp.modulo_id ';
		// print( $vkt_id );exit;
		$modulos = __usuario_tipo_id == 8 ? '':' AND m.vkt_id = 249';
		$filtroUsuario = intval($usuario_id) > 0 ? " AND ( u.id = '".intval($usuario_id)."' OR m.id = 722 ) " : "";
		$t="SELECT m.*
			FROM usuario u
					JOIN usuario_tipo ut ON ut.id = u.usuario_tipo_id
					JOIN usuario_tipo_modulo AS utp ON utp.usuario_tipo_id = ut.id
					
					JOIN sis_modulos AS m ON ( m.id = utp.modulo_id OR m.acao_menu <> 'abre' )
			WHERE
				m.id = '".$modulo_id."'
				$modulos
				$filtroUsuario
			";
		$q=$this->buscarTodos($t);
		return $q;
	}

	function select_menu_pais(){
		
		// como ao habilitar telas para usuario vc nao seleciona os modulos o sistema faz isso, pega os pais dos filhos
		$t="SELECT 
							*
						FROM
							sis_modulos as m
						WHERE
								m.id in (SELECT m2.modulo_id FROM
										  	usuario_tipo_modulo as u2
											JOIN sis_modulos as m2 on m2.id=u2.modulo_id
										 WHERE
											u2.usuario_tipo_id='".__usuario_tipo_id."'
											group by m2.modulo_id
										)
								AND 
								m.modulo_id='0'
						ORDER BY 
							ordem_menu, m.modulo_id, m.nome";
		//echo $t;
		return $this->buscarTodos($t);
		
	}
	
	function select_menu_filhos($modulo_id){
		//Controla acesso de Modulos pelo Administrador/Financeiro
		// $modulos = __usuario_tipo_id == 8 ? '':' JOIN modulo_administrador as md on md.modulo_id=u.modulo_id ';
		// $modulos = __usuario_tipo_id == 8 ? '':' AND m.vkt_id = '.__VKT_ID;
		$modulos = __usuario_tipo_id == 8 ? '':' AND m.vkt_id = 249';
		$t="SELECT 
					m.*
				FROM
					usuario_tipo_modulo as u
					
					JOIN sis_modulos as m on m.id=u.modulo_id
					
				WHERE
					u.usuario_tipo_id='".__usuario_tipo_id."'
					$modulos
				AND
					m.modulo_id='$modulo_id'
				AND
					m.acao_menu<>'interno'
				ORDER BY 
				m.ordem_menu, m.modulo_id, m.nome";
		//pr( $t);
		 $q=$this->buscarTodos($t);
		return $q;
	}

    /*
     * Retorna os acessos do sistema
     * */
	function moedaBrToUsa($valor){

		$nv = str_replace('.','',$valor);
		$nv = str_replace(',','.',$nv);
	
		return @number_format($nv,2,'.','');
	
	}


	function aessos_usuario($modulo_id){
		// provisoriamente soment pelo bd
		if(count($_SESSION['acessos_menu'])<1||1==1){
			
			$q = $modulo_id > 0 ? $this->select_menu_filhos($modulo_id) : $this->select_menu_pais();
			
			$q = count($q) >0 ? $q : array();
			//pr($q);
			//exit();		
			foreach( $q as $r){
				//pr($r);
				$acessos[] = array(
				'id'=>$r[id],
				'pai'=>$r[modulo_id],
				'nome'=>$r[nome],
				'ordem'=>$r[ordem_menu],
				'tela'=>$r[tela],
				'caminho'=>$r[caminho],
				'acao_menu'=>$r[acao_menu],
				'icone'=>$r[icone],
				'versao'=>$r[versao],
				'ativo'=>$r[acao_menu]
					
					,'filhos' => $this->aessos_usuario($r[id])
					);
			}
			if($modulo_id=='0'){
				//$_SESSION['acessos_menu'] = $acessos;
			}
		}else{
			//$acessos = $_SESSION['acessos_menu'];
		}
		return $acessos;
	}
	
	function codeToMessage($code){
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }
	
	function enviaArquivo($pasta,$file,$newfile,$id){
	
			$file_i = array_keys($file);

			if(strlen($file[$file_i[0]]['name'])){
							  

				$files_autorizados = array(
					'jpg',
					'txt',
					'csv',
					'ofx',
					'gif',
					'pdf',
					'pdf',
					'png',
					'xlsx',
					'png',
					'mp3',
					'mp4',
					'doc',
					'docx',
					'pfx',
					'pdf',
					'cdr',
					'jpeg'
				);
				
				$extensao = strtolower(substr($file[$file_i[0]]['name'],-4));
				$extensao = str_replace(".",'',$extensao);
				$arquivo 	= str_replace(".extensao",".".$extensao,$newfile);
				
				if(in_array($extensao,$files_autorizados)){
					
					@unlink($arquivo);
					ciradorDePastas($pasta);
					if(is_dir($pasta)){
						
						if( !is_writable($pasta) ){
							return array(
								'sucesso' => false,
								'retorno' => "Erro de permissao em: " . $pasta
							);
						}
						
						if( $file[$file_i[0]]['error'] > 0 ){
							return array(
								'sucesso' => false,
								'retorno' => "Erro de upload: " . self::codeToMessage($file[$file_i[0]]['error'])
							);
						}
						
						if(move_uploaded_file($file[$file_i[0]]['tmp_name'],$arquivo)){
							return array(
									'sucesso' => true,
									'extensao' => $extensao,
									'retorno' => $arquivo
									
								);
							chmod($arquivo,0755);
							
						}else{
							return array(
									'sucesso' => false,
									'retorno' => "Erro ao fazer upload para: " . $arquivo
								);
							echo "";
						}
					}else{
						return array(
								'sucesso' => false,
								'retorno' => "Sem diretorio".$pasta
								
							);
						echo "";
						exit;
					}
				}else{
					return array(
								'sucesso' => false,
								'retorno' => "Formato de autenticação Inadequado: $extensao"
								
							);
				}
			}
				
	}

	
}