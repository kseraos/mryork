<?php



class cadastroCliente extends \Vkt{

    function buscarTelefone($d)
    {
        // sleep(1);
        $busca = $d['telefone'];
        // $busca = str_replace("-",'%',$busca);

        $resultado = parent::q($q = "SELECT id,nome_fantasia as nome,telefone1,telefone2,cep,endereco,casa_numero,complemento,bairro,cidade,estado,rg,cnpj_cpf FROM cliente_fornecedor WHERE telefone1 LIKE '$busca' OR telefone2 = '$busca' LIMIT 1");
        $cliente_id = $resultado['dados'][0]['id'];


        $resultado['dogs'] = parent::q("SELECT * FROM `dogs` WHERE cliente_id= '$cliente_id'");
        $resultado['dogsqtd'] = $resultado[registros];

        return $resultado;
    }

    function GerarCodigoTelefone($d)
    {
        // sleep(1);
        $busca = $d['codigotelefone'];
        
        $resultado = parent::q($q = "SELECT id,nome_fantasia as nome,telefone1,telefone2,cep,endereco,casa_numero,complemento,bairro,cidade,estado,rg,cnpj_cpf FROM cliente_fornecedor WHERE telefone1 LIKE '$busca' OR telefone2 = '$busca' LIMIT 1");
        $cliente_id = $resultado['dados'][0]['id'];

      
        //$busca = str_replace("-",'%',$busca);
        $cliente_id = parent::q($q = "SELECT id,nome_fantasia as nome FROM cliente_fornecedor LIMIT 1");
        $cliente_id = $$cliente_id['dados'][0]['id'];



        $resultado = parent::q($q = "SELECT codigotelefone FROM dogs_checkin WHERE cliente_id = '$cliente_id'");
        $resultado  = $resultado['dados'][0]['codigotelefone'];
        


        $codigo = parent::query("UPDATE dogs_checkin SET codigotelefone = '$busca' WHERE dogs_checkin.cliente_id = '$cliente_id'");


        $resposta['codigo'] =  $resultado;
        return $resposta;
    }


