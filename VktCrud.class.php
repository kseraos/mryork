<?php
/*

Verifica a acao

	3 - Salva(Insere ou Edita), Deleta, Lista

Se for  Salvar
	Chega a as tabelas
	2 checa e foramta os campos
	da os bind nos campos
	
	perepara o sql
	excuta
	
	retorna
		Sucesso : true ou false
		Dados :Objeto Interio (id campos e agregados)
		Resorno
				Programacao
					PHP
					BD
	
	

*/


class VktCrud {
	
	
	public $table;

    public $charset='utf8';
    private static $instance;
	
	// Array de Tabelas para Sincronismo.
    private $arrTabelasSync = array(
		'bacco_abertura_caixa'
		, 'bacco_abertura_fechamento'
		, 'bacco_comanda'
		, 'bacco_comanda_itens'
		, 'bacco_caixa_valores'
		, 'usuario'
	);
	
	public function getInstance(){
		if( !isset(self::$instance) || !preg_match("/^(SET)/", $this->charset) ){
			try {
				
				if(strpos(strtolower($this->charset), "utf8") > -1){
					$this->charset = 'SET NAMES utf8';
				}else{
					$this->charset = 'SET NAMES latin1';
				}
				
				self::$instance = new PDO('mysql:host='.DB_HOST.';port='.DB_PORTA.';dbname='.DB_BANCO, DB_USUARIO, DB_SENHA, array(PDO::MYSQL_ATTR_INIT_COMMAND => $this->charset));
				self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
				$clienteVekttor = self::q(" SELECT timezone FROM bacco_configuracao WHERE vkt_id = '".__VKT_ID."' LIMIT 1 ");
				$clienteVekttor = $clienteVekttor['dados'][0];
				date_default_timezone_set($clienteVekttor['timezone']);
				self::$instance->query("SET time_zone = '".date("P")."'");
				
			}catch(PDOException $e){
				echo "<div class='errophp'>";
				pr("nao conectou");
				pr($e->getMessage());
				echo "</div>";
			}
		}
		return self::$instance;
	}
	public function close(){
		if(!isset(self::$instance)){
			try {

				self::$instance= null;
			}catch(PDOException $e){
				echo "<div class='errophp'>";
				pr($e->getMessage());
				echo "</div>";
			}
		}
		return self::$instance;
	}
	
	public function prepare($sql){
            //echo "<pre>";
		return self::getInstance()->prepare($sql);
	}

	public function query($sql){
		try {
			return self::getInstance()->query($sql);
		}catch(PDOException $e){
			echo "<div class='errophp'>";
			pr($e->getMessage());
			echo "</div>";
		}
	}

	public function buscarTodos($sql){
		try{
			$s = self::query($sql)	;
			return $s->fetchAll();
		}catch(PDOException $e){
				echo "<div class='errophp'>";
				pr($sql);
				pr($e->getMessage());
				echo "</div>";
		}
	}
	
	public function listaTodos($filetro, $params = NULL){
		
		if( $params == NULL )
			$filtro = strlen($filetro) > 0 ? " WHERE ".$filetro : NULL;
		else
			$filtro .= $params;
		
		
		$sql = "SELECT * FROM `$this->table` $filtro";
		
		//pr($sql);
		
		if(__DEBUG__==1 && $_GET[sql]==1){
			echo $sql;
		};
		try{
			$s = self::query($sql)	;
			if($s){
				$results=  $s->fetchAll();
				
				$result['registros'] = count($results); 
				$result['dados'] = $results; 
				return $result;
			}else{
				$erro = "erro $sql";
				echo $erro;
				return $erro;
			}
			
		}catch(PDOException $e){
				echo "<div class='errophp'>";
				pr($sql);
				pr($e->getMessage()). "$sql";
				echo "</div>";
		}
	}
	
