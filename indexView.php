
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <link  href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <title>agendamento</title>
   
    <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://momentjs.com/downloads/moment.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link rel='stylesheet' href='//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.1/fullcalendar.min.css' />
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;500;600&display=swap" rel="stylesheet">
    <script src="assets/js/bind.js"></script>
    <script type="text/javascript" src="assets/js/script.js"></script>
    
    <script src='//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.1/fullcalendar.min.js'></script>
    

</head>
<body>

   <!-- Estrutura formulario

        logo
        numero
        botão de iniciar  -->

        
        <section id="section-inicio">
        <header>
            <nav class="nav" >
                <div class="area-principal">
                    <span class="material-icons icon-menu">menu</span>
                    <img src="assets/img/logo.jpeg" alt="" class="logo">
                    <span class="material-icons">account_circle</span>
                </div>
            </nav>
        </header>

            <article class="text">
                <header class="titulo">
                    <h1>Agendamento</h1>
                </header>
                   
            </article>

            <div class="form form_vkt formcheckin">
                <div class="telefone-input">
                        <label for="telefone" class="telefone"> Telefone </label>
                        <input type="tel" name="telefoneinicial"  id="telefoneinicial" class="telefone-input-input" placeholder="Insira seu telefone" maxlength="10" pattern="\(\d{2}\)\s*\d{5}-\d{4}" required>
                </div>
    
                    <div class="button-inicia botao-submit verificartelefone">Avançar</div>
            </div>


        </section>


        <form dominio='formcadastro' method="POST" action="?Ctrl">
        <input type="hidden" id="cliente_id" name="cliente_id"  >
            <input type="hidden" name="Ctrl" value="1">
        <section id="section-cadastro" class="section" id="conteudo">
            <header>
                <nav class="nav" >
                    <div class="area-principal">
                        <span class="material-icons icon-menu">menu</span>
                        <img src="assets/img/logo.jpeg" alt="" class="logo">
                        <span class="material-icons">account_circle</span>
                    </div>
                </nav>
            </header>
            <div class="voltar">Voltar</div>
                <article class="text">
                    <header class="titulo titulo-cadastro">
                        <h1>Agendamento</h1>
                    </header>
                       
                </article>


             <div class="form">
                <div class="dados-input">
                    <label for="telefone">Telefone</label>
                    <input type="tel" placeholder="Telefone" id="telefone1" name="telefone1" > 

                </div>
                <div class="dados-input">
                    <label for="Nome">Nome
                    <input type="text" name="nome" id="nome" placeholder="Nome" require></label>
                </div>
                <div class="dados-input" id="email">
                    <label for="email">Email
                    <input type="email" name="email" placeholder="Email"></label>
                </div>
                
                <div class="dados-input tax-dog" style="flex-direction: row">
                    <input type="checkbox" name="taxdog" class="taxdog" id="taxdog">
                    <label>Vai precisar de Taxi Dog?</label>   
                </div>
                <div class="div-endereco">
                <div class="dados-input">
                    <!-- <label for="cep">CEP</label>   -->
                    <input type="text" name="cep" id="cep" placeholder="CEP">
                    <small id='aguardecep'  style="display: none">Aguarde ...</small>

                   <script> $("#cep").keyup(function(){
                            cepd = $(this).val();
                            cepd = cepd.replace(/[^0-9]/g,'');
                            tamanhocep =cepd.length
                           //https://viacep.com.br/ws/69025-370/json/?_=1670549258415
                            if(tamanhocep == 8){
                                $("#aguardecep").show()
                                $.getJSON( "https://viacep.com.br/ws/"+cepd+"/json/?"+cepd, 
                                function( data ) {
                                    console.log()
                                    $("#endereco").val(data.logradouro)
                                    $("#bairro").val(data.bairro)
                                    $("#cidade").val(data.localidade)
                                    $("#estado").val(data.uf)
                                    $("#casa_numero").focus();
                                    $("#aguardecep").hide();

                                })
                            }
                            
                        })
                        </script>
                    
                </div>
                <div class="dados-input ">
                    <!-- <label for="rua">Rua</label>   -->
                    <input type="text" name="endereco" id="endereco" placeholder="Enderço">
                    
                </div>
                <div class="dados-input">
                    <!-- <label for="numero">N°</label>   -->
                    <input type="text" name="numero" id="numero" placeholder="N°">
                    
                </div>
                <div class="dados-input">
                    <!-- <label for="bairro">Bairro</label>   -->
                    <input type="text" name="bairro" id="bairro" placeholder="Bairro">
                    
                </div>
                <div class="dados-input">
                    <!-- <label for="complemento">Complemento</label>   -->
                    <input type="text" name="complemento" id="complemento" placeholder="Complemento">
                    
                </div>
                    </div>
     

            
                <div  class="button-inicia botao-submit avanca">Avançar</div>
            
        </section>

        <section id="section-com-cadastro" class="section">
            <header>
                <nav class="nav" >
                    <div class="area-principal">
                        <span class="material-icons icon-menu">menu</span>
                        <img src="assets/img/logo.jpeg" alt="" class="logo">
                        <span class="material-icons">account_circle</span>
                    </div>
                </nav>
            </header>
                <article class="text">
                    <header class="titulo titulo-cadastro">
                        <h1>Agendamento</h1>
                    </header>
                       
                </article>

            <div action="" class="form">
                <div class="nome-expor">Olá, Kristine</div>
                <p class="p-aviso">Insira o codigo que enviamos para o seu numero</p>
                <div class="dados-input tax-dog">

                    <!-- <label for="telefone">Fred</label> -->
                    <input type="text" name="telefone" id="telefone2" placeholder="Telefone" readonly>
                    
                </div>
                <div  class="gerarcodigo">Gerar codigo</div>
                <div class="dados-input tax-dog">
                    <!-- <label for="telefone">Codigo</label> -->
                    <input type="tel" name="codigo_telefone" id="codigo_telefone" placeholder="Insira o codigo" >
                   
                </div>

            
            <div class="button-inicia botao-submit avanca" onclick="deletar_entrada()">Avançar</div> 

               

                    </div>

        </section>

        <section id="section-cadastro-com-dog" class="section">
            <header>
                <nav class="nav" >
                    <div class="area-principal">
                        <span class="material-icons icon-menu">menu</span>
                        <img src="assets/img/logo.jpeg" alt="" class="logo">
                        <span class="material-icons">account_circle</span>
                    </div>
                </nav>
            </header>
            <div class="voltar">Voltar</div>
                <article class="text">
                    <header class="titulo titulo-cadastro">
                        <h1>Agendamento</h1>
                    </header>
                       
                </article>

            <div action="" class="form">
                <div class="nome-expor">Olá, Kristine</div>
                <p class="p-aviso">Selecione o pet para qual deseja fazer o agendamento</p>

                <script>
                
                $(document).on("click", "#section-com-cadastro .avanca", function(e) {
                                telefoneinicial = $("#telefoneinicial").val();
                                dogsselecionados = [];
                                $.ajax({
                                    url:"?Ctrl&action=buscarTelefone&telefone="+telefoneinicial, 
                                    type: "GET",
                                    dataType: 'json',
                                    success: function (data) {
                                        
                                        if(data){
                                            registros = data.registros *1;
                                            
                                            if(registros>0){
                                                
                                                d = data.dados
                                                d = d[0];                                        
                                                
                                              registros = data.dogsqtd *1;
                                                if(registros>0){

                                                    
                                                    if(data.dogs.registros > 0 ){
                                                        
                                                        dogdos = data.dogs.dados;
                                                        
                                                       
                                                        $.each(dogdos,function(i,v){
                                                           
                                                            dogsselecionados[v.id] = v                                                        
                                                            $('#petsdotor').append("<label><input name='dog_id' type='checkbox' class='taxdog pet_selecionado' value='"+v.id+"'> "+v.nome_pet+"</label>");
                                                        })
                                                           
                                                    }else{
                                                        $('#petsdotor').hide();
                                                    }
                                                }

                                            }else{
                                            }


                                        }
                                    },
                                    error: function(e){	
                                        console.log(e)
                                    }
                                }).done(function(){
                                    
                                    //  notificX($('#buscando')); 
                                });
                                    
                                    
                            
                            
                        })
                    </script>


                
                <div class="dados-input tax-dog" style="flex-direction: column" id='petsdotor'>
                    
                </div>
                <div class="dados-input tax-dog" style="flex-direction: row">
                    <div class="button-adiciona button-inicia" value="novo">Adicionar novo pet</div>
                </div>

                        <div  class="button-inicia botao-submit avanca teste">Avançar</div>
               

                    </div>

        </section>
        
        <section id="section-semcadastro-semdog">
            <header>
                <nav class="nav" >
                    <div class="area-principal">
                        <span class="material-icons icon-menu">menu</span>
                        <img src="assets/img/logo.jpeg" alt="" class="logo">
                        <span class="material-icons">account_circle</span>
                    </div>
                </nav>
            </header>
            <div class="voltar">Voltar</div>
                <article class="text">
               
                    <header class="titulo titulo-cadastro">
                    
                        <h1>Perfil do Pet</h1>
                        
                    </header>
                       
                </article>

            <div action="" class="form">
            
                <div class="nome-expor">Olá, Kristine</div>
                    <p class="p-aviso">Precisamos de algumas informações do seu pet.</p>
                    
                    <div class="dados-input">
                        <input type="text" id="nome" name='nome_pet' placeholder="Nome do seu PET" >
                    </div>

                    <div class="dados-input">
                                <strong>Raça</strong>
                                

                                <div class="select">
                                <select id="standard-select" name="raca">
                                        <option value="0" >Selectione uma raça</option>
                                        <?
                                        $raca= $Vkt->q("SELECT id, nome, ordem FROM dogs_raca ORDER BY dogs_raca.ordem ASC");
                                        foreach($raca['dados'] as $k => $v){
                                            extract($v);
                                        ?>
                                        <option value="<?=$id?>"><?=$nome?></option>
                                        <?
                                        }
                                        ?>
                                        </select>
                                
                                </div>
                     </div>
        

                    <div class="dados-input">
                        <strong>Sexo</strong>
                     <div class="class-ck">
                        <label for="sexo-macho" class="label-checkbox">
                            <input type="radio" name="sexo" value='m' id="sexo-macho" class="checkbox">Macho
                        </label>
                        <label for="sexo-femea" class="label-checkbox">
                            <input type="radio" name="sexo" value='f' id="sexo-femea" class="checkbox">Femea
                        </label>
                     </div>
                    </div>

        

                <div class="dados-input">
                <strong>Nascimento</strong>
                <label for="telefone" class="label-checkbox" style="font-size: 7px;">
                     <input type="checkbox" name="telefone" id="telefone" class="checkbox">Não sei</label>
                    
                    <input type="date" id="nascimento" name='nascimento' placeholder="__/__/____" >
                </div>

                
                <div class="dados-input">
                <strong>Idade</strong>
                    <input type="tel" id="idade" name='idade' placeholder="Idade" >
                    
                </div>
                
                

                <div class="dados-input">

                <strong>Porte</strong>
                    <div class="class-ck">
                        <label for="mini" class="label-checkbox" >
                        <input type="radio" name="porte" value='mini' id="mini" class="checkbox"> Mini</label>
                        <label for="medio" class="label-checkbox">
                        <input type="radio" name="porte" value='medio' id="medio" class="checkbox">Médio</label>
                        <label for="grande" class="label-checkbox">
                        <input type="radio" name="porte" value='grande' id="grande" class="checkbox">Grande
                        </label>
                    </div>
                </div>
              
                <div class="dados-input">
                <strong>Pelagem</strong>
                <div class="class-ck">
                        <label for="curta" class="label-checkbox">
                        <input type="radio" name="pelagem" value='curta' id="curta" class="checkbox"> Curta
                        <span></span>
                        </label>
                        <label for="media" class="label-checkbox">
                        <input type="radio" name="pelagem" value='media' id="media" class="checkbox">Média
                        <span></span>
                        </label>
                        <label for="longa" class="label-checkbox">
                        <input type="radio" name="pelagem" value='longa' id="longa" class="checkbox">Longa
                        <span></span>
                        </label>
                        <label for="dupla" class="label-checkbox">
                        <input type="radio" name="pelagem" value='dupla' id="dupla" class="checkbox"> Dupla
                        <span></span>
                        </label>
               
                </div>
                </div>


                <div class="dados-input">

                <label for="story"><strong> Alguma Obervação?</strong> </label>
                    <textarea id="story" name="obs" style="font-size:7px;"
                            rows="3" cols="5">
                    </textarea>
                    
                </div>
               

                <div  class="button-inicia botao-submit avanca">Salvar</div>


                                </div>

        </section >


        <section id="section-agenda">
            <header>
                <nav class="nav" >
                    <div class="area-principal">
                        <span class="material-icons icon-menu">menu</span>
                        <img src="assets/img/logo.jpeg" alt="" class="logo">
                        <span class="material-icons">account_circle</span>
                    </div>
                </nav>
            </header>
            <div class="voltar">Voltar</div>
                <article class="text">
                    <header class="titulo titulo-cadastro">
                        <h1>Agendamento</h1>
                    </header>
                       
                </article>

            <div action="" class="form">
                <div class="nome-expor">Olá, Kristine</div>
                <p class="p-aviso">Selecione o pet para qual deseja fazer o agendamento</p>
                <div class="dados-input">


                <div class="dados-input">

                        <select id="standard-select" name="proficssional_id">
                        <option value="0" >Selectione um profissional</option>
                        <?
                        $profissional= $Vkt->q("SELECT id, nome FROM usuario");
                        foreach($profissional['dados'] as $k => $v){
                            extract($v);
                        ?>
                        <option value="<?=$id?>"><?=$nome?></option>
                        <?
                        }
                        ?>
                        </select>



</div>



                <div class="dados-input">
                <strong>Data</strong>
                    <input type="date" id="data" name='data' placeholder="Selecione a data" >
                    <!-- <input placeholder="Type Date" type="text" onfocus="(this.type = 'date')"  id="date"> -->
                    
                </div>

                <div class="dados-input">             
                     <table class="container">
                                    <thead>
                                        <tr>
                                    
                                        <th><h1>Horários</h1></th>
                                        <th><h1>Horários</h1></th>
                                        <th><h1>Horários</h1></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                        <td>8h</td>
                                        <td>9h</td>
                                        <td>10h</td>
                                        
                                        </tr>
                                        <tr>
                                        <td>12h</td>
                                        <td>14h</td>
                                        <td>16h</td>
                                        
                                        </tr>
                                        <tr>
                                        <td>17h</td>
                                        <td>18h</td>
                                        <td>18h</td>
                                    
                                        </tr>
                                        <tr>
                                    </tbody>
                                    </table>



              
                
                        <a href="" class="button-inicia botao-submit add-pet">+ Agendar mais um pet</a>
                        <button class="button-inicia botao-submitc concluiragenda" name="action" value="salvar">Avançar</button>
                        
                

               

                                </div>

                   

        </section>

        </form>
</body>     


</html>

