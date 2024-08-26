<?php

$pdo = require 'connect.php';

if (!empty($_POST['login']) and !empty($_POST['password'])) {

    $login = $_POST['login'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $res = $pdo->prepare('SELECT * FROM users WHERE phone=:phone or email=:email');
    $res->execute([
        'email' => $login,
        'phone' => $login,
    ]);

    while ($user = $res->fetch()) {
        $hash = $user['password'];
        if (password_verify($_POST['password'], $hash)) {
            $_SESSION['auth'] = true;
            $_SESSION['id'] = $user['id'];
        break;
        } else {
            $_SESSION['flash'] = 'Введен неверный телефон, почта или пароль!';
        }
    }

    if ($user == null) {
        $_SESSION['flash'] = 'Введен неверный телефон, почта или пароль!';
    }

} else {
    if (!empty($_POST['submit'])) {
        $_SESSION['flash'] = 'Все поля являются обязательными для заполнения!';
    }
}

if (empty($_SESSION['auth'])) {
    $flash = $_SESSION['flash'] ?? '';

    $main = '                   
        <div class="d-grid gap-2 col-3 mx-auto" >
        <p class="text-center bg-danger text-light rounded-3">'. $flash .'</p>
            <form action="login" method="POST">
                <div class="mb-3">
                    <label for="login" class="form-label" >Телефон или email</label>
                    <input name="login" type="text" class="form-control" id="login"">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input name="password" type="password" class="form-control" id="password">
                </div>
                <button name="submit" type="submit" class="btn btn-primary" value="true">Войти</button>
            </form>
            <a class="btn btn-primary" href="/" role="button">На главную</a>
            <div id="captcha-container" class="smart-captcha" data-sitekey="ysc1_Ar1pFMLr9W6OSb2aILLtyl7rAsNdMfagvhXwhGd5200e2978">
                <input type="hidden" name="smart-token" value="bpnf1t0jhkh2021atme4">
            </div>
        </div>
        
        ';

    if (isset($_SESSION['flash'])) {
        unset($_SESSION['flash']);
    }

} else {
    $main = '
    <p class="text-center">Вы авторизованы!</p>
    <div class="d-grid gap-2 col-3 mx-auto">
        <a class="btn btn-primary" href="/" role="button">Главная</a>
        <a class="btn btn-primary" href="/profile" role="button">Мой профиль</a>
        <a class="btn btn-primary" href="/logout" role="button">Выйти</a>
    </div>
    ';
}

$page = [
    'title' => 'Авторизация',
    'main' => $main,
];
return $page;