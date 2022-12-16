<?php
    session_start();
    require $_SERVER["DOCUMENT_ROOT"] . "/aws/aws-autoloader.php";    
?>
<!DOCTYPE html>
<html>
<head>
<title>Example Upload to S3 in PHP</title>
</head>
<body>

<?php
    if (isset($_SESSION['msg'])):
        echo $_SESSION['msg'];
    endif;
?>
<h1>Upload file to S3</h1>

<form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="text" name="description" />
    <input type="file" name="file" />
    <input type="submit" value="Enviar"/>
</form>

</body>
</html>