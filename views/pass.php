<?php

$pdo = require 'connect.php';

if (!empty($_SESSION['auth'])) {
    if (!empty($_POST['password']) and !empty($_POST['confirmPassword'])) {
        $password = $_POST['password'];

        if ($password != $_POST['confirmPassword']){
            $_SESSION['flash'] = 'Пароли в обоих полях должны совпадать!';
        } elseif (mb_strlen($password) < 4) {
            $_SESSION['flash'] = 'Длина пароля должна быть не менее 4 символов!';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $res = $pdo->prepare('UPDATE users SET password=:password WHERE id=:id');
            $res->execute([
                'password' => $password_hash,
                'id' => $_SESSION['id'],
            ]);
            $_SESSION['flashSuccess'] = 'Пароль успешно изменен!';
        }
    } else {
        if (!empty($_POST['submit'])) {
            $_SESSION['flash'] = 'Все поля являются обязательными для заполнения!';
        }
    }

    $flash = $_SESSION['flash'] ?? '';
    $flashSuccess = $_SESSION['flashSuccess'] ?? '';
    $main = '
    <div class="d-grid gap-2 col-5 mx-auto">            
        <form action="/pass" method="POST">
        <p class="text-center bg-danger text-light rounded-3">'. $flash .'</p>                        
        <p class="text-center bg-success text-light rounded-3">'. $flashSuccess .'</p>          
            <div class="input-group mb-3">
              <span class="input-group-text" id="basic-addon1">Введите новый пароль</span>
              <input name="password" type="password" class="form-control" aria-label="password" aria-describedby="basic-addon1">
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text" id="basic-addon1">Подтвердите пароль</span>
              <input name="confirmPassword" type="password" class="form-control" aria-label="confirmPassword" aria-describedby="basic-addon1">
            </div>
            <p class="text-start">Для изменения укажите новые данные в форме и нажмите "Изменить данные"</p>
            <button name="submit" type="submit" class="btn btn-primary" value="true">Изменить пароль</button>
        </form>
        <div class="d-grid gap-2 col-7 mx-auto"> 
            <a class="btn btn-primary" href="/profile" role="button" >Мой профиль</a>
            <a class="btn btn-primary" href="/" role="button" >На главную</a>
        </div>
    </div>
    ';

    if (isset($_SESSION['flash']) or isset($_SESSION['flashSuccess'])) {
        unset($_SESSION['flash']);
        unset($_SESSION['flashSuccess']);
    }

} else {
    header('Location: /');
    die();
}

$page = [
    'title' => 'Профиль',
    'main' => $main,
];
return $page;