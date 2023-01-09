// Defini template de dominio
template_repeat = [];
data_repeat = [];
form_fields = [];
data_paginacao_total = [];

/*
Ex:
	povoa = clientes_vekttor:RKT/Ctrl?action=lista1&id=249; "dominio:url"

	povo_tipo = 'html:' "tipo:url" 
	callback = "";
	titulo = "";
	
	db_id = "";
	
	povoador(db_id,dominio,url,povo_tipo,callback,titulo);

*/

function povoador(db_id, dominio, url, tipo, callback, titulo) {

    tipo_p = tipo.split(':');
    povoador_tipo = tipo_p[0];
    povoador_valor = tipo_p[1];
    elemento_id = "[" + dominio + "_id='" + db_id + "']";
    //console.log(elemento_id);
    $(elemento_id).addClass('active');
    modal_attr = {
        modal: 'ajax:' + povoador_valor,
        com_bloqueio: "0",
        arrasta: "1",
        altura: "padrao",
        titulo: titulo,
        modal_tipo: 'ajax',
        modal_valor: povoador_valor
    };

    notific('', 'az', 'i-spinner animate-spin', 'povoamento', 1);

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: { dominio: dominio },
        success: function(data) {
            dados_ind_get = data;
            if (povoador_tipo == 'modal_ajax') {
                if (abrindo_modal_ajax == 0) {
                    modal_attr.povoadados = data
                    modal_attr.callback = callback
                    modal(modal_attr);
                } else {
                    notific('Ainda estamos processando o ultimo click, Aguarde', 'lr');
                }
            } else {
                povoador_fields(dominio, data.dados, callback);
            }

            notificX($("#povoamento"));
        },
        error: function() {
            notificX($("#povoamento"));
            alert('erro ao receber dados');
        }
    });

}

function povoador_fields(dominio, dados, callback) {

    var mdados = [];
    cointu = 0;
    form = $("[dominio='" + dominio + "']");
    if (form.attr("dominio") != dominio) {
        alert('nao foi encontrada a tag (dominio="' + dominio + '") em quem tem que ser povoado');
    }
    for (var i in dados[0]) {
        cointu++;
        nome_campo = i;
        key = dados[0][i];
        campo = $("[dominio='" + dominio + "'] [name='" + nome_campo + "']");
        campo2 = $("[dominio='" + dominio + "'] [name='" + nome_campo + "[]']");
        tipo = campo.attr('type');
        if (campo || campo2) {

            if (tipo != 'radio' && tipo != 'checkbox') {
                campo.val(key);
                campo2.val(key);
            }
            if (tipo == 'html') {
                campo.html(key);
                campo2.html(key);
            }
            if (tipo == 'radio') {
                $("[dominio='" + dominio + "'] [name='" + nome_campo + "'][value='" + key + "']").prop('checked', true);
            }
            if (tipo == 'checkbox') {
                dados_checl_box = key.split(',');
                for (i = 0; i < dados_checl_box.length; i++) {
                    $("[dominio='" + dominio + "'] [name*='" + nome_campo + "'][value='" + dados_checl_box[i] + "']").prop('checked', true);
                }
            }
        }


    }

    if (callback) {
        try {
            eval(callback);
        } catch (e) {
            alert('Erro ao executar callback do pov' + callback + e);
        }
    }

}

$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function vkt_bind(t) {

    inputs = $(t).serializeObject();
    dominio = $(t).attr('dominio');
    url = $(t).attr('action');
    callback = $(t).attr('callback');
    inputs.i = '#';
    inputs.bind = {};
    inputs.bind.dominio = dominio;
    inputs.bind.callback = callback;
    inputs.bind.url = url;

    form_fields[dominio] = inputs

    callback = $("[dominio='" + dominio + "']").attr('callback_bind');
    if (callback) {
        try {
            eval(callback);
        } catch (e) {
            alert('erro ao executar o callbackbin');
        }
    }
    eval("dominio = inputs")
    return inputs;

}



/**

Funcoes de upload progress

*/

function uploadFailed(evt) {
    alert("Erro ao enviar o arquivo.");
}

