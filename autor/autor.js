function ajax_request_cadastrar(){
	destacar_input('input_text_nome');
	destacar_input('input_text_sobrenome');
	destacar_input('input_text_datanascimento');
	destacar_input('input_text_email');
	destacar_input('input_text_usuario');
	destacar_input('input_text_senha');
	destacar_input('input_text_senha_repetida');
	
	var query_string = "nome=" + encodeURIComponent($('#input_text_nome').val())
	                 + "&sobrenome=" + encodeURIComponent($('#input_text_sobrenome').val())
	                 + "&datanascimento=" + $('#input_text_datanascimento').val()
	                 + "&email=" + $('#input_text_email').val()
	                 + "&usuario=" + $('#input_text_usuario').val()
	                 + "&senha=" + encodeURIComponent($('#input_text_senha').val())
	                 + "&senha_repetida=" + encodeURIComponent($('#input_text_senha_repetida').val())
	
    $.ajax({
        url: "autor/json/autor_json_salvar.php",
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



