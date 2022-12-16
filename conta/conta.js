function ajax_resquest_carregar(){	
    $.ajax({
        url: "conta/json/conta_json_carregar.php",
        async: true,
        type: "post",
        cache: false,
        //data: { "card_id": card_id},
        success: function(data, textStatus) {    
        	data = resposta_servidor(data);
        	
            //Se há mensagem
            if(data['mensagem'] != ""){
            	mostrar_mensagem(data);            	
            }//end if
            
            $('#input_text_usuario').val(data['autor_usuario']);            
            $('#input_text_nome').val(data['autor_nome']);            
            $('#input_text_sobrenome').val(data['autor_sobrenome']);            
            $('#input_text_datanascimento').val(data['autor_datanascimento']);
            $('#input_text_email').val(data['autor_email']);                                               
        },//end success
        error: function(XMLHttpRequest, textStatus, errorThrown) {      
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
        }//end error                
    });//end ajax     	
}//end function

function ajax_request_salvar(senha){
	destacar_input('input_text_nome');
	destacar_input('input_text_sobrenome');
	destacar_input('input_text_datanascimento');
    
	//Nota: O formulário ao carregar não recebe a senha, pois ela não é armazenada, 
	//      ela é apenas assinada, a assinatura sim é armazenada.
	//      Assim somente o usuário tem conhecimento da senha
	
	var query_string = "nome=" + encodeURIComponent($('#input_text_nome').val())
	                 + "&sobrenome=" + encodeURIComponent($('#input_text_sobrenome').val())
	                 + "&datanascimento=" + $('#input_text_datanascimento').val()
	                 + "&email=" + encodeURIComponent($('#input_text_email').val())
	                 + "&senha=" + encodeURIComponent(senha);
	
    $.ajax({
        url: "conta/json/conta_json_salvar.php",
        async: true,
        type: "post",
        cache: false,
        data: { "query_string": query_string},
        success: function(data, textStatus) {    
        	data = resposta_servidor(data);
        	
            //Se há mensagem
            if(data['mensagem'] != ""){
            	mostrar_mensagem(data);            	
                return;
            }//end if                        
        },//end success
        error: function(XMLHttpRequest, textStatus, errorThrown) {     
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
        }//end error                
    });//end ajax     
}//end function

function destacar_input(id){
	if($('#' + id).val() == ""){
		$('#' + id).addClass('is-invalid');
		$('#' + id).removeClass('is-valid');
	}else{
		$('#' + id).addClass('is-valid');
		$('#' + id).removeClass('is-invalid');
	}//end if
}//end function

