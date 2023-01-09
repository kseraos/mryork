<?php

//namespace cadastroCliente;


class  cadastroClienteCtrl extends cadastroCliente {

  
    function buscarTelefone($dados){
		
		$resposta = parent::buscarTelefone($dados); // metodo do \dominio
		
        parent::exibe($resposta,$dados);// metodo do \vkt.class.php
		
	}

    function GerarCodigoTelefone($dados){
		
		$resposta = parent::GerarCodigoTelefone($dados); // metodo do \dominio
		
        parent::exibe($resposta,$dados);// metodo do \vkt.class.php
		
	}
    

    function listacheckin1($dados){
		
		$resposta = parent::listacheckin1($dados); // metodo do \dominio
		

        parent::exibe($resposta,$dados);// metodo do \vkt.class.php
		
	}

    function listar($dados){
		
		$resposta = parent::listar($dados); // metodo do \dominio /// colocar filtro de sql
		
		parent::exibe($resposta,$dados);// metodo do \vkt.class.php
	}

    function retornaCheckin($d){


    }
    function listaPetsDeTutor($d){
        parent::exibe( parent::listaPetsDeTutor($d),$dados);
        
    }
    function listaPet($d){
        parent::exibe(parent::listaPet($d),$d);
        
        
    }
  
	function salvar($c){

        $retorno = parent::SalvarTutor($c);
        $c['cliente_id'] = $retorno; 
        $comanda['cliente_id'] =  $c['cliente_id'];
        
        $c['dog_id'] = parent::SalvarDog($c);

        $c['checkin_id']  = parent::SalvarCheckin($c);
        if(!$c['id'] > 0 ){
            $comanda['checkin_id'] =  $c['checkin_id'];
            $c['id'] =  $c['checkin_id'];
            $itens_salvos = parent::SalvarItens($c);
             $comanda = $c;
            $c['comanda_id']  = parent::SalvarComanda($comanda);
        }
//		if($retorno>0){
//			
//			// Se salva consulta os dados salvos para repreenchimento do formulário
//			
//			$dados_retorno = parent::lista1($retorno);	
//			$dados_retorno = $dados_retorno['dados'];	// recebe os dados pega o rpimeiro registro para ser colocado no registro
//
//			$dadosr['action'] 	= $dados[id]> 0 ? 'up' : 'in';
//			$dadosr['doominio'] 	= $dados['bind']['dopminio'];
//			$dadosr['sucesso'] 	= 'true' ;
//			$dadosr['dados'] 	=  $dados_retorno;
//
//		}else{
//			$dadosr['dominio'] = $bind['dominio'];
//			$dadosr['sucesso'] = 'false' ;
//			$dadosr['retorno'] = $retorno ;
//		}
//		
		echo parent::exibe($c,$c);// metodo do \vkt.class.php
		
	}
	
	function deleta($dados){
		$retorno = parent::deletar($dados['id']);
		echo parent::exibe($retorno,$dados);// metodo do \vkt.class.php
		
	}

}


$Ctrl =  new cadastroClienteCtrl();


$method =$_REQUEST["action"];

if($method){
		$cadastroClienteC =  call_user_func_array(array($Ctrl,$method),array($_REQUEST));
}
