<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/include/php/restrito.inc";    
    //session_start();
    
    //teste
    $autor_usuario = mb_strtolower($_SESSION['autor_usuario'], 'UTF-8');
    
    //Integração com o CKEDITOR
    $resposta = new stdClass();
    $erro =  new stdClass();
    
    //AWS
    $bucket = "topictree.eti.br";
    $regiao = "sa-east-1";
    $access_key = "????????????????????";
    $secret_key = "????????????????????????????????????????";
    
    //Passei a usar o Composer
    require $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
    //require $_SERVER["DOCUMENT_ROOT"] . "/aws/aws-autoloader.php";
    
    use Aws\S3\S3Client;
    
    try {
        
        if (!isset($_FILES['upload'])) {
            throw new Exception("Falha ao subir o arquivo!", 1);
        }
        $arquivo = $_FILES['upload']['tmp_name'];
        $md5 = md5_file($arquivo);
        $chave = $autor_usuario . '/' . $md5 . '-' . $_FILES['upload']['name'];
        
        //AWS - cria o objeto do cliente, necessita passar as credenciais da AWS
        $clientS3 = S3Client::factory(array(
            'version' => "2006-03-01",
            'region' => $regiao,
            'credentials' => array(
                'key' => $access_key,
                'secret'  => $secret_key
            )            
        ));
        
        //AWS - método putObject envia os dados para o bucket selecionado
        $response = $clientS3->putObject(array(
            'Bucket' => $bucket,
            'Key'    => $chave,
            'SourceFile' => $arquivo
        ));                
        
        $resposta->uploaded = 1;
        $resposta->fileName = $_FILES['upload']['name'];
        $resposta->url = $response['ObjectURL'];
        
    } catch(Exception $e) {
        $erro->message = $e->getMessage();
        $resposta->uploaded = 0;
        $resposta->error = $erro;
    }
    
    //ob_clean();
    ob_end_clean();
    echo json_encode($resposta);       
?>









