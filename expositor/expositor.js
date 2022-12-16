
function ajax_request_montar_lista_tutoriais()
{
	var avisos = {add_card: 0, mover_card: 0};
	
    $.ajax({
        url: "expositor/json/expositor_json_carregar.php",
        async: false,
        type: "post",
        cache: false,
        /*, data: {"query_string": ""}*/
        success: function(data, textStatus) {                      
            data = resposta_servidor(data);
            
            //Se há mensagem
            if(data['mensagem'] != "")
            {
            	mostrar_mensagem(data);            	
                return;
            }//end if
            
            //Vai para a página indicada
            if(data['pagina'] != "")
            {
                window.location = data.pagina;
            }//end if
            
            //Monta a lista de tutoriais, ou seja, os cards
            for(var indice in data['tutoriais'])
            {
            	//lê objeto
                o = data['tutoriais'][indice];
                
                avisos.add_card = o.autor_aviso_add_card;
                avisos.mover_card = o.autor_aviso_mover_card;
                
                var li_new = $('<li/>');
                $(li_new).attr('id', 'card_id_' + o.tutorial_id);
                //$(li_new).attr('display', 'block');    
                //$(li_new).attr('position', 'relative');    
                //$(li_new).attr('margin', 'auto');
                $(li_new).addClass('ui-state-default');
                
                var div_new = $('<div/>');
                $(div_new).addClass('card');
                
                var img_new = $('<img/>');
                $(img_new).addClass('sort-handle');
                $(img_new).css('display', 'none');
                
                if(o.tutorial_card_permissao == "SE")
                {
                    //fantasma - para permissao SE - Somente Eu
                    var icone_codigo = "&#xf6e2"; //&#xf005
                    var icone = "<span style='font-family: context-menu-icons;color:white;float:right;display:inline;position:absolute;background-color:black;font-size:80%'>"
                              + icone_codigo + "</span>";
                    $(li_new).html(icone);
                }
                else if(o.tutorial_card_permissao == "TCS")
                {
                    //cadeado - para permissao TCS - Todos Com a Senha 
                    var icone = "<span style='font-family: context-menu-icons;color:white;float:right;display:inline;position:absolute;background-color:black;font-size:65%'>&#xf023;</span>"
                    $(li_new).html(icone);
                }//end if
                
                $(li_new).append(div_new);
                
                if (o.tutorial_card_cor == 'imagem')
                {
                    if(o.tutorial_card_personalizada != null)
                    {
                  	    $(div_new).css('background-image', 'url(' + o.tutorial_card_personalizada + ')');                  	    
                    }//end if                	
                }//end if
                else
                {
                    var url_imagem = 'expositor/resources/card_' + o.tutorial_card_cor + '.png';
                    $(div_new).css('background-image', 'url(' + url_imagem + ')');
                }//end if
                
                /*
                if (o.tutorial_card_tipo == 'pre_definida'){
                    if(o.tutorial_card_predefinida != null){
                  	    $(div_new).css('background-image', 'url(' + o.tutorial_card_predefinida + ')');
                    }//end if
                }//end if
                
                if (o.tutorial_card_tipo == 'personalizada'){                    
                    if(o.tutorial_card_personalizada != null){
                  	    $(div_new).css('background-image', 'url(' + o.tutorial_card_personalizada + ')');                  	    
                    }//end if                	
                }//end if
                */
                
                //$(img_new).addClass('sort-handle');
                //$(div_new).append(img_new);
                
                $(div_new).html(o.tutorial_card_texto);
                $(div_new).append(img_new);
                
                $('#grade').append(li_new);                  
            }//end for    
        }, //end success
        error: function(XMLHttpRequest, textStatus, errorThrown) 
        {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
	    }//end error    
    });//end ajax
    
    return avisos;    
}//end function

