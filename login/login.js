function ajax_request_logar(){
    $.ajax({
        url: "login/login_json.php",
        async: true,
        type: "post",
        cache: false,
        data: {"query_string": $('#frm_login').serialize()},
        success: function(data, textStatus) {       
        	data = resposta_servidor(data);
        	
            //Se há mensagem
            if(data.mensagem != ""){
            	mostrar_mensagem(data);            	
                return; //cai fora
            }//end if
            
            //Vai para a página indicada
            window.location = data.pagina;
            return;            
        },//end success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);
        }//end error
    });//end ajax       
}//end function

function ajax_esqueci_senha(){
    $.ajax({
        url: "email_recuperar/email_recuperar_etapa1_json.php",
        async: true,
        type: "post",
        cache: false,
        data: {"query_string": $('#frm_login').serialize()},
        success: function(data, textStatus) {       
        	data = resposta_servidor(data);
        	
            //Se há mensagem
            if(data.mensagem != ""){
            	mostrar_mensagem(data);            	
                return; //cai fora
            }//end if
            
            //Vai para a página indicada
            window.location = data.pagina;
            return;            
        },//end success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);
        }//end error
    });//end ajax       
}//end function


