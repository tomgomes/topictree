
function ajax_request_montar_arvore_topicos()
{
	var tutorial_id = $('#input_tutorial_id').val();
	
    $.ajax({
        url: "/tutorial/json/tutorial_json_carregar.php",
        async: false,
        type: "post",
        cache: false,
        data: {"query_string": "tutorial_id=" + tutorial_id},
        success: function(data, textStatus) {                      
        	data = resposta_servidor(data);
            
            //Se há mensagem
            if(data.mensagem != ""){
            	mostrar_mensagem(data);            	
                return;
            }//end if
            
            //Monta a árvore de tópicos
        	for (topico_nivel = 0; topico_nivel <= data.nivel_maximo; topico_nivel++) {            	    
                for(var indice in data['arvore_topicos']){             	          
                    topico = data['arvore_topicos'][indice];                	  
                    if(topico.topico_nivel == topico_nivel){
              	        var li = $("<li/>", {id:"li_id_" + topico.topico_id});                	      
              	        $(li).append(topico.topico_nome);
              	        
              	        var ul = $("<ul/>", {id:"ul_id_" + topico.topico_id});
              	        $(li).append(ul);
              	        
              	        if(topico.topico_superior == null){
                            $("#ul_raiz").append(li);                		  
              	        }else{
              	    	    $("#ul_id_" + topico.topico_superior).append(li);
              	        }//end if              	      
                    }//end if               	                                  	  
                }//end for                  
            }//end for
        	
            //ajusta largura                        
            $('#painel_esquerdo').css('width', data.arvore +  'px');
            $('#form_editortexto').css('width', data.editor + 'px');
            
        }, //end success
        error: function(XMLHttpRequest, textStatus, errorThrown) 
        {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
	    }//end error                                        
    });//end ajax     
}//end function

function ajax_request_abrir_topicos()
{
	var tutorial_id = $('#input_tutorial_id').val();
	
    $.ajax({
        url: "/tutorial/json/tutorial_json_carregar.php",
        async: false,
        type: "post",
        cache: false,
        data: {"query_string": "tutorial_id=" + tutorial_id},
        success: function(data, textStatus) {                      
            
            //Se há mensagem já foi dada pela função ajax_request_montar_arvore_topicos()
        	//pois ela já fez a mesma chamada dessa função.
        	
            //Abre os nós
        	var ref_arvore = $("#jstree");
        	for (topico_nivel = 0; topico_nivel <= data.nivel_maximo; topico_nivel++) 
        	{
                for(var indice in data['arvore_topicos'])
                {
                	topico = data['arvore_topicos'][indice];
              	    var id_topico = "#li_id_" + topico.topico_id;
              	    
              	    if(topico.topico_aberto == 'S')
              	    {
              	    	ref_arvore.jstree("open_node", $(id_topico));
              	    }
              	    else
              	    {
              	    	ref_arvore.jstree("close_node", $(id_topico));              	    
              	    }//end if
              	    
        	        //troca o ícone para oculto
              	    if(topico.topico_oculto == 'S')
              	    {
              	    	ref_topico = ref_arvore.jstree(true).get_node(id_topico);
              	    	ref_arvore.jstree(true).set_type(ref_topico, "oculto");
              	    }//end if
                }//end for                  
            }//end for   
        }, //end success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
	    }//end error                                        
    });//end ajax     
}//end function

function ajax_resquest_largura(id)
{
	
	//ver screen.availWidth
	
	//teste idj = '#' + 'form_editortexto';
	idj = '#' + id;
	var largura = $(idj).innerWidth();
	
	var tutorial_id = $('#input_tutorial_id').val();
	
	var elemento;
	if(id == 'painel_esquerdo'){
		elemento = 'arvore';
	}else if(id == 'form_editortexto'){
		elemento = 'editor';
	}//end if
	
	query_string = "tutorial_id=" + tutorial_id 
                 + "&largura=" + largura
                 + "&elemento=" + elemento;
	
    $.ajax({
        url: "/tutorial/json/tutorial_json_largura.php",
        async: true,
        type: "post",
        cache: false,
        data: {"query_string": query_string},        	                 
    	success: function(data, textStatus) {        
    		  data = resposta_servidor(data);
              
              //Se há mensagem
              if(data.mensagem != ""){
            	  mostrar_mensagem(data);            	  
                  return;
              }//end if
              
              //Vai para a página indicada
              if(data.pagina != ""){
                  window.location = data.pagina;
              }//end if	
        },//end sucess
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
	    }//end error                                                
    });//end ajax     
}//end function