function ajax_resquest_id_novo_tutorial(mover)
{
	//Se async for true, topico_id ira retornar sempre null
	//Se async for false, a tela aguarde não irá funcionar
    
    $.ajax({
        url: "expositor/json/expositor_json_id_novo_tutorial.php",
        async: true,
        type: "post",
        cache: false,
        /*, data: {"query_string": "topico_superior=" + topico_superior}*/
        success: function(data, textStatus) {   
        	data = resposta_servidor(data);
        	
            //Se há mensagem
            if(data['mensagem'] != ""){
            	mostrar_mensagem(data);            	
                return;
            }//end if
            
            location.reload();
            
        },//end success        
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);
        }//end error        
    });//end ajax        
}//end function

function ajax_resquest_apagar(card_id)
{
	//Se async for true, topico_id ira retornar sempre null
	//Se async for false, a tela aguarde não irá funcionar
    
    $.ajax({
        url: "expositor/json/expositor_json_apagar.php",
        async: true,
        type: "post",
        cache: false,
        data: {"query_string": "card_id=" + card_id},
        success: function(data, textStatus) {   
        	data = resposta_servidor(data);
        	
            //Se há mensagem
            if(data['mensagem'] != ""){
            	mostrar_mensagem(data);            	
                return;
            }//end if
            
            var seletor = '#card_id_' + card_id;
            $(seletor).remove();
            
            //Vai para a página indicada
            if(data['pagina'] != ""){
                window.location = data.pagina;
            }//end if
            
        },//end success        
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);
        }//end error        
    });//end ajax        
}//end function

function ajax_resquest_contar(aviso)
{
	//Se async for true, topico_id ira retornar sempre null
	//Se async for false, a tela aguarde não irá funcionar
    
    $.ajax({
        url: "expositor/json/expositor_json_contar_aviso.php",
        async: true,
        type: "post",
        cache: false,
        data: {"query_string": "aviso=" + aviso},
        success: function(data, textStatus) {   
            //Não avisa nada, esta ação não é tão importante                                    
        },//end success        
        error: function(XMLHttpRequest, textStatus, errorThrown) {        
            //Não avisa nada, esta ação não é tão importante
        }//end error        
    });//end ajax        
}//end function

function ajax_resquest_ordenar()
{
	//Se async for true, topico_id ira retornar sempre null
	//Se async for false, a tela aguarde não irá funcionar
	
	var ids = [];    
	var elementos = $('#grade').children();	    
	for (var i = 0; i < elementos.length; i++){		
	    ids.push(elementos[i].id.replace('card_id_', ''));
	}//end for		
	var dados = JSON.stringify(ids);
	
    $.ajax({
        url: "expositor/json/expositor_json_ordenar.php",
        async: true,
        type: "post",
        cache: false,
        data: {"query_string": "dados=" + dados},
        success: function(data, textStatus) {   
        	data = resposta_servidor(data);
        	
            //Se há mensagem
            if(data['mensagem'] != ""){
            	mostrar_mensagem(data);            	
                return;
            }//end if
                        
            //Vai para a página indicada
            if(data['pagina'] != ""){
                window.location = data.pagina;
            }//end if            
        },//end success        
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
        }//end error        
    });//end ajax        
}//end function

function ajax_request_permissao(card_id, senha)
{
    
	//Se async for true, topico_id ira retornar sempre null
	//Se async for false, a tela aguarde não irá funcionar
    
    $.ajax({
        url: "expositor/json/expositor_json_permissao.php",
        async: true,
        type: "post",
        cache: false,
        data: {"query_string": "card_id=" + card_id
                             + "&senha=" + encodeURIComponent(senha)
        },
        success: function(data, textStatus) 
        {
        	data = resposta_servidor(data);
        	
            if(data['mensagem'] == "pedir_senha")
            {
                $("#username").val(data['tutorial_card_nome']);
                $('#modalSenha').modal('show');
            }
            else if(data['mensagem'] != "")
            {
                //Se há mensagem
            	mostrar_mensagem(data);            	
                return;
            }//end if
            
            //Vai para a página indicada
            if(data['pagina'] != "")
            {
                window.location = data.pagina;
            }//end if
        },//end success        
        error: function(XMLHttpRequest, textStatus, errorThrown) 
        {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);
        }//end error        
    });//end ajax        
}//end function

