<?php
//require $_SERVER["DOCUMENT_ROOT"] . "/include/php/redirecionar_https.inc";
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Topic Tree - SGC - Sistema de Gerenciamento de Conteúdo</title>
        <?php
        require $_SERVER["DOCUMENT_ROOT"] . "/include/html/meta.inc";
        require $_SERVER["DOCUMENT_ROOT"] . "/include/js/jquery-bootstrap.inc";
        require $_SERVER["DOCUMENT_ROOT"] . "/include/js/jquery-block-ui.inc";
        ?>        
        <link rel="stylesheet" 
              type="text/css" 
              href="/email_confirmar/email_confirmar.css?t=<?php echo filemtime($_SERVER["DOCUMENT_ROOT"] . '/email_confirmar/email_confirmar.css')?>">
        
        <script type="text/javascript" 
                src="/email_confirmar/email_confirmar.js?t=<?php echo filemtime($_SERVER["DOCUMENT_ROOT"] . '/email_confirmar/email_confirmar.js')?>">
        </script>
        
        <script type="text/javascript" 
                src="/js/topictree/topictree.js?t=<?php echo filemtime($_SERVER["DOCUMENT_ROOT"] . '/js/topictree/topictree.js')?>">
        </script>
        
        <script type="text/javascript">   
            $(document).ready(function () {         
                $(document).ajaxStart(function(){ $.blockUI({ message: $('#span_aguarde') }) });
                $(document).ajaxStop($.unblockUI);
                
                ajax_request_confirmar_email("<?php echo $_GET['guid'] ?>");
                
            });//end ready  
        </script>                
    </head>
    <body class="tela">        
        <div class="container">            
            <div align="center">
            <div style="padding: 5% 0">
            <div style="padding: 5% 0">                    
            <div class="row">
                
                <div class="col-xl-4" >
                </div>
                
                <div class="col-xl-4" >                                                            
                </div>
                
                <div class="col-xl-4" >
                </div>
                
                <div id="div_dialog" title="Aviso" style="display:none">
                    <p id="p_dialog">
                    </p>
                </div>
                
                <span id="span_aguarde" style="display:none" class="aguarde position-relative">
                    <img src="../resources/ajax-loader-red.gif" /> aguarde, confirmação do e-mail...
                </span>                
                
            </div>
            </div>
            </div>
            </div>
            
        </div>
        
    </body>
</html>
