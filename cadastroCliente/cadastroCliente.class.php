<?php



class cadastroCliente extends \Vkt{

    function buscarTelefone($d)
    {
        // sleep(1);
        $busca = $d['telefone'];
        //$busca = str_replace("-",'%',$busca);

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

    //Gerar Comanda Balção
    function SalvarComanda($d)
    {
        $pedido = $d;
        $pedido['id'] = $d['id'];
        $retorno = array();
        // Se não existir id para o pedido, lança um erro.
        if (!(strlen($pedido['id']) > 0)) {
            throw new \Exception('Não há pedido para o id informado.');
        }
        // Selecionando pedido já foi cadastrado.
        $comanda = parent::q("
            SELECT id
            FROM bacco_comanda
            WHERE vkt_id = '" . __VKT_ID . "' AND integracao_id = '" . $pedido['id'] . "' AND integracao_tipo = 'MrYork' ");
        $comanda = $comanda['dados'][0];
        // Se o pedido já foi cadastrado, retorna sucesso.

        if ($comanda['id'] > 0) {
            throw new \Exception('Pedido já cadastrado anteriomente.');
        }
        // Retornando o caixa aberto.
        $Bacco = new \modulos\Bacco\Bacco;
        $caixa = $Bacco->reornaCaixaAberto();
        $caixa = $caixa['dados'];

        // Verificando se há caixa aberto.
        if (!($caixa['id'] > 0)) {
            throw new \Exception('Não há caixa aberto para recebimento do pedido.');
        }

        // Instanciando a model Clientes.
        $Clientes = new \modulos\administrativo\clientes\clientes;

        $ddd = $pedido['customer']['phoneCode'];
        $telefone = $pedido['customer']['phoneNumber'];
        $telefoneFiltro = substr($telefone, 0, 5) . '-' . substr($telefone, 5, 4);

        // Criando/Atualizando informações do cliente.
        $cliente = $Clientes->q("
			SELECT cf.*
			FROM cliente_fornecedor cf
			WHERE
				cf.cliente_vekttor_id = '" . __VKT_ID . "'
				AND cf.id = '" . $pedido['cliente_id'] . "' ");
        $cliente = $cliente['dados'][0];

        $taxa = $Clientes->q(" SELECT id FROM bacco_taxa_entrega WHERE vkt_id = '" . __VKT_ID . "' AND valor = '" . floatval($d['taxa']) . "' ");
        $taxa = $taxa['dados'][0];
        $taxa = 0;
        $cliente['tipo'] = 'Cliente';
        $cliente['tipo_cadastro'] = 'Físico';
        $cliente['cliente_vekttor_id'] = __VKT_ID;
        $cliente['taxa_entrega'] = $taxa['id'];
        $cliente['status_cliente'] = 'ativo';

        if ($cliente['origem_contato_id'] <= 0) {
            $origem_contato_id = parent::q("SELECT * FROM bacco_origem_contatos as boc WHERE boc.vkt_id = '" . __VKT_ID . "' AND boc.nome LIKE '%MrYork%' ");
            $origem_contato_id = $origem_contato_id['dados'][0];
            if (($origem_contato_id > 0)) {
                $origem_contato_id = $origem_contato_id['id'];
            } else {
                $query = parent::prepare('
			INSERT INTO bacco_origem_contatos
			SET vkt_id = ' . __VKT_ID . ', nome = :nome');
                $query->bindValue(':nome', "MrYork");
                $query->execute();

                $origem_contato_id = parent::q("SELECT * FROM bacco_origem_contatos as boc WHERE boc.vkt_id = '" . __VKT_ID . "' AND boc.nome LIKE '%MrYork%' ");
                $origem_contato_id = $origem_contato_id['dados'][0];
                $origem_contato_id = $origem_contato_id['id'];
            }
        }
        $cliente['origem_contato_id'] = $cliente['origem_contato_id'] <= 0 ? $origem_contato_id : $cliente['origem_contato_id'];
        $cliente['id'] = $Clientes->salvar($cliente);
        // Checando se o cliente foi salvo.
        if (!($cliente['id'] > 0)) {
            throw new \Exception('Erro ao tentar salvar o cliente.');
        }

        // Instanciando a Model Comandas.
        $Comandas = new \modulos\Bacco\Comandas\Comandas;

        // Criando uma nova comanda.
        $comanda = array();
        $comanda['vkt_id'] = __VKT_ID;
        $comanda['bacco_abertura_caixa_id'] = $caixa['id'];
        // orderType = "Balcão";
        $comanda['tipo_comanda'] = 'balcao';
        $comanda['mesa'] = '0';
        // $comanda['status'] = '1';
        // $comanda['integracao_mesa_balcao'] = 2;
        $comanda['sequencia'] = $Comandas->retornaUltimaSequencia($comanda) + 1;
        $comanda['pessoas'] = '1';
        $comanda['nome_cliente'] = $pedido['nome'];
        $comanda['senha_aleatoria'] = $Comandas->retornarSenhaAleatoria();
        $comanda['cliente_fornecedor_id'] = $cliente['id'];
        $comanda['abertura'] = date("Y-m-d H:i:s");
        $comanda['fechamento'] = date("Y-m-d H:i:s");
        $comanda['ultimo_pedido'] = date("Y-m-d H:i:s");

        $comanda['tax'] = 0;
        $comanda['desconto'] = 0;
        $comanda['subtotal'] = $pedido['valor_total'];
        $comanda['total'] = $pedido['valor_total'];

        $comanda['obs'] = 'MrYork: ' . $pedido['id'];
        // $comanda['obs'] .= ($tipoPedido=='delivery'?'DELIVERY - ' .$comanda['sequencia'] ."\n" :"");
        // $comanda['obs'] .= ($tipoPedido=='pickup'?'Retirada no BALCÃO - ' .$comanda['sequencia'] ."\n" :"");
        //Detalhe da Observação  

        // Situação do Pet
        $obsPet = '';
        if ($pedido['no'] == '1') {
            $obsPet .= 'Nó no pelo, ';
        };
        if ($pedido['ouvido'] == '1') {
            $obsPet .= 'Problema no Ouvido, ';
        };
        if ($pedido['unhas'] == '1') {
            $obsPet .= 'Unhas Anormal, ';
        };
        if ($pedido['pele'] == '1') {
            $obsPet .= 'Pele Anormal, ';
        };
        if ($pedido['olhos'] == '1') {
            $obsPet .= 'Olhos Anormal, ';
        };
        if ($pedido['dentes'] == '1') {
            $obsPet .= 'Dentes Anormal, ';
        };
        if ($pedido['pulga'] == '1') {
            $obsPet .= 'Tem pulgas, ';
        };
        if ($pedido['carrapato'] == '1') {
            $obsPet .= 'Tem carrapatos, ';
        };
        if ($pedido['secrecao'] == '1') {
            $obsPet .= 'Tem secreção, ';
        };
        if ($pedido['lesao'] == '1') {
            $obsPet .= 'Tem lessões, ';
        };
        if ($pedido['veterinario'] == '1') {
            $obsPet .= 'Indicação ao Veterinário, ';
        };
        if (!$obsPet == '') {
            $tamanho = strlen($obsPet);
            $obsPet = '-> ' . substr($obsPet, 0, $tamanho - 2) . '.';
            $comanda['obs'] .= "\n" . '#Situação do Pet#' . "\n" .  $obsPet;
        }
        // Objetos Deixados
        $obsObjetos = '';
        if ($pedido['coleira'] == '1') {
            $obsObjetos .= 'Coleira';
        };
        if ($pedido['guia'] == '1') {
            $obsObjetos .= ', Guia';
        };
        if ($pedido['retratil'] == '1') {
            $obsObjetos .= ', Retratil';
        };
        if ($pedido['peitoral'] == '1') {
            $obsObjetos .= ', Peitoral';
        };
        if ($pedido['shampoo'] == '1') {
            $obsObjetos .= ', Shampoo';
        };
        if ($pedido['roupa'] == '1') {
            $obsObjetos .= ', Roupa';
        };
        if ($pedido['escova_de_dente'] == '1') {
            $obsObjetos .= ', Escova';
        };
        if ($pedido['pasta_de_dente'] == '1') {
            $obsObjetos .= ', Pasta de Dente';
        };
        if (!$obsObjetos == '') {
            $tamanho = strlen($obsObjetos);
            $obsObjetos = '-> ' . substr($obsObjetos, 0, $tamanho - 2) . '.';
            $comanda['obs'] .= "\n" . '#Objetos Deixados#' . "\n" .  $obsObjetos;
        }
        // obs_checkin
        $comanda['obs'] .= "\n" . '##Obs. Checkin##' . "\n" . '-> ' . $pedido['obs_checkin'];

        $comanda['usuario_id_atendente'] = __usuario_id;
        $comanda['atendente_id'] = __usuario_id;
        $comanda['integracao_id'] = $pedido['id'];
        $comanda['integracao_tipo'] = 'MrYork';
        $comanda['integracao_taxa_entrega'] = 0;
        //    $comanda['debug'] = 1;
        $comanda['id'] = $Comandas->salvar($comanda);

        if (!($comanda['id'] > 0)) {
            throw new \Exception('Erro ao tentar salvar a comanda.');
        }

        $Pedido = new \modulos\Bacco\Comandas\Itens\Itens;
        $Pedido->table = "bacco_comanda_pedidos";
        $comandaPedido = array();
        $comandaPedido['bacco_comanda_id'] = $comanda['id'];
        $comandaPedido['data_hora'] = date("Y-m-d H:i:s");
        $comandaPedido['id'] = $Pedido->salvar($comandaPedido);

        //Inicio Itens
        $Itens = new \modulos\Bacco\Comandas\Itens\Itens;
        foreach ($d['intens'] as $k => $v) {
            $cItem = array();
            $cItem['vkt_id'] = __VKT_ID;
            $cItem['bacco_comanda_id'] = $comanda['id'];
            $cItem['pedido_id'] = $comandaPedido['id'];
            $cItem['origem_id'] = $k;
            $cItem['status'] = 1;
            $cItem['hora'] = date("Y-m-d H:i:s");
            $cItem['origem_tipo'] = 'produto';
            $cItem['usuario_id'] = __usuario_id;
            $cItem['obs'] = $pedido['obs_item'][$k];
            $cItem['quantidade'] = 1;
            $cItem['valor_u'] = $v;
            $cItem['valor_t'] = $v;
            $itens['id'] = $Itens->salvar($cItem);
            //Controle de Estoque
            if ($config['controlar_estoque'] === '1') {
                $movimento = array();
                $movimento['doc_id'] = $itens['id'];
                $movimento['doc_tipo'] = 'venda';
                $movimento['tipo_almoxarifado'] = 'atendimento';
                $movimento['produto_id'] = $k;
                $movimento['quantidade'] = 1;
                $Estoque->consumirMateriaPrima($movimento);
            }
        }
        $retorno['comanda_id'] = $comanda['id'];
        $retorno['comanda_sequencia'] = $comanda['sequencia'];
        return $retorno;
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
    