function uploadCanceled(evt) {
    alert("Upload Foi cancelado pelo usuário ou navegador.");
}

function fileSelected(file) {

    if (file) {
        var fileSize = 0;
        if (file.size > 1024 * 1024)
            fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
        else
            fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';

        document.getElementById('fileName').innerHTML = file.name + " - " + fileSize;
        //document.getElementById('fileType').innerHTML = 'Type: ' + file.type;
    }
}

barprogress = '<div class="progress"><div id="progressBar" style="width:0%"></div><section><a id="fileName"></a><span id="progressNumber">0%</span></section></div>';

function uploadProgress(evt) {
    if (evt.lengthComputable) {
        var percentComplete = Math.round(evt.loaded * 100 / evt.total);
        document.getElementById('progressNumber').innerHTML = percentComplete.toString() + '%';
        document.getElementById('progressBar').style.width = percentComplete.toString() + '%';

    } else {
        document.getElementById('progressNumber').innerHTML = 'unable to compute';
    }
}



indexUpload = 0;


function uploadFile(fileSel, url) {

    qtdFiles = fileSel.length;
    //console.log(indexUpload,qtdFiles);
    fileIput = fileSel[indexUpload];



    if ($.isEmptyObject(fileIput) == false) {

        fieldFile = fileIput.field;

        fieldFileName = fieldFile.name;

        file = fileIput.file;

        s = {}
        s.w = $(fieldFile).outerWidth();
        s.h = $(fieldFile).outerHeight();

        $(fieldFile).parent().prepend(barprogress);
        $(fieldFile).hide();


        if (file) {
            var fd = new FormData();
            // coloca o nome do arquivo;
            fileSelected(file);
            // anexa o arquivo
            fd.append(fieldFileName, file);

            xml.upload.addEventListener("progress", uploadProgress, false);
            xml.addEventListener("load", uploadComplete, false);
            xml.onreadystatechange = function() {
                if (xml.readyState == 4) {

                    var texto = xml.responseText;
                    if (texto) {
                        texto = texto.replace(/\+/g, " ");

                        if (texto.length > 10) {
                            resposta = JSON.parse(texto);
                        }
                    }


                    $(fieldFile).show();
                    $('.progress').remove();

                    //console.log(resposta);

                    if (resposta.sucesso == true) {

                        callback = $(fieldFile).attr('callback');
                        if (callback) {
                            callback = callback.replace('arquivo', resposta.retorno);
                            try {
                                eval(callback);
                            } catch (e) {
                                alert('Erro ao executar callback do arquivo ' + callback);
                            }
                        }
                    } else {
                        alert('Erro ao enviar arquivo' + resposta.retorno)
                    }

                    indexUpload++;

                    if (indexUpload < qtdFiles) {

                        uploadFile(fileSel, url);
                    }

                }
            }
            xml.addEventListener("error", uploadFailed, false);
            xml.addEventListener("abort", uploadCanceled, false);
            xml.open("POST", url);
            xml.send(fd);
        }


    }

    function uploadComplete(evt) {
        /* This event is raised when the server send back a response */
        // evt.target.responseText // resposta do texto
        //modal({modal:"alert:Arquivo enviado "});


    }
}

/*  */

function vkt_bind_submit(inputs) {

    vkt_bind_send(inputs, "POST");

}


