<?php

$pdo = require 'connect.php';

if (!empty($_SESSION['auth'])) {

    $res = $pdo->prepare('SELECT * FROM users WHERE id=?');
    $res->execute([$_SESSION['id']]);
    $user = $res->fetch();

    if (!empty($_POST['submit'])) {
        $name = $_POST['name'] ?? $user['name'];
        $phone = $_POST['phone'] ?? $user['phone'];
        $email = $_POST['email'] ?? $user['email'];

        $res = $pdo->prepare('SELECT * FROM users WHERE id<>:id and (name=:name or email=:email or phone=:phone)');
        $res->execute([
            'id' => $_SESSION['id'],
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);
        $row = $res->fetch();

        //если нет пользователя с новыми данными - именем, телефоном или почтой - то меняем данные
        if ($row == null) {

            if (!empty($_POST['name']) and !empty($_POST['phone']) and !empty($_POST['email'])) {
                $res = $pdo->prepare('UPDATE users SET name=:name, phone=:phone, email=:email WHERE id=:id');
                $res->execute([
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'id' => $_SESSION['id'],
                ]);
                $_SESSION['flashSuccess'] = 'Данные успешно изменены!';
            } else {
                    $_SESSION['flash'] = 'Все поля являются обязательными для заполнения!';
            }


        } else {
            $_SESSION['flash'] = 'Такие имя или телефон, или почта уже заняты, укажите другие!';
        }
    }

    $flash = $_SESSION['flash'] ?? '';
    $flashSuccess = $_SESSION['flashSuccess'] ?? '';

    $main = '
    <div class="d-grid gap-2 col-3 mx-auto">            
        <form action="/profile" method="POST">
        <p class="text-center bg-danger text-light rounded-3">'. $flash .'</p>
        <p class="text-center bg-success text-light rounded-3">'. $flashSuccess .'</p>
            <div class="input-group mb-3">
              <span class="input-group-text" id="basic-addon1">Имя</span>
              <input name="name" type="text" class="form-control" value="'. $user['name'] .'" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text" id="basic-addon1">Телефон</span>
              <input name="phone" type="text" class="form-control" value="'. $user['phone'] .'" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text" id="basic-addon1">Почта</span>
              <input name="email" type="text" class="form-control" value="'. $user['email'] .'" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <p class="text-start">Для изменения укажите новые данные в форме и нажмите "Изменить данные"</p>
            <button name="submit" type="submit" class="btn btn-primary" value="true">Изменить данные</button>
        </form>
        <a class="btn btn-primary" href="/pass" role="button">Хочу поменять пароль</a>
        <a class="btn btn-primary" href="/" role="button">На главную</a>
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