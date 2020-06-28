<?php 
    include 'db_connection.php'; // Подключение к бд с помощью PDO

  


    $regexp_valid = "/[^a-zA-Z0-9.,@а-яА-Я -_]+/"; // регулярка для проверка входных данных

    // Избовляюсь от нотисов в HTML, т.к. поля после неверного заполения таблицы не стираются и не надо вводить все данные заново
    $result = '';
    $login = '';
    $mail = '';
    $msg = "Поля могут содержать цифры, буквы, сивол - @"; 

    // Если отправилась форма методом POST начинаем проверку данных и заносим в БД. Форма на этой же странице.
    // Если заходить на страницу просто по адресу а не отправкой формы то мы на нее поподаем методом GET
    if($_SERVER['REQUEST_METHOD'] == 'POST' && count($_POST) > 0){        
        // Обработка данных из массива POST
        $login = strtolower(trim($_POST['login']));
        $mail = strtolower(trim($_POST['mail']));
        $password = $_POST['password'];
        $valid_password = $_POST['valid_password'];
        $today = date("Ymd");; 

            // проверка на корректность
            if(((bool)preg_match($regexp_valid, $login)) ||  $login == '' ){
                $msg = "Ошибка, логин $login - не кооректный";
            }elseif(!filter_var($mail, FILTER_VALIDATE_EMAIL) ||  $mail == ''){
                $msg = "Ошибка, $mail - не кооректный";
            }elseif($password != $valid_password){
                $msg = "Пароли не совпадают";
            }elseif(strlen($password) < 8 ||  $password == ''){
                $msg = "Ошибка, пароль не кооректный";
            }else{
                // Проверка на идентичность с БД (что бы пользователь с одими и теми же данными не зарестрировался два раза)

                // Проверка логина
                $check_login =  $pdo->prepare('SELECT count(*) FROM`users` WHERE `login`=:login ');
                $check_login->execute(['login' => $login]); // отправка подготовленого запроса вернет true если запрос сработал
                $res_check_login = $check_login->fetchColumn();// смотрим результат по колонке  если select ничего не нашел будет пусто
                // Проверка почты                
                $check_mail = $pdo->prepare('SELECT count(*) FROM`users` WHERE `mail`= :mail ');
                $check_mail->execute(['mail' => $mail]);// отправка подготовленого запроса вернет true если запрос сработал
                $res_check_mail = $check_mail->fetchColumn();// смотрим результат по колонке  если select ничего не нашел будет пусто

                //  Проверяем что пришло из БД и если все в норме выполнится блок else где данные нового пользователся занесутся в БД
                if($res_check_login){            
                    $msg = "Пользователь с таким именем уже существует";
                }elseif($res_check_mail){
                    $msg = "Пользователь с таким e-mail уже существует";
                }else{
                    // добавить сюда хеширование
                    $hash_pass = password_hash($password, PASSWORD_DEFAULT);
                    // Выполняем insert в БД
                    $reg_user= $pdo->prepare('INSERT INTO `users`(`login`, `password`, `mail`, `date`) VALUES (:login,:password,:mail,:date)');
                    $check_reg_yser =  $reg_user->execute(['login' => $login, 'password' => $hash_pass, 'mail'=> $mail, 'date' => $today ]);

                    if($check_reg_yser){
                        session_start();
                        $_SESSION["$login"] = $login;                        
                        header('Location: index.php');
                    }else{
                        $msg = "Регистрация временно недоступна, приносим извенения";
                    }                   
                }  
            }
      }
?>




<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/signup.css">
</head>
<body>
        <main class="main_form">            
            <form method="POST" class="main_form_login">
                <label for="login"> Имя: <input id="login" class="main_input main_form_login" type="text" name="login"  placeholder="Иван Иванов" autofocus value="<?php echo $login;?>" required></label>
                <label for="login"> E-mail: <input id="login" class="main_input main_form_login" type="email" name="mail" placeholder="ivan@example.ru" value="<?php echo $mail;?>" required ></label>
                <label for="pass"> Пароль: не менее 8 символов<input id="pass" class="main_input main_form_password" type="password" name="password"  required></label>                                      
                <label for="pass"> Подтвердите пароль<input id="pass" class="main_input main_form_password" type="password" name="valid_password"  required></label>                                      
                <input class="main_form_btn" type="submit" name="enter" value="Зарегестрироватся">
                <p class="msg"><?php echo $msg;?> </p>    
            </form>
        </main>
                      
</body>
</html>