    function listacheckin1($d)
    {
        // lista1::Retorna1  método da class crud
        $d = parent::q($sql = "
                SELECT
                    *,
                    dc.id AS id,
                    fc.nome_fantasia AS nome,
                    fc.id AS cliente_id
                    , d.id AS pet_id
                FROM dogs_checkin AS dc
                LEFT JOIN dogs AS d ON dc.dog_id = d.id
                LEFT JOIN cliente_fornecedor AS fc ON dc.cliente_id = fc.id 
                WHERE 1=1
                AND dc.id='" . $d['id'] . "'
            ");

        $cliente_id = $d['dados'][0]['cliente_id']; 
        $d['dogs'] = parent::q("SELECT * FROM `dogs` WHERE cliente_id= '$cliente_id'");
        $d['dogsqtd'] = $resultado[registros];

        return $d;
    }

    function listar($d)
    {
        //Retorna1 // método da class crud
        // você tbm pode usar o query mas o resulta do a ser enviado é array('registros'=>123,'rados'=>todo o resultado)
        // $registro = parent::listaTodos($filtros);//  retorna um array('registros'=>123,'rados'=>todo o resultado)

        $filtro  = $d['id'] > 0 ? " AND dc.id='" . $d['id'] . "' " : '';
        //Filtrar ultimos 3 dias
		$dia = date('Y-m-d', strtotime("-3 day"));
        $filtro  .= " AND DATE_FORMAT(dc.datahora_inicio_chechin,'%Y-%m-%d') >= '" . $dia . "' ";


        $registro = parent::q($sql = "SELECT
                                        dc.id AS id 
                                        , DATE_FORMAT(dc.datahora_inicio_chechin,'%d/%m/%Y') AS data_hora 
                                        , dc.data_hora_saida AS data_hora_saida 
                                        , TIMEDIFF(now(), dc.datahora_inicio_chechin) AS tempo
                                        , (TIME_TO_SEC( dc.data_hora_saida) - TIME_TO_SEC(dc.datahora_inicio_chechin))/60 AS tempoMinutos
                                        , d.nome_pet AS nome_pet 
                                        , d.id AS id_pet 
                                        , fc.id AS id_tutor 
                                        , fc.razao_social AS nome_tutor 
                                        , fc.telefone1 AS telefone1 
                                        , fc.telefone2 AS telefone2 
                                        , dc.status AS status 
                                    FROM dogs_checkin AS dc
                                    LEFT JOIN dogs AS d
                                    ON dc.dog_id = d.id
                                    LEFT JOIN cliente_fornecedor AS fc
                                    ON dc.cliente_id = fc.id 
                                    WHERE 1=1
                                    $filtro 
                                    ORDER BY dc.id DESC
                                    ");


        return $registro;
    }
    
    function SalvarTutor($d)
    {
        $this->table = 'cliente_fornecedor';

        $d['id'] = $d['cliente_id'];
        $d['nome_fantasia'] = $d['nome'];
        $d['telefone1'] = $d['telefone1'];
        $d['telefone2'] =  $d['telefone2'];
        // $d['telefone1'] = str_replace('-','',$d['telefone1']);
        // $d['telefone2'] =  str_replace('-','',$d['telefone2']);
        $d['nome_contato'] = $d['nome'];
        $d['razao_social'] = $d['nome'];
        $d['cliente_vekttor_id'] = __VKT_ID;
        $d['tipo='] = 'Cliente';
        $d['tipo_cadastro='] = 'Fisico';
        $d['usuario_id'] = __usuario_id;
        // $d['debug'] = 1;
        return parent::salvar($d);
    }
    function SalvarDog($d)
    {
        if ($d['raca'] == 'Adicionar') {
            $this->table = 'dogs_raca';
            $draca['nome'] =  $d['nova_raca'];
            $d['raca'] = parent::salvar($draca);
        }

        if ($d['nome_pet'] == '') {
            return;
        }
        $this->table = 'dogs';

        $d['data_cadastro'] = date('Y-m-d');
        $d['id'] = $d['pet_id'];
        // $d['debug'] = 1;
        return parent::salvar($d);
    }
    function SalvarCheckin($d)
    {   

     
        $this->table = 'dogs_checkin';
        $d['id'] = $d['id'];
        if(!$d['id'] > 0){
            $d['datahora_entrada'] =  date("Y-m-d H:i");
            // data_hora_agendado
            $d['data_hora_agendado'] =  $d['agendamento_data'] . ' ' . $d['agendamento_hora'];
        }else{
            $zerarCheckin = parent::query("DELETE FROM dogs_checkin WHERE id='".$d['id']."'");
            $inserirIdCheckin = parent::query("INSERT INTO dogs_checkin SET ID = '".$d['id']."'");
        }

        //    $d['debug'] = 1;

        return parent::salvar($d);
    }
    function SalvarItens($d)
    {
        $this->table = 'dogs_checkin_itens';

        $itens = 0;
        foreach ($d['intens'] as $k => $v) {
            $ds['id'] = 0;
            $ds['cliente_id'] =  $d['cliente_id'];
            $ds['proficssional_id'] =  $d['proficssional_id'];
            $ds['dog_id'] = $d['dog_id'];
            $ds['checkin_id'] =  $d['id'];
            $ds['produto_id'] = $k;
            $ds['qtd'] = 1;
            $ds['valor_unitario'] = $v;
            $ds['valor_total'] = $v;
            $ds['obs'] = $d['obs_item'][$k];
            $itemsalvo = parent::salvar($ds);
            if ($itemsalvo > 0) {
                $itens++;
            }
        }
        return $itens;
    }

    

    function listaPetsDeTutor($d)
    {
        return parent::q("SELECT * FROM `dogs` WHERE cliente_id= '" . $d['cliente_id'] . "'");
    }
    function listaPet($d)
    {
        return parent::q("SELECT * FROM `dogs` WHERE id= '" . $d['dog_id'] . "'");
    }
    function listaCheckin($d)
    {
        $this->table = 'dogs_checkin';
        $checkinD = parent::Retorna1($d['id']);
        $checkinD = $checkinD['dados'][0];
        pr($checkinD);


        $this->table = 'dogs';
        $dogD = parent::Retorna1($checkinD['dog_id']);
        $dogD = $dogD['dados'][0];
        pr($dogD);

        $this->table = 'cliente_fornecedor';
        $clienteD = parent::Retorna1($checkinD['cliente_id']);
        $$clienteD = $$clienteD['dados'][0];
        pr($$clienteD);


        $itensD = parent::q("SELECT * FROM `dogs_checkin_itens` WHERE checkin_id= '" . $checkinD['id'] . "' ");
        pr($itensD);
    }


    function salvar($dados)
    {

        //		$retorno = parent::salvar($dados);
        //		
        //		return 	$retorno;

    }


    function deletar($id)
    {


        //		if(parent::delete($this->table,$id)){
        //			
        //			$dadosr['action'] = 'del';
        //			$dadosr['sucesso'] = 'true' ;
        //			$dadosr['dados'] =  $dados_retorno;
        //		}else{
        //			$dadosr['action'] = 'del';
        //			$dadosr['sucesso'] = 'false' ;
        //			$dadosr['retorno'] = 'Erro ao deletar o arquivo' ;
        //		}
        //
        //
        //		return $dadosr;

        }
    }
    