<?php 
    include 'db_connection.php'; // Подключение к бд с помощью PDO

    // Избовляюсь от нотисов в HTML, т.к. поля после неверного заполения таблицы не стираются и не надо вводить все данные заново    
    $mail = '';
    $msg = "Поля могут содержать цифры, буквы, сивол - @"; 

    // Если отправилась форма методом POST начинаем проверку данных и заносим в БД. Форма на этой же странице.
    // Если заходить на страницу просто по адресу а не отправкой формы то мы на нее поподаем методом GET
    if($_SERVER['REQUEST_METHOD'] == 'POST' && count($_POST) > 0){       

        // Обработка данных из массива POST
        $mail = strtolower(trim($_POST['mail']));
        $password = $_POST['password'];

        if(isset($_POST['save_me'])){$save_me = $_POST['save_me'];}else{$save_me = null;}

            // проверка на корректность            
            if(!filter_var($mail, FILTER_VALIDATE_EMAIL) ||  $mail == ''){
                $msg = "Ошибка, mail $mail - не кооректный";
            }elseif(strlen($password) < 8 ||  $password == ''){
                $msg = "Ошибка,пароль не может содержать менее 8 символов";
            }else{
                // Проверка на идентичность с БД (есть ли такой пользователь в бд и установка куки и сесси)

                // Проверка почты                
                $check_mail = $pdo->prepare('SELECT count(*) FROM`users` WHERE `mail`= :mail ');
                $check_mail->execute(['mail' => $mail]);// отправка подготовленого запроса вернет true если запрос сработал

                // $check_mail = db_question('SELECT count(*) FROM`users` WHERE `mail`= :mail ', array('mail' => $mail));
                $res_check_mail = $check_mail->fetchColumn();// смотрим результат по колонке  если select ничего не нашел будет пусто

                // Проверка сработает только если такой mail найден в базе
                if($res_check_mail){
                    // Получаем пароль из бд и сравниваем с тем который нам ввел пользователь
                    $check_password = $pdo->prepare('SELECT `password` FROM `users` WHERE `mail` = :mail');
                    $check_password->execute(['mail' => $mail]);// отправка подготовленого запроса вернет true если запрос сработал
                    $password_hash_db = $check_password->fetchColumn();// смотрим результат по колонке  если select ничего не нашел будет пусто
                    
                    // Проверяем введенный пароль
                    if(password_verify($password, $password_hash_db)){ 
                        echo '1';            
                        $select_login = $pdo->prepare('SELECT `login` FROM `users` WHERE `mail` = :mail');
                        $select_login ->execute(['mail' => $mail]);// отправка подготовленого запроса вернет true если запрос сработал                            
                        $login_db = $select_login ->fetchColumn();// смотрим результат по колонке  если select ничего не нашел будет пусто
                        // Если установле флажок save_me, будут установлены куки
                        if(isset($save_me)){  
                            setcookie('pws', $password_hash_db, time() + 3600 * 24 * 365, '/');
                            setcookie('us', $login_db, time() + 3600 * 24 * 365, '/');
                            session_start();
                            $_SESSION['login'] = $login_db;
                            header('Location: index.php');
                        }else{
                            setcookie('pws', $password_hash_db, time() + 3600 * 24, '/');
                            setcookie('us', $login_db, time() + 3600 * 24, '/');
                            session_start();
                            $_SESSION['login'] = $login_db;
                            header('Location: index.php');
                        }
                        //Сессия открывается в любом случаи чтобы поприветствовать нашего юзверя)
                                             
                    }else{
                        $msg = "Неверный   пароль";
                    }
                }else{
                    $msg = "Пользователя с такми e-mail не существует";
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
            <a href="./signup.php">Зарегистрироваться</a>                
                <label for="login"> E-mail: <input id="login" class="main_input main_form_login" type="email" name="mail" placeholder="ivan@example.ru" value="<?php echo $mail;?>" required ></label>
                <label for="pass"> Пароль:<input id="pass" class="main_input main_form_password" type="password" name="password"  required></label>
                <label for="save_me"> Запомнить меня<input id="save_me" type="checkbox" name="save_me"></label>                
                <input class="main_form_btn" type="submit" name="enter" value="Войти">
                <p class="msg"><?php echo $msg;?> </p>    
            </form>
        </main>
                      
</body>
</html>