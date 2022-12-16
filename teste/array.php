<!DOCTYPE html>
<html>
    <head>
        <title>Topic Tree - SGC - Sistema de Gerenciamento de Conteúdo</title>
        <?php
        require "../include/meta.inc";
        require "../include/bootstrap.inc";        
        ?>        
        <script type="text/javascript">            
            $(document).ready(function () {            	                
                $("#button_testar").click(function (event) {                    
                    ajax_request_teste();
                });                
            });        
        </script>                
    </head>
    <body>        
    	
        <form action="" method="post" id="form_teste">	        
          	<button type="button" class="btn btn-primary" id="button_testar">Testar</button>          	
        </form>            
        <span id="span_resposta"></span>
    </body>
    
    <script type="text/javascript">            
    function ajax_request_teste(){
        $.ajax({
            url: "array_json.php"
            , async: true
            , type: "post"
            , cache: false
            , data: {"query_string": $('#form_teste').serialize()}
            , complete: function(data){                  
                  var resposta = eval("(" + data.responseText + ")");              
                  $("#span_resposta").html(resposta.mensagem);
                  
              }//end complete
        });//end ajax       
    }//end function
    </script>
                    
</html>