function ajax_resquest_renomear(li_id, novo_nome)
{
	var topico_id = li_id.replace('li_id_', ''); 
	
	query_string = "topico_id=" + topico_id 
	             + "&novo_nome=" + encodeURIComponent(novo_nome);
	
    $.ajax({
        url: "/tutorial/json/tutorial_json_renomear.php",
        async: true,
        type: "post",
        cache: false,
        data: {"query_string": query_string},
    	success: function(data, textStatus) {        
    		  data = resposta_servidor(data);
              
              //Se há mensagem
              if(data.mensagem != ""){
            	  mostrar_mensagem(data);            	  
                  return;
              }//end if
              
              //Vai para a página indicada
              if(data.pagina != ""){
                  window.location = data.pagina;
              }//end if	
        },//end sucess
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
	    }//end error                                                
    });//end ajax     
}//end function

function ajax_resquest_criar(topico_superior, topico_id, posicao, hierarquia, topico_selecionado)
{
	var tutorial_id = $('#input_tutorial_id').val();
	var topico_selecionado = topico_selecionado.replace('li_id_', '');
	
	query_string = "topico_id=" + topico_id 
                 + "&topico_superior=" + topico_superior
                 + "&posicao=" + posicao 
                 + "&hierarquia=" + hierarquia
                 + "&topico_selecionado=" + topico_selecionado
                 + "&tutorial_id=" + tutorial_id;
	
    $.ajax({
        url: "/tutorial/json/tutorial_json_criar.php",
        async: true,
        type: "post",
        cache: false,
        data: {"query_string": query_string},
        success: function(data, textStatus) {        	  
        	  data = resposta_servidor(data);
        	  
              //Se há mensagem
              if(data.mensagem != ""){
            	  mostrar_mensagem(data);            	  
                  return;
              }//end if
              
              //Vai para a página indicada
              if(data.pagina != ""){
                  window.location = data.pagina;
              }//end if
              
              $('#jstree').jstree(true).deselect_all();
              $('#jstree').jstree('select_node', '#li_id_' + topico_id);
              
              var nodeSelecionado = $.jstree.reference('#jstree').get_selected(false)[0];
              $("#jstree").jstree(true).edit(nodeSelecionado);
              
        },//end success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
	    }//end error                                                
    });//end ajax    
}//end function

function ajax_resquest_id_novo_topico(li_id)
{
	//Se async for true, topico_id ira retornar sempre null
	//Se async for false, a tela aguarde não irá funcionar
	var tutorial_id = $('#input_tutorial_id').val();
	var topico_superior = li_id.replace('li_id_', '');
    var topico_id = null; 
    
    $.ajax({
        url: "/tutorial/json/tutorial_json_id_novo_topico.php",
        async: false,
        type: "post",
        cache: false,
        data: {"query_string": "topico_superior=" + topico_superior
        	                 + "&tutorial_id=" + tutorial_id},
    	success: function(data, textStatus) {   
    		  data = resposta_servidor(data);
              
              //Se há mensagem
              if(data.mensagem != ""){
            	  mostrar_mensagem(data);
                  return;
              }//end if
              
              //Vai para a página indicada
              if(data.pagina != ""){
                  window.location = data.pagina;
              }//end if
              
              topico_id = data.topico_id;
        },//end success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
	    }//end error                                                
    });//end ajax
    return topico_id;
}//end function

function ajax_resquest_apagar(li_id)
{
	var topico_id = li_id.replace('li_id_', ''); 
	
    $.ajax({
        url: "/tutorial/json/tutorial_json_apagar.php",
        async: true,
        type: "post",
        cache: false,
        data: {"query_string": "topico_id=" + topico_id},
        success: function(data, textStatus) {        
        	  data = resposta_servidor(data);
              
              //Se há mensagem
              if(data.mensagem != ""){
            	  mostrar_mensagem(data);            	  
                  return;
              }//end if
              
              //Vai para a página indicada
              if(data.pagina != ""){
                  window.location = data.pagina;
              }//end if
	
        },//end success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
	    }//end error                                                
    });//end ajax     
}//end function

