<?php    

    $host = 'localhost';
    $db   = 'mnemo_db';
    $user = 'mnemo';
    $pass = '';
    $charset = 'utf8';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";       

    // try {
    //     $pdo = new PDO($dsn, $user, $pass);
    // }catch (PDOException $e){
    //     die('Подключение не удалось: ' . $e->getMessage());
    // }


    try {  
        $pdo = new PDO($dsn, $user, $pass);  
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
      }  
      catch(PDOException $e) {  
          echo "Хьюстон, у нас проблемы.";  
          file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);  
      }

      
      
      
      


?>