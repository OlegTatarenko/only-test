<?php
session_start();

use src\Controller;
use src\Viewer;

$pdo = require_once 'connect.php';
require_once 'src/Controller.php';
require_once 'src/Viewer.php';

$url = $_SERVER['REQUEST_URI'];

$page = Controller::getPage($url);
Viewer::viewPage($page);