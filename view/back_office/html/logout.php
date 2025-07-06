<?php
require_once '../../../controller/AuthController.php';

$authController = new AuthController();
$authController->logout();

header('Location: authentication-login.php');
exit();
?> 