function ajax_request_salvar()
{
	var nodeSelecionado = $.jstree.reference('#jstree').get_selected(false)[0];
	var topico_selecionado = nodeSelecionado.replace('li_id_', '');
	var topico_conteudo = CKEDITOR.instances.editortexto.getData();
	
	//Remover caracters no-break
	topico_conteudo = topico_conteudo.replace(/&amp;nbsp;/g,"");
	
    $.ajax({
        url: "/tutorial/json/tutorial_json_salvar.php",
        async: true,
        type: "post",
        cache: false,
        data: {"topico_selecionado": topico_selecionado
       	      ,"topico_conteudo": topico_conteudo},
        success: function(data, textStatus) {        	  
        	  data = resposta_servidor(data);
        	  
              //Se há mensagem
              if(data.mensagem != ""){
            	  mostrar_mensagem(data);            	  
                  return;
              }//end if
              
              CKEDITOR.instances.editortexto.getCommand('save').disable();
              
              //Vai para a página indicada
              if(data.pagina != ""){
                  window.location = data.pagina;
              }//end if
              
        },//end success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
	    }//end error                                                	    
    });//end ajax    
}//end function

function ajax_resquest_conteudo(li_id, visitante)
{
	var tutorial_id = $('#input_tutorial_id').val();
	var topico_selecionado = li_id.replace('li_id_', '');
	
    $.ajax({
        url: "/tutorial/json/tutorial_json_conteudo.php",
        async: true,
        type: "post",
        cache: false,        
        data: {"query_string": "topico_selecionado=" + topico_selecionado },
        success: function(data, textStatus) {
        	data = resposta_servidor(data);        	  
        	
            //Se há mensagem
            if(data.mensagem != ""){
            	mostrar_mensagem(data);
                return;
            }//end if
            
            if(visitante){
            	$('#form_editortexto').html(data.conteudo);
            }else{
                //Remover caracters nbsp (nobreak space)
                var topico_conteudo = data.conteudo;
	            topico_conteudo = topico_conteudo.replace(/&amp;nbsp;/g,"");
	            CKEDITOR.instances.editortexto.setData(topico_conteudo);
	            
                //CKEDITOR.instances.editortexto.setData(data.conteudo);	
            }//end if
            
            //Vai para a página indicada
            if(data.pagina != ""){
                window.location = data.pagina;
            }//end if
            
            //Se for visitante atualiza a url
            //if(autor_id != undefined){
            if(visitante){
            	var autor_id = $('#input_hidden_autor_id_para_visitante').val();            	            	
                url_topico = "tutorial.php?aut=" + autor_id + "&tut=" + tutorial_id + "&topico=" + topico_selecionado;
            	window.history.pushState("topico", "", url_topico);
            }else{
            	var autor_id = $('#input_hidden_autor_id').val();            	
                url_topico = "tutorial.php?aut=" + autor_id + "&tut=" + tutorial_id + "&topico=" + topico_selecionado;
            	window.history.pushState("topico", "", url_topico);            	
            }//end if
            
        }, //end success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
	    }//end error                                        
    });//end ajax    
}//end function

function ajax_resquest_abertura_topico(li_id, estado)
{
	var topico_id = li_id.replace('li_id_', ''); 
	
    $.ajax({
        url: "/tutorial/json/tutorial_json_abertura_topico.php",
        async: true,
        type: "post",
        cache: false,
        data: {"query_string": "topico_id=" + topico_id 
        	                 + "&estado=" + estado},
    	success: function(data, textStatus) {        
    		  data = resposta_servidor(data);
              
              //Se há mensagem
              if(data.mensagem != ""){
            	  mostrar_mensagem(data);
                  return;
              }//end if
              
              //Vai para a página indicada
              if(data.pagina != ""){
                  window.location = data.pagina;
              }//end if	
        },//end sucess
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
	    }//end error                                                
    });//end ajax     	
}//end function

