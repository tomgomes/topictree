
function ajax_resquest_carregar_card()
{
	var card_id = $('#input_card_id').val();
	
    $.ajax({
        url: "card_permissao/json/card_json_carregar.php",
        async: true,
        type: "post",
        cache: false,
        data: { "card_id": card_id},
        success: function(data, textStatus) {    
        	data = resposta_servidor(data);
        	
            //Se há mensagem
            if(data['mensagem'] != ""){
            	mostrar_mensagem(data);            	
            }//end if
            
            if(data.card == null){
            	return;
            }//end if
            
            $("#select_permissao").val(data.card['tutorial_card_permissao']);
            me_senha();
            
            $("#username").val(data.card['tutorial_card_nome']);
            
            $("#input_text_senha_nova").val(data.card['tutorial_card_senha']);
            $("#input_text_senha_repetida").val(data.card['tutorial_card_senha']);
            
        },//end success
        error: function(XMLHttpRequest, textStatus, errorThrown) {      
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
        }//end error                
    });//end ajax     	
}//end function

function ajax_resquest_salvar()
{
    if($("#select_permissao").val() == "TCS")
    {
        var nova = $('#input_text_senha_nova').val();
        var repetida = $('#input_text_senha_repetida').val();
        if(nova == "")
        {
            var data = {mensagem:"Por favor, informe uma senha.",
                        pagina:""};
            mostrar_mensagem(data);
            return;
        }//end if
        
        if(nova != repetida)
        {
            var data = {mensagem:"Validação da senha falhou!<br/>Por favor, repita a senha corretamente.",
                        pagina:""};
            mostrar_mensagem(data);
            return;
        }
        
        var senha = $('#input_text_senha_nova').val();
    }//end if
    
	//serialize() retorna uma query string com os dados do formulário
	//NOTA: sem o caracter de início '?'
	//EXEMPLO: parametro1=valor1&parametro2=valor2
	//ATENÇÃO: JÁ ESTÁ CODIFICADO, OU SEJA, NÃO PRECISA PASSAR PELA FUNÇÃO encodeURIComponent()
	var dados_formulario = $('#form_card').serialize();
	var query_string = "&senha=" + encodeURIComponent(senha);
    
    $.ajax({
        url: "card_permissao/json/card_json_salvar.php",
        async: true,
        type: "post",
        cache: false,
        data: { "query_string": dados_formulario + query_string},
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

//Mostra ou esconde a senha
function me_senha(){
    if($("#select_permissao").val() == "TCS")
    {
        $("#div_senha").show();
    }
    else
    {
        $("#div_senha").hide();
    }//end if
}//end function