function vkt_bind_send(inputs, type) {

    // exibe notificao de enviando dados
    notific('', 'az', 'i-spinner animate-spin', 'envia_dodos_bind_noti', 1);

    // muda botao de submit
    $("[dominio='" + inputs.bind.dominio + "'] [type='submit']").removeClass('az').attr('disabled', 'disabled').css('opacity', '0.5');
    $("[dominio='" + inputs.bind.dominio + "'] [type='submit'] i").addClass("i-spinner animate-spin");
    if (!inputs.bind.url) { console.log("url do bind não foi definido."); }
    url = inputs.bind.url + '&type=form'; //""RKT/Ctrl?action=salvar
    url = '?tela_id=' + (url.replace('?', '&'));
    $.ajax({
        url: url,
        type: type,
        dataType: 'json',
        data: inputs,

        success: function(data) {
            //console.log(data);
            dados_ind_get = data
                // Some notificacao
            notificX($("#envia_dodos_bind_noti"));
            // Muda botao
            $("[dominio='" + inputs.bind.dominio + "'] [type='submit']").addClass('vd').removeAttr('disabled').css('opacity', '1');;

            $("[dominio='" + inputs.bind.dominio + "'] [type='submit'] i").removeClass("i-spinner animate-spin");
            // Muda de verde para azul depois de 2 segundos
            setTimeout(function() { $("[dominio='" + inputs.bind.dominio + "'] [type='submit']").addClass('az').removeClass('vd') }, 2000);



            /**
            	se deu sucesso faz o envio do aruivo para levar o id
            	
            	1 - coloca o progressbar no label do arquivo
            	2 -começa o upload
            	ao completar
            	some com o progress 
            */

            field = $("[dominio='" + inputs.bind.dominio + "'] [type='file']");

            qtdFildsFile = field.length;
            filesUploadI = 0
            filesUpload = []
            for (i = 0; i < qtdFildsFile; i++) {
                qtdFiles = field[i].files.length

                for (fi = 0; fi < qtdFiles; fi++) {
                    filesUpload[filesUploadI] = {
                        file: field[i].files[fi],
                        field: field[i]

                    };
                    filesUploadI++;
                }

            }
            /*
            filesUpload =  {
            	file: FIle 
            	field: campo 
            			}
            */

            if (field.length > 0) {
                indexUpload = 0;
                uploadFile(filesUpload, inputs.bind.url + "&action=recebeArquivo&id=" + data.dados[0].id);
            }
            // Reescreve formulario adicionano ou alterando liha
            vkt_bind_retorno(data);

        },
        error: function(err) {
            $("[dominio='" + inputs.bind.dominio + "'] [type='submit']").addClass('vd').removeAttr('disabled').css('opacity', '1');;
            $("[dominio='" + inputs.bind.dominio + "'] [type='submit'] i").removeClass("i-spinner animate-spin");
            notificX($("#envia_dodos_bind_noti"));
            alert('Erro no retorno: ' + err.responseText);
        }
    });
}


function sortByKey(ement, prop, asc) {
    ement = ement.sort(function(a, b) {
        if (asc) return (a[prop] > b[prop]) ? 1 : ((a[prop] < b[prop]) ? -1 : 0);
        else return (b[prop] > a[prop]) ? 1 : ((b[prop] < a[prop]) ? -1 : 0);
    });
    return ement;
}

function paginacao_calc(total, limite, pagina, registros) {
    paginas = Math.ceil(total / limite);
    inicio = ((pagina - 1) * limite) + 1;
    fim = inicio + limite;

    paina_inicio = (Math.ceil(pagina / 10) - 1) * 10;
    paina_inicio = paina_inicio + 1;

    qpaginas = paginas > 10 ? paina_inicio + 10 : paginas;
    qpaginas = qpaginas > paginas ? paginas : qpaginas;

    return { paginas: paginas, inicio: inicio, fim: fim, paina_inicio: paina_inicio, qpaginas: qpaginas }


}

