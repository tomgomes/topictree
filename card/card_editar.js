
function ajax_resquest_carregar_card()
{
	var card_id = $('#input_card_id').val();
	
    $.ajax({
        url: "card/json/card_json_carregar.php",
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
            
        	$('#input_card_nome').val(data.card['tutorial_card_nome']);
        	
        	//$('#div_card_modelo').html(data.card['tutorial_card_texto']);
        	CKEDITOR.instances['div_card_modelo'].setData(data.card['tutorial_card_texto']);
        	
        	$('#input_card_ordem').val(data.card['tutorial_card_ordem']);
        	

        	if(data.card['tutorial_card_cor'] == 'imagem')
        	{
        	    $('#input_url_imagem').val(data.card['tutorial_card_personalizada']);
        	    trocar_imagem();
        		//$('#div_card_modelo').css('background-image', 'url(' + data.card['tutorial_card_personalizada'] + ')');
        	}
        	else
        	{
        	    var nome_radio = "#radio_" + data.card['tutorial_card_cor'];
        	    $(nome_radio).attr('checked',true);
        	    
        	    var cor = data.card['tutorial_card_cor'];
        		carregar_imagem(cor);
        	}
        	
        },//end success
        error: function(XMLHttpRequest, textStatus, errorThrown) {      
        	mostrar_erro(XMLHttpRequest, textStatus, errorThrown);        	
        }//end error                
    });//end ajax     	
}//end function

function carregar_imagem(cor)
{
    if(cor == 'imagem')
    {
        trocar_imagem();
        return;
    }
    
	var url_imagem = 'expositor/resources/card_' + cor + '.png';	
	$('#div_card_modelo').css('background-image', 'url(' + url_imagem + ')');
}//end function

function trocar_imagem()
{
    var url_imagem = $('#input_url_imagem').val();
    
    if(imagem_com_erro(url_imagem))
    {
        if($('#radio_imagem').prop('checked'))
        {
            $('#radio_cinza').prop('checked', true);
            carregar_imagem("cinza");
            $('#div_card_imagem').css('background-image', "");
        }
        $('#input_url_imagem').val('');
        return;
    }
    $('#div_card_imagem').css('background-image', 'url(' + url_imagem + ')');
    $('#div_card_modelo').css('background-image', 'url(' + url_imagem + ')');
    $('#radio_imagem').prop('checked', true);
}

function trocar_imagem3() 
{
    var image = new Image(); 
    $(image).on("load", function() {
        //Certo
        $('#div_card_imagem').css('background-image', 'url(' + url_imagem + ')');
        $('#div_card_modelo').css('background-image', 'url(' + url_imagem + ')');
        $('#radio_imagem').prop('checked', true);
   }).bind('error', function() {
       //Errado
        if($('#radio_imagem').prop('checked'))
        {
            $('#radio_cinza').prop('checked', true);
            carregar_imagem("cinza");
            $('#div_card_imagem').css('background-image', "");
        }
        //$('#input_url_imagem').val('');
        
        var data = new Object();
        data.mensagem = "O link para imagem é inválido!";
        data.pagina = "";
        mostrar_mensagem(data);
  });
  
  var url_imagem = $('#input_url_imagem').val();
  image.src = url_imagem;
}

function imagem_com_erro(url_imagem)
{
    var image = new Image(); 
    image.src = url_imagem;
    return (image.width == 0);
}

function ajax_resquest_salvar()
{
    var titulo = $('#input_card_nome').val();
	if( (titulo == "") || (titulo == "Título")  ){
		$('#input_card_nome').addClass('is-invalid');
		$('#input_card_nome').removeClass('is-valid');
	}else{
		$('#input_card_nome').addClass('is-valid');
		$('#input_card_nome').removeClass('is-invalid');
	}//end if
	
	//serialize() retorna uma query string com os dados do formulário
	//NOTA: sem o caracter de início '?'
	//EXEMPLO: parametro1=valor1&parametro2=valor2
	//ATENÇÃO: JÁ ESTÁ CODIFICADO, OU SEJA, NÃO PRECISA PASSAR PELA FUNÇÃO encodeURIComponent()
	var dados_formulario = $('#form_card').serialize();
	
	var query_string = "&card_texto=" + encodeURIComponent($('#div_card_modelo').html())
	                 + "&card_ordem=" + encodeURIComponent($('#input_card_ordem').val())
	                 + "&personalizada=" + encodeURIComponent($('#input_url_imagem').val());
    $.ajax({
        url: "card/json/card_json_salvar.php",
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

function ajax_resquest_enviar_imagem()
{
    var file_data = $('#input_file').prop('files')[0]; //retorna um [object file]
    var form_data = new FormData();
    form_data.append('upload', file_data);
    
    $.ajax({
        url: '/tutorial/upload.php', // <-- point to server-side PHP script 
        dataType: 'text',  // <-- what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,                         
        type: 'post',
        success: function(php_script_response){
            var resposta = eval("(" + php_script_response + ")");
            if(resposta['uploaded'])
            {
                $('#input_url_imagem:text').val(resposta['url']);
                trocar_imagem3();
            }
            else
            {
                var data = new Object();
                data.mensagem = resposta['error'].message;
                data.pagina = "";
                
                mostrar_mensagem(data);
            }//end if
        }//end sucess
     });//end ajax
}//end function