	public function q($sql){
		// pr($sql);
		// pr($this);
		if(__DEBUG__==1 && $_GET[sql]==1){
			echo $sql;
		};
		try{
			$s = self::query($sql);
			if($s){
				$results=  $s->fetchAll();
				
				$result['registros'] = count($results); 
				$result['dados'] = $results; 
				return $result;
			}else{
				$erro = "erro $sql";
				echo $erro;
				return $erro;
			}
			
		}catch(PDOException $e){
				echo "<div class='errophp'>";
				pr($sql);
				pr($e->getMessage()). "$sql";
				echo "</div>";
		}
	}
	public function q1($sql){
		
		$retorno = $this->q($sql);
		$retorno['dados'] = $retorno['dados']['0'];
		return $retorno;
	}
	public function Retorna1($id){
		$sql = "SELECT * FROM $this->table WHERE id='$id'";
		if(__DEBUG__==1 && $_GET[sql]==1){
			echo $sql;
		};
		try{
			$s = self::query($sql)	;

			$result['dados'][] = $s->fetch();
			
			return $result;
		}catch(PDOException $e){
				echo "<div class='errophp'>";
				pr($sql);
				echo $sql;
				pr($e->getMessage());
				echo "</div>";
		}
	}
	
	public function delete($tabela,$id){
		try{
			$sql = "DELETE FROM $tabela WHERE id='$id' ";

			$s = self::query($sql)	;
			return $s;
		}catch(PDOException $e){
				echo "<div class='errophp'>";
				pr($sql);
				pr($e->getMessage());
				echo "</div>";
		}
	}
//crud
	
	public function lista($id){
		$where = $id>0 ? "WHERE id =:$id" : NULL;
		
		$sql = "SELECT * FROM $this->table $where ";	
		//pr($sql);
		$stmt = self::prepare($sql);
		$stmt->bindParam(":id",$id, PDO::PARAM_INT);
		
		$stmt->execute();
		
		return $id>0 ? $stmt->fetch()  : $stmt->fetchAll() ;
		
	}
	
	// public function deletar($id){
    //         $sql = "DELETE FROM $this->table ";		
    //         $stmt = self::prepare($sql);
    //         $stmt->bindParam(":id",$id, PDO::PARAM_INT);

    //         $stmt->execute();

    //         return $id>0 ?  $stmt->fetchALL() : $stmt->fetch() ;
	// }
	// public function deletar2($id,$vkt_id){
    //         $sql = "DELETE FROM $this->table ";		
    //         $stmt = self::prepare($sql);
    //         $stmt->bindParam(":id",$id, PDO::PARAM_INT);

    //         $stmt->execute();

    //         return $id>0 ?  $stmt->fetchALL() : $stmt->fetch() ;
	// }
	protected function checa_tabela($tabela_teste){
	
	$sql = "show tables ";		
	$stmt = self::prepare($sql);		
	$stmt->execute();
	$dados = $stmt->fetchALL() ;
	foreach($dados as $linha){
				$chama_db = "Tables_in_".DB_NAME;
				if($linha->$chama_db == $tabela_teste){
					return true;
				}
				
			}
			//return false;
	   
	}
        