function paginacao_hrml(dominio, paginas, pagiana_atual) {
    pagina = parseInt(paginas);
    pagiana_atual = parseInt(pagiana_atual);
    inner = '';
    pagina_anterior = pagiana_atual > 1 ? pagiana_atual - 1 : 1;
    proxima_pagaina = paginas == pagiana_atual ? pagiana_atual : pagiana_atual + 1;

    inner += "<a class='pagainacao_link paginacao_anterior' vkt_pagina='." + dominio + "' pagina='" + pagina_anterior + "'>Anterior</a>";
    paina_inicio = (Math.ceil(pagiana_atual / 10) - 1) * 10;

    paina_inicio = paina_inicio + 1;

    qpaginas = paginas > 10 ? paina_inicio + 10 : paginas;
    qpaginas = qpaginas > paginas ? paginas : qpaginas;


    for (i = paina_inicio; i <= qpaginas; i++) {

        class_atual = i == pagiana_atual ? ' pagaina_ataual' : '';

        inner += "<a class='pagainacao_link" + class_atual + "' vkt_pagina='." + dominio + "' pagina='" + i + "'>" + i + "</a>";

    }

    if (paginas > 10 && (pagiana_atual - 10) < (paginas - 10)) {
        pula_pagina = pagiana_atual + 10;
        pula_pagina = pula_pagina > paginas ? paginas : pula_pagina;
        inner += "<a class='pagainacao_link' vkt_pagina='." + dominio + "' pagina='" + (pula_pagina) + "'>...</a>";
        inner += "<a class='pagainacao_link pagina_ultima' vkt_pagina='." + dominio + "' pagina='" + (paginas) + "'>Ultima</a>";
    }

    inner += "<a class='pagainacao_link proxima_pagina' vkt_pagina='." + dominio + "' pagina='" + proxima_pagaina + "'>Proxima</a>";

    return inner;

}

function paginacao(dominio, total, limite, pagina, registros) {

    if (!pagina) {
        pagina = 1;
    }

    attrP = paginacao_calc(total, limite, pagina, registros);

    paginacaoI = paginacao_hrml(dominio, attrP.paginas, pagina);
    //vkt_paginacao_informacoes vkt_total_registros


    $("[vkt_paginacao='" + dominio + "']").html(paginacaoI);
    $("[vkt_paginacao='" + dominio + "']").attr('vkt_total_registros', total);
    $("[vkt_paginacao='" + dominio + "']").attr('limite', limite);

}

/*
Funcao que retorna o dados se repeat = 1 ele povoa o repat html, depois executa o callback la dentro da vkt_bind_repeat();
*/
dados_ind_get = '';

function vkt_reapat_get(dominio, url, repeat, callbackbind, callbackget) {
    var nomeNotific = "noti_abre_modal" + Date.now();
    notific('Carregando informações...', 'az', 'i-spinner animate-spin', nomeNotific, 1);

    $('[vkt_repeat=".' + dominio + '"]').html("");

    $.ajax({
        url: url,
        type: 'GET',
        async: true,
        dataType: 'json',
        data: {},
        error: function() {
            alert('Erro ao carregar o arquivo com os dados.');
        }
    }).done(function(data) {

        notificX($("#" + nomeNotific));

        dados_ind_get = data;
        data_repeat[dominio] = data.dados;

        data_paginacao_total[dominio] = data.registros;

        if (repeat == 1) {
            vkt_bind_repeat(dominio, data.dados, callbackget);
        }

        // Se houver callbackbind para executar.
        if (callbackbind) {
            eval(callbackbind);
        }

        // Se houver callbackget para executar.
        if (callbackget) {
            var eventos = String(callbackget).match(/[a-z]+\:/i) || false;
            var funcoes = eventos ? String(callbackget).split(/[a-z]+\:/i) : (callbackget ? callbackget : false);
            if (eventos) {
                funcoes.shift();
                for (var x = 0; x < eventos.length; x++) {
                    eventos[x] = eventos[x].replace(":", "");
                    funcoes[x] = /^function/ig.test($.trim(funcoes[x])) || /^[a-z0-9\_]+$/ig.test($.trim(funcoes[x])) ? "(" + funcoes[x] + "(e))" : funcoes[x];
                    eval('$(document).on("' + eventos[x] + '.vkt_repeat", "[vkt_repeat=\'.' + dominio + '\'] > *", function(e){ ' + funcoes[x] + ' });');
                }
            } else {
                eval('$(document).on("click.vkt_repeat", "[vkt_repeat=\'.' + dominio + '\'] > *", function(e){ ' + funcoes + ' });');
            }
        }

    });
}


