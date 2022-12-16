function resposta_servidor(data){
    /* Normalmente o servidor retorna um texto JSON que precisa ser avaliado, ou seja, interpretado
     * Porém em casos de erros graves é retornado um texto em HTML 
     * Aqui iremos retornar sempre um objeto podendo assim tratar a resposta com menos complexidade */
	try {
		//complete
        //Tenta avaliar (interpretar) a resposta do servidor, normalmente é esperado um texto JSON
        //var resposta = eval("(" + data.responseText + ")");
        
		//Se já é um objeto, então retorna o próprio objeto
		if(typeof data === 'object'){
		    return data;
		}//end if
		
		//Se não é, tentamos converter em um objeto
		var resposta = eval("(" + data + ")");
        
        //Se deu certo, retornamos o objeto convertido 
        return resposta;
    } catch (ex) {
    }//end try
    
    //Se chegou aqui, deu errado, então o parâmetro data (argumento responseText) é um html descrevendo o erro ocorrido
    //Montamos um objeto com as propriedades: mensagem e pagina
    var resposta = {mensagem:"", pagina:""};    
    resposta.mensagem = data;
    return resposta;
}//end function

function mostrar_mensagem(data){
    //Mostra mensagem
    $("#p_dialog").html(data.mensagem);                          	  
    $("#div_dialog").dialog({
        modal: true,
        height: "auto",
        width: "auto",
        buttons:{ "Fechar":function(){ $(this).dialog("close"); }                    
        },//end buttons
        close: function(){
            //Vai para a página indicada
            if(data.pagina != ""){
                window.location = data.pagina;
            }//end if                    	
        }//end close
    });//end dialog	
}//end function

function mostrar_erro(XMLHttpRequest, textStatus, errorThrown){
	//Mostra a mensagem
	$("#p_dialog").html('XML HTTP request response: ' + XMLHttpRequest.responseText
	                   + '<br/>Status: ' + textStatus 
	                   + '<br/>Message: ' + errorThrown.message 
	                   + '<br/>Stack: ' + errorThrown.stack);
	$("#div_dialog").dialog({
	    modal: true,
	    height: "auto",
	    width: "auto",
	    buttons:{
	        "Fechar":function(){$(this).dialog("close");}
	    }//end buttons                 
	});//end dialog              
}//end function

function copiar(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
}

function copiarTexto(texto) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(texto);
    $temp.select();
    document.execCommand("copy");
    $temp.remove();
}

