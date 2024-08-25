<?php
$pdo = require 'connect.php';

if (!empty($_POST['name']) and !empty($_POST['phone']) and !empty($_POST['email']) and !empty($_POST['password']) and !empty($_POST['confirmPassword'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $res = $pdo->prepare('SELECT * FROM users WHERE name=:name or email=:email or phone=:phone');
    $res->execute([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        ]);
    $row = $res->fetch();

    //если нет пользователя с таким именем, телефоном или почтой, то записываем пользователя в бд
    if ($row == null and $password == $_POST['confirmPassword'] and mb_strlen($password) >= 4) {
        $res = $pdo->prepare('INSERT INTO users SET  name=:name, phone=:phone, email=:email, password=:password_hash');
        $res->execute([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'password_hash' => $password_hash,
        ]);

        $_SESSION['auth'] = true;
        $id = $pdo->lastInsertId();
        $_SESSION['id'] = $id;
    } else {
        if ($password != $_POST['confirmPassword']){
            $_SESSION['flash'] = 'Пароли в обоих полях должны совпадать!';
        } elseif (mb_strlen($password) < 4) {
            $_SESSION['flash'] = 'Длина пароля должна быть не менее 4 символов!';
        } else {
            $_SESSION['flash'] = 'Такие имя или телефон, или почта уже заняты, укажите другие!';
        }
    }
} else {
    if (!empty($_POST['submit'])) {
        $_SESSION['flash'] = 'Все поля являются обязательными для заполнения!';
    }
}

//покажем форму регистрации для неавторизованного пользователя
if (empty($_SESSION['auth'])) {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $flash = $_SESSION['flash'] ?? '';

    $main = '                   
        <div class="d-grid gap-2 col-3 mx-auto" >
        <p class="text-center bg-danger text-light">'. $flash .'</p>
            <form action="reg" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label" >Имя</label>
                    <input name="name" type="text" class="form-control" id="name" value="' . $name . '">
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Телефон</label>
                    <input name="phone" type="text" class="form-control" id="phone" value="' . $phone . '">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Почта</label>
                    <input name="email" type="email" class="form-control" id="email" value="' . $email . '">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input name="password" type="password" class="form-control" id="password">
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Подтвердите пароль</label>
                    <input name="confirmPassword" type="password" class="form-control" id="confirmPassword">
                </div>
                <button name="submit" type="submit" class="btn btn-primary" value="true">Зарегистрироваться</button>
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
    <p class="text-center">Вы зарегистрированы и авторизованы!</p>
    <div class="d-grid gap-2 col-3 mx-auto">
        <a class="btn btn-primary" href="/" role="button">Главная</a>
        <a class="btn btn-primary" href="/profile" role="button">Мой профиль</a>
        <a class="btn btn-primary" href="/logout" role="button">Выйти</a>
    </div>
    ';
}

$page = [
    'title' => 'Регистрация',
    'main' => $main,
];
return $page;