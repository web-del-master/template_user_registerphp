<?php  
if($_SERVER['REQUEST_METHOD'] == 'POST' && count($_POST) > 0){
    foreach($_COOKIE as $key => $value) setcookie($key, '', time() - 3600, '/');
    header('Location: enter.php');
}
?>