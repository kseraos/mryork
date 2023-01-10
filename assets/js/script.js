        codigoValidacao = 0;
        db_cliente  = [];
        db_dogs  = [];
        //ACÃO DE CLICK
          $(document).ready(function(){
            $('section').not($('#section-inicio')).hide();

          }); 

            $(document).on("click", "#section-cadastro .avanca", function(e) {

            $('#section-cadastro').hide();
            $('#section-semcadastro-semdog').show();
            
          
            });
            $(document).on("click", "#section-semcadastro-semdog .avanca", function(e) {

              $('#section-semcadastro-semdog').hide();
              $('#section-agenda').show();
              
            
              });



              $(document).on("click", "#section-cadastro-com-dog .teste", function(e) {

                
                dogsselecionados = [];

                $('#section-cadastro-com-dog').hide();
                $('#section-agenda').show();


              })

              $(document).on("click", "#section-semcadastro-semdog .voltar", function(e) {

                $('#section-cadastro').show();
                $('#section-semcadastro-semdog').hide();
                
              
                });

           
               

              $(document).on("click", "#section-cadastro .voltar", function(e) {

                $('#section-cadastro').hide();
                $('#section-inicio').show();
                
              
                });

                $(document).on("click", "#section-agenda .voltar", function(e) {

                  $('#section-agenda').hide();
                  $('#section-semcadastro-semdog').show();
                  
                
                  });

                  $(document).on("click", ".button-adiciona", function(e) {
  
                                      $('#section-cadastro-com-dog').hide();
                                      $('#section-semcadastro-semdog').show();
                                    
                  });

                  


                $(document).on("click", "#section-semcadastro-semdog .avanca", function(e) {

                  
      
                  $('#section-semcadastro-semdog').hide();
                  $('#section-agenda').show();
                  
                
                  });


                  function deletar_entrada() {
                    codigoDigitado = $("#codigo_telefone").val();
                    // console.log(codigoValidacao);
                    // console.log(codigoDigitado);
                        if(codigoDigitado == codigoValidacao){
                          $('#section-com-cadastro').hide();
                          $('#section-cadastro-com-dog').show();
                        }else{
                          alert("codigo errado")
                        }

                      };

                  // ----- 

                  /// Ocultar/Mostrar endereço 

                  $(document).ready(function(){

                    $('#cep').hide();
                    $('#endereco').hide();
                    $('#numero').hide();
                    $('#bairro').hide();
                    $('#complemento').hide();
              
                  });
      
                  $(document).on("click", "#taxdog", function(e) {
      
                    if (this.checked) {
                      
                      
                     
                      $('#cep').show();
                       $('#cep').focus();
                      $('#endereco').show();
                      $('#numero').show();
                      $('#bairro').show();
                      $('#complemento').show();
                  } else{
                     $('#cep').hide();
                    $('#endereco').hide();
                    $('#numero').hide();
                    $('#bairro').hide();
                    $('#complemento').hide();
                  }
                    });

                    // ----------

                    // Gerar codigo Telefone

                    $(document).on("click", ".gerarcodigo", function(e) {
                
                      codigoValidacao = getRandomIntInclusive(1000,9999);
                      alert(codigoValidacao);
                      $("#codigo_telefone").text(codigo_telefone);
                    //  $.ajax({
                   
                    //    url:"?Ctrl&action=GerarCodigoTelefone&codigotelefone="+ codigoValidacao, 
                    //    type: "GET",
                    //    dataType: 'json',
                      
                       
                    //    success: function (data) {
                        
                            
      
                    //    }
                     
                    //  })
                    
                    });

                    function getRandomIntInclusive(min, max) {
                      min = Math.ceil(min);
                      max = Math.floor(max);
                      return Math.floor(Math.random() * (max - min + 1)) + min;
                    }



                    // -------
                    // Verificar telefone

                    $(document).on("click", ".verificartelefone", function(e) {

                      dogsselecionados=[];
                      datat = $("#telefoneinicial").val();
                       $("#telefone1").val(datat);
                       $("#telefone2").val(datat);
     
                      
                      datat = datat.replace(/[^0-9]/g,'');
                      datat = datat.substr(0,5)+'-'+datat.substr(5,4);
                                        
                     $.ajax({
                       url:"?Ctrl&action=buscarTelefone&telefone="+ datat, 
                       type: "GET",
                       dataType: 'json',
                       
                       success: function (data) {
                       if(data){
                         registros = data.registros *1;
                         
                            $(".nome-expor").html('Olá, ' + data['dados'][0]['nome'])     
                             if(registros > 0){
                              db_cliente  = data['dados'];

                              db_dogs  = data['dogs'];
                              d = db_cliente[0];
                              // console.log(d);
                              $("#cliente_id").val(d.id);
                              $("#telefone1").val(d.telefone1);
                              // $("#telefone2").val(d.telefone2);
                              $("#nome").val(d.nome);
                              $("#casa_numero").val(d.casa_numero);
                              $("#cep").val(d.cep);
                              $("#rg").val(d.rg);
                              $("#cnpj_cpf").val(d.cnpj_cpf);
                              $("#complemento").val(d.complemento);
                              $("#endereco").val(d.endereco);
                              $("#bairro").val(d.bairro);
                              $("#cidade").val(d.cidade);
                              $("#estado").val(d.estado);
                              
                              dogdos = data.dogs.dados;
                                                        
                                                       
                              $.each(dogdos,function(i,v){
                                 
                                  dogsselecionados[v.id] = v                                                        
                                  $('#petsdotor').append("<label><input name='pet_id' type='radio' class='taxdog pet_selecionado' value='"+v.id+"'> "+v.nome_pet+"</label>");
                              })


                               $('#section-inicio').hide();
                               $('#section-com-cadastro').show();
     
                           }else if(registros <= 0){
                               $('#section-inicio').hide();
                               $('#section-cadastro').show();
     
                           }
                     
                     }}});
                   }
                   )
    $(document).on("click", ".pet_selecionado", function(e) {


      selecionado =  $(this).val();
          // console.log(selecionado);
          // console.log(dogsselecionados);
          if(selecionado == "novo" || selecionado == "0"){
              $("#nome_pet").focus();

              $("#nome_pet").val('').css("color","");
              $("#raca").val(0).css("color","");
              $("input[name=sexo]").prop("checked",false).css("color","");
              $("#nascimento").val('').css("color","");
              $("#idade").val(0).css("color","");
              $("input[name=porte]").prop("checked",false).css("color","");
              $("input[name=pelagem]").prop("checked",false).css("color","");
              $("#obs_dog").val('').css("color","");

          }else{
              
              petSelecionado = dogsselecionados[selecionado];
              $("#nome_pet").val(petSelecionado.nome_pet).css("color","rgb(0, 152, 7)");
              $("#raca").val(petSelecionado.raca).css("color","rgb(0, 152, 7)");
              $("input[name=sexo][value='" + petSelecionado.sexo + "']").prop("checked",true).css("color","rgb(0, 152, 7)");
              $("#nascimento").val(petSelecionado.nascimento).css("color","rgb(0, 152, 7)");
              $("#idade").val(petSelecionado.idade).css("color","rgb(0, 152, 7)");
              $("input[name=porte][value='" + petSelecionado.porte + "']").prop("checked",true).css("color","rgb(0, 152, 7)");
              $("input[name=pelagem][value='" + petSelecionado.pelagem + "']").prop("checked",true).css("color","rgb(0, 152, 7)");
              $("#obs_dog").val(petSelecionado.obs_dog).css("color","rgb(0, 152, 7)");
            
      
          }
    
     });


          // ------
                //Mascará telefone
              function mascara(o,f){
                  v_obj=o
                  v_fun=f
                  setTimeout("execmascara()",1)
              }
              function execmascara(){
                  v_obj.value=v_fun(v_obj.value)
              }
              function mtel(v){
                  v=v.replace(/\D/g,""); //Remove tudo o que não é dígito
                  // v=v.replace(/^(\d{2})(\d)/g,"($1) $2"); //Coloca parênteses em volta dos dois primeiros dígitos
                   v=v.replace(/(\d)(\d{4})$/,"$1-$2"); //Coloca hífen entre o quarto e o quinto dígitos
                  return v;
              }
              function id( el ){
                return document.getElementById(el);
              }
              window.onload = function(){
                id('telefoneinicial').onkeyup = function(){
                  mascara( this, mtel );
                }
              }


            // -----
           
             

             
                    
                
       

    // $(document).on('keyup', '#novoCheckIn [name="telefone1"]', function(e) {

    //     if (e.which == KEYBOARD.ENTER) {
    
    //         var campo = $(e.currentTarget),
    //             campoDDD = $('#novoCheckIn [name="ddd1"]'),
    //             data = { busca_auto_complete: campoDDD.val() + "" + campo.val() }
    
    //         if (data.busca_auto_complete.length < 1) return;
    
    //         $('#novoCheckIn input').prop('disabled', true);
    
    //         $.ajax({
    //             url: '../sis/MII/Ctrl?action=buscarCliente',
    //             type: 'GET',
    //             dataType: 'json',
    //             data: data,
    //             success: function(res) {
    //                 if (res && res.dados && res.dados[0]) {
    //                     inserirClienteCheckIn(res.dados[0]);
    //                 } else {
    //                     inserirClienteCheckIn({ ddd1: campoDDD.val(), t1: campo.val() });
    //                     alert2('Número de cliente não cadastrado.');
    //                 }
    //             },
    //             error: function(err) {
    //                 console.log(err);
    //             },
    //             complete: function() {
    //                 $('#novoCheckIn input').prop('disabled', false);
    //             }
    //         });
    
    //     }
    
    // });
    