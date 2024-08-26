<?php
if (empty($_SESSION['auth'])) {
    $main = '
    <p class="text-center">Авторизуйтесь или зарегистрируйтесь</p>
    <div class="d-grid gap-2 col-3 mx-auto">
        <a class="btn btn-primary" href="/login" role="button">Авторизоваться</a>
        <a class="btn btn-primary" href="/reg" role="button">Зарегистрироваться</a>
    </div>
';
} else {
    $main = '        
    <p class="text-center">Главная</p>        
    <div class="d-grid gap-2 col-3 mx-auto mt-5">
        <a class="btn btn-primary" href="/profile" role="button">Мой профиль</a>
        <a class="btn btn-primary" href="/logout" role="button">Выйти</a>
    </div>
';
}


$page = [
    'title' => 'Главная',
    'main' => $main,
];
return $page;


