<?php
require_once('includes/common.php');

$returnURL = isset($_GET['return']) ? $_GET['return'] : BASE_URL;
session_destroy();
header('Location: '.$returnURL);
