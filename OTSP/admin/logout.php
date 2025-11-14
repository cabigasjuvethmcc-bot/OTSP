<?php
session_start();

session_unset();
session_destroy();

header('Location: /OTSP/admin/login.php');
exit;