function vkt_bind_repeat(dominio, j, callbackget) {

    $('[vkt_repeat=".' + dominio + '"]').html("");
    i = 1;
    limite = $("[vkt_paginacao='" + dominio + "']").attr("limite");

    if (limite) {
        limite = parseInt(limite);

        pagina_atual = parseInt($(".pagaina_ataual").html());
        pagina_atual = pagina_atual > 0 ? pagina_atual : 1;

        paginacao(dominio, data_paginacao_total[dominio], limite, pagina_atual);

        inicia = (pagina_atual - 1) * limite;
        fim = (inicia + limite) > data_paginacao_total[dominio] ? data_paginacao_total[dominio] : (inicia + limite);

        $('[vkt_paginacao_informacoes="' + dominio + '"]').html("Exibindo " + (inicia + 1) + " a <span id='total_registros_tela'>" + fim + "</span> registros de <span id='fim_registros_tela'>" + data_paginacao_total[dominio] + '</span>');

        limita = 1;
    } else {
        limita = 0;
        pagina_atual = 1;
        inicia = 0;
        fim = 0;
    }

    $.each(j, function(index, value) {

        if (limita == 0) {
            value.i = inicia + index + 1;
            value.nome = value.nome;
            vkt_bind_insert(dominio, value, null);
        } else {
            if (index >= inicia && index < fim) {
                mudabind = value;
                mudabind.i = index + 1;
                mudabind.nome = value.nome;
                vkt_bind_insert(dominio, mudabind, null);
            }
        }

        i++;
    });

    $("[vkt_repeat='." + dominio + "']").show();

    // edita_altua_menu();
    refreshtootip();
}

function vkt_bind_insert(dominio, dado, ordem) {
    var dominio = dominio;
    var template_p = template_repeat[dominio];
    template_p = 'template_p = "' + template_p + '"';
    try {
        eval(template_p);
        if (typeof ordem != "undefined" && ordem == "1") {
            $("[vkt_repeat='." + dominio + "']").prepend(template_p);
        } else {
            $("[vkt_repeat='." + dominio + "']").append(template_p);
        }
    } catch (e) {
        console.log('Erro ao executar: ', template_p);
    }
}

function vkt_bind_edit(dominio, dados) {

    var dominio = dominio;
    var template_p = template_repeat[dominio];
    dados.i = $('[' + dominio + '_id="' + dados.id + '"]').attr(dominio + "_i");
    template_p = vkt_bind_bind(dominio, dados);
    template_p = $(template_p).addClass('active');
    $('[' + dominio + '_id="' + dados.id + '"]').replaceWith(template_p);
    refreshtootip();

}

function vkt_bind_retorno(j) {
    if (j.dominio) {

        if (j.sucesso) {



            if (j.action == 'in') {



                for (var t = 0; t < j.dados.length; t++) {
                    j.dados[t].i = fim + 1;
                    fim = j.dados[t].i;

                    var cima = $('[dominio="' + j.dominio + '"]').attr("cima") || null;

                    $('#fim_registros_tela').text(j.dados[t].i);
                    //Muda o id do form
                    //data_repeat[j.dominio] [ (j.dados[t].i) - 1 ] = j.dados[t];
                    $('[dominio="' + j.dominio + '"] [name="id"]').val(j.dados[t].id);

                    vkt_bind_insert(j.dominio, j.dados[t], cima);
                }

                $('[vkt_repeat=".' + j.dominio + '"]').show();
            }

            if (j.action == 'up') {

                vkt_bind_edit(j.dominio, j.dados[0]);


            }


            if (j.sucesso != 'true') {
                modal({ modal: "alert:Erro - " + j.retorno });
                if (j.input_focus) {
                    $("#" + j.input_focus).focus();
                    $("[name=" + j.input_focus + "]").focus();
                }
            } else {
                callback = $("[dominio='" + j.dominio + "']").attr('callback');
            }
        }

        if (callback) {
            try {
                if (j.dados) { dado = j.dados[0]; }
                eval(callback);
            } catch (e) {
                console.log('2 - Erro ao executar o callback do salvar que está no form: ' + callback);
            }
        }

    } else {
        console.log('Dominio não retornou');
    }

    if (j.callback) {
        try {
            if (j.dados) { dado = j.dados[0]; }
            eval(j.callback);
            $('.modal_h .i-cancel').trigger('click');
        } catch (e) {
            console.log('1 - Erro ao executar o callback do salvar que está no form: ' + j.callback);
        }
    }

}



