function ajax_request_enviar_dados(){
    $.ajax({
        url: "/email_recuperar/email_recuperar_etapa2_json.php",
        async: true,
        type: "post",
        cache: false,
        data: {"query_string": $('#frm_login').serialize()},
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
            return;            
        },//end success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);
        }//end error
    });//end ajax       
}//end function


