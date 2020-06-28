<?php 
    include 'db_connection.php'; // Подключение к бд с помощью PDO
    include 'function.php'; // библиотека функций 

    
    // При поподании на страницу index проверяем установлены ли куки и если да то, проверяем их с БД
    if(isset($_COOKIE['pws'])&&isset($_COOKIE['us'])){
        $login = $_COOKIE['us'];
        $password = $_COOKIE['pws'];
        // Проверка логина
        $check_login =  $pdo->prepare('SELECT count(*) FROM`users` WHERE `login`=:login ');
        $check_login->execute(['login' => $login]); // отправка подготовленого запроса вернет true если запрос сработал
        $res_check_login = $check_login->fetchColumn();// смотрим результат по колонке  если select ничего не нашел будет пусто
        // Проверка пароля               
        $check_pass = $pdo->prepare('SELECT count(*) FROM`users` WHERE `password`= :pasd ');
        $check_pass->execute(['pasd' => $password]);// отправка подготовленого запроса вернет true если запрос сработал
        $res_check_password = $check_pass->fetchColumn();// смотрим результат по колонке  если select ничего не нашел будет пусто
        if($res_check_login && $res_check_password){
            session_start();
            $_SESSION['login'] = $login;
        }else{
            // Если куки установлены но не найдено совпадение с БД переводим человека на регистрацию
            header('Location: signup.php');
        }
    }else{
        // Если куки не установлены переводим человека на страницу входа
        header('Location: enter.php');        
    }
        
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="exit.php" method="POST">
        <?php 
            echo "<h1>Здравствуйте $login </h1>";  
        ?> 
        <input type="submit" name="exit" value="Выйти">
    </form>
    <ul class="list-unstyled">
        <li><a href="./en.php">Английская раскладка</a></li>
        <li><a href="./en.php">Русская раскладка</a></li>
    </ul>  
</body>
</html>