function vkt_bind_deselect(dominio, id) {
    $('[vkt_repeat="' + dominio + '"]').parent().find('.active').removeClass('active');

    $('[' + dominio + '_db="' + id + '"]').addClass('active');
}


function vkt_bind_bind(dominio, dado) {

    template_p = template_repeat[dominio];

    template_p = 'template_p = "' + template_p + '"';

    eval(template_p);

    return template_p;
}


function vkt_bind_template(t) {
    dominio = $(t).attr('vkt_repeat');
    dominio = dominio.replace('.', '');
    clonado = $(t).clone();

    clonado = $(clonado).addClass('vkt_bind_edit');
    clonado = $(clonado).attr(dominio + '_id', '{{dado.id}}');
    clonado = $(clonado).attr(dominio + '_i', '{{dado.i}}');
    clonado = $(clonado).removeAttr('vkt_repeat');
    clonado = $(clonado).removeAttr('action_repeat');
    clonado = $(clonado).removeAttr('callbackget');
    clonado = $("<div>").append($(clonado)).html();

    template_p = clonado.replace(/\n/g, '');
    template_p = template_p.replace(/\r/g, '');
    template_p = template_p.replace(/"/g, '\\"');
    template_p = template_p.replace(/&/g, '&');

    template_p = template_p.replace(/&amp;/g, '&');
    template_p = template_p.replace(/&gt;/g, '>');
    template_p = template_p.replace(/&lt;/g, '<');

    template_p = template_p.replace(/&rt;/g, '>');
    template_p = template_p.replace(/{{/g, '" + (');
    template_p = template_p.replace(/}}/g, ' || "") + "');

    template_repeat[dominio] = template_p;

    $(t).parent().attr('vkt_repeat', '.' + dominio);
    $(t).parent().attr('action_repeat', $(t).attr('action_repeat'));
    $(t).remove();
}




function some_modal_form() {
    $("#modal_id_0").remove();
}

function vkt_bind_remove(db_id, dominio, url, callback) {

    $('[dominio="' + dominio + '"] [vkt_remove*=":"]').find('i').removeClass('i-trash').addClass('i-spinner animate-spin');
    $('[dominio="' + dominio + '"] [vkt_remove*=":"]').attr('disabled', 'disabled');
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: { id: db_id, action: 'deleta' },
        success: function(data) {
            // se sucesso for ok
            //console.log(data)
            if (data.sucesso == 'true') {
                info_remove = '[' + dominio + '_id="' + db_id + '"]';
                ///console.log(info_remove);
                $(info_remove).remove();
                registros = $('#fim_registros_tela').text();
                fim = (registros * 1) - 1;
                $('#fim_registros_tela').text(fim)
                try {
                    eval(callback);
                } catch (callback) {
                    alert("Erro ao executar callback ao remover");
                }
            } else {
                modal({ modal: "alert:" + data.retorno + "" })
            }
            $('[dominio="' + dominio + '"] [vkt_remove*=":"]').find('i').addClass('i-trash').removeClass('i-spinner animate-spin');
            $('[dominio="' + dominio + '"] [vkt_remove*=":"]').removeAttr('disabled');
        },
        error: function() {
            alert('erro ao remover arquivo');
            $('[dominio="' + dominio + '"] [vkt_remove*=":"]').find('i').addClass('i-trash').removeClass('i-spinner animate-spin');
            $('[dominio="' + dominio + '"] [vkt_remove*=":"]').removeAttr('disabled');
        }
    });
}

function buscaObjects(obj, key, val) {
    var objects = [];
    val = val.toLowerCase();
    for (var i in obj) {
        buca_mais = key.split(',');
        o = '';
        for (k = 0; k < buca_mais.length; k++) {

            eval('o += obj[' + i + '].' + buca_mais[k] + ';');

        }
        o = o.toLowerCase();
        if (o.indexOf(val) > -1) {
            objects.push(obj[i]);
        }


    }
    return objects;
}