function ajax_resquest_ocultar_mostrar(li_id, acao)
{
	var topico_id = li_id.replace('li_id_', ''); 
	
    $.ajax({
        url: "/tutorial/json/tutorial_json_ocultar_mostrar.php",
        async: true,
        type: "post",
        cache: false,
        data: {"query_string": "topico_id=" + topico_id 
        	                 + "&acao=" + acao},
    	success: function(data, textStatus) {        
            data = resposta_servidor(data);
            
            //Se há mensagem
            if(data.mensagem != "")
            {
                mostrar_mensagem(data);
                return;
            }//end if
              
            //Vai para a página indicada
            if(data.pagina != "")
            {
                window.location = data.pagina;
            }//end if
            
            if(acao == 'ocultar')
            {
                //Oculto o tópico selecionado
                ref_arvore = $('#jstree');
                ref_topico_selecionado = ref_arvore.jstree(true).get_selected(true)[0];
                ref_arvore.jstree(true).set_type(ref_topico_selecionado, "oculto");
                
                //Oculto os subtópicos
                for(id_subtopico of ref_topico_selecionado.children_d)
                {
                      ref_subtopico = ref_arvore.jstree(true).get_node(id_subtopico);
                      ref_arvore.jstree(true).set_type(ref_subtopico, "oculto");
                }//end for
            }//end if
            
            if(acao == 'mostrar')
            {
                ref_arvore = $('#jstree');
                ref_topico_selecionado = ref_arvore.jstree(true).get_selected(true)[0];
                ref_arvore.jstree(true).set_type(ref_topico_selecionado, "default");
                
                id_topico_superior = ref_topico_selecionado.parent;
                do 
                {
                    ref_topico_superior = ref_arvore.jstree(true).get_node(id_topico_superior);
                    ref_arvore.jstree(true).set_type(ref_topico_superior, "default");
                    id_topico_superior = ref_topico_superior.parent;
                }
                while (id_topico_superior != "#");
                
            }//end if
        },//end sucess
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
	    }//end error                                                
    });//end ajax     	
}//end function


function ajax_resquest_abertura_tutorial(estado, evento_abrir, evento_fechar)
{
	var tutorial_id = $('#input_tutorial_id').val();
	
    $.ajax({
        url: "/tutorial/json/tutorial_json_abertura.php",
        async: false,
        type: "post",
        cache: false,
        data: {"query_string": "tutorial_id=" + tutorial_id
        	                 + "&estado=" + estado},
    	success: function(data, textStatus) {
    		  
    		  data = resposta_servidor(data);
              
              //Se há mensagem
              if(data.mensagem != ""){
            	  mostrar_mensagem(data);
                  return;
              }//end if
              
              //Vai para a página indicada
              if(data.pagina != ""){
                  window.location = data.pagina;
              }//end if
              
              //Remove evento, caso contrário vai ocorrer um evento para cada nó
              $("#jstree").unbind("open_node.jstree");
              $("#jstree").unbind("close_node.jstree");
              
              //Fecha ou abre TODOS os nós 
    		  if(estado == 'S'){
    		      $("#jstree").jstree("open_all");
    		  }else if(estado == 'N'){
    			  $("#jstree").jstree("close_all");
    		  }//end if
    		  
    		  //Reativa evento
    		  $("#jstree").bind("open_node.jstree", evento_abrir);
    		  $("#jstree").bind("close_node.jstree", evento_fechar);
        },//end sucess
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
	    }//end error                                                
    });//end ajax     	
}//end function

function ajax_resquest_mover(data)
{
	var topico_id = data.node.id.replace('li_id_', '');
	var topico_ordem = data.old_position;
	var topico_superior_destino = data.parent.replace('li_id_', ''); 
    var topico_ordem_destino = data.position;    	
	var tutorial_id = $('#input_tutorial_id').val();
    
    query_string = "topico_id=" + topico_id 
                 + "&topico_ordem=" + topico_ordem
                 + "&topico_superior_destino=" + topico_superior_destino
                 + "&topico_ordem_destino=" + topico_ordem_destino 
                 + "&tutorial_id=" + tutorial_id;
    
    $.ajax({
        url: "/tutorial/json/tutorial_json_mover.php",
        async: true,
        type: "post",
        cache: false,
        data: {"query_string": query_string},
        success: function(data, textStatus) {        	  
        	  data = resposta_servidor(data);
        	  
              //Se há mensagem
              if(data.mensagem != ""){
            	  mostrar_mensagem(data);
                  return;
              }//end if
              
              //Vai para a página indicada
              if(data.pagina != ""){
                  window.location = data.pagina;
              }//end if                                          
        },//end success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
	    }//end error                                                
    });//end ajax    
}//end function























