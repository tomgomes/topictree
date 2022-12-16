
function ajax_request_confirmar_email(guid){
	
    $.ajax({
        url: "/email_confirmar/email_confirmar_json.php",
        async: true,
        type: "post",
        cache: false,
        data: {"query_string": "guid=" + guid},
        success: function(data, textStatus) {    
        	data = resposta_servidor(data);
        	
            //Se há mensagem
            if(data.mensagem != ""){
            	mostrar_mensagem(data);            	                 
                return;
            }//end if
            
            //Vai para a página indicada
            if(data['pagina']= ""){
                window.location = data.pagina;
            }//end if            
        },//end success
        error: function(XMLHttpRequest, textStatus, errorThrown) {    
            mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
        }//end error
    });//end ajax       
}//end function