	public function salvar($dados){
            
            /*
             * 1- checa se tem tabela
             * 
             *              
             */
			
			// Verifica se a tabela atual está na lista de tabelas para sincronização e se não existe o parametro "sincronizado".
			if( in_array($this->table, $this->arrTabelasSync) && !isset($dados['sincronizado']) ){
				$clienteVekttor = self::q("SELECT codigo_unidade FROM clientes_vekttor WHERE id = '".__VKT_ID."'");
				$clienteVekttor = $clienteVekttor['dados'][0];
				$dados['filial_id'] = $clienteVekttor['codigo_unidade'];
				$dados['sincronizado'] = '0';
			}
			
            $tabela_info = strlen($this->table) < 1 ? $dados['tabela']  : $this->table ;
            
            $tabela_origem = strlen($this->table) < 1 ? 'input'  : 'php';
            
            $tabela_info= explode(',',$tabela_info);
          //  pr($tabela_info);
            //pr($tabela_info);
            $sub= 0 ;
			if(count($tabela_info)<1){
				return array('erro'=> "Nenhuma tabela econtrada");
				
			}
          
            foreach($tabela_info as $tabela){
                $subs = $sub==0 ? 1 : count($dados["sub".$sub.'_id']);
                            
                for($s=0;$s<$subs;$s++){

                    if($tabela_origem == 'input'){
                        if($this->checa_tabela($tabela)){
                            $this->table = $tabela;
                        }else{
                            return array('erro'=>'tabela não existe('.$tabela.')') ;

                        }
                    }else{

                        $this->table = $tabela;

                    }


                   // echo "(($tabela))";
                    $tabelas_origens[$sub]= $tabela;

                    $sql = " SHOW COLUMNS FROM $this->table  ";	
                    //echo  "$sql";
                    $stmt = self::prepare($sql);		
                    $stmt->execute();
                    $dados_banco = $stmt->fetchALL() ;
                    $chave_dados = array_keys($dados);
                    $campos = array();
                    $camposBind= array();
                    $campos2 = array();
                    //pr($chave_dados);
                    //pr($dados_banco);
                    //echo"--- aqui --- ";

                    //pr($camposBind);
                    $subinfo = $sub>0 ? "sub".$sub.'_': NULL ;// caso seja subquery
                    
                    if($sub>0){
                        
                       $campos['id'] = $tabelas_origens[0]."_id"."= :".$tabelas_origens[0]."_id"."";/// subsitituir por id do registro
                       $camposBind['id'] = $sub;
                       $campos2['id'] = $tabelas_origens[0]."_id"." = '".$sub."',\n"; // subistituir sub por id do origen
                   
                    }

                    
                    //pr($this->table);
                   // pr($dados_banco);
                    foreach($dados_banco as $linha){

                        //echo $subinfo.$linha->Field."<br>";
						// pr($chave_origem);
						$campo=  $linha['Field'];
						$chave_origem = $subinfo.$campo;
						
						//pr($chave_origem);
						//pr($chave_dados);
                            if(in_array($chave_origem,$chave_dados) && ($campo!='id' || $dados['forcar_insercao'] == '1')){								
								$valor = $sub<1  ? $dados[$campo] : $dados[$subinfo.$campo][$s];
								$valor = is_array($valor) ? implode(',',$valor) : $valor;
                                $campos2[$campo] = "$campo = '".$valor."'\n";
								if($dados['compress'] == $campo){
                                	$campos[$campo] = "$campo = compress('".addslashes($valor)."')\n";
									$campos2[$campo] = "$campo = compress('".addslashes($valor)."')\n";
								} else {
									$campos[$campo] = "$campo = :$campo";
									$camposBind[$campo] = $valor;
								}
                           }
                    }
					
					if(count($campos)<1){
						return array('erro'=> "Nao foram encontrados os campos");
						
					}

                    $sql_inicio = $dados['id'] < 1 || $dados['forcar_insercao'] == '1' ? " INSERT INTO $this->table SET \n\t" : " UPDATE $this->table SET \n\t" ;

                    $sql_meio   = implode("\n\t,", $campos);
					
                    $sql_meio2   = implode("\n\t,", $campos2);
					
					//if($_GET['sql']==1 && __DEBUG__==1){
					//	pr($sql_meio2);
					//}
                    
                    //echo "(($subinfo.'id'))";
                    
                    $id  = $sub<1 ? $dados['id']  : $dados[$subinfo.$linha->Field][$s];
                     
                    $sql_fim    = $id > 0 && $dados['forcar_insercao'] != '1' ? "\n WHERE id='$id' " : NULL ;

					
                    $sql  =  $sql_inicio. $sql_meio. $sql_fim;
                    $sql2  =  $sql_inicio. $sql_meio2. $sql_fim;
					if($dados['debug']==1){
						pr($dados);
						pr($sql2);
					}
					
					try {				
	                    $stmt = self::prepare($sql);
						foreach($camposBind as $campo => $valor){
							$stmt->bindValue(":$campo", (string) $valor);
						}
						if(!$stmt->execute()){
							return array('erro','Não pode executar alterar_tabela'.$this->table);
						}else{
							return $dados['id']>0 ?$dados['id']: self::getInstance()->lastInsertId();	
						}
					} catch(\PDOException $e) {
						echo "Erro: ".$e->getMessage().'<br>'.$sql2;
					}
                } 
             $sub++;    
            }    
		
	}
	
	function exibe ($resultado,$dados){
		 if($dados['json']=='n'){
			 pr($resultado);
		 }else{
			$resultado['dominio'] =  $resultado['dominio'] ?  $resultado['dominio'] : $dados['bind']['dominio'] ;
			$resultado['dominio'] =  strlen($resultado['dominio'])>0 ? $resultado['dominio'] : $dados['dominio'];

//			$resultado['dados'] = utf8_encode($resultado['dados']);
		 	echo json_encode($resultado);	
		}
	}
	
}



?>