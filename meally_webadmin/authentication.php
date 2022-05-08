<?php

use Firebase\Auth\Token\Exception\InvalidToken;

session_start();
include('dbcon.php');

if (isset($_SESSION['verified-uid'])) {
    $uid = $_SESSION['verified-uid'];
    $idTokenString = $_SESSION['idTokenString'];
    $claims = $auth->getUser($uid)->customClaims;
    if (isset($claims['admin']) == true) {
        $_SESSION['admincontrol'] = "true";
    }
    try {
        $verifiedIdToken = $auth->verifyIdToken($idTokenString);
    } catch (InvalidToken $e) {
        echo 'The token is invalid: ' . $e->getMessage();
        $_SESSION['expired-status'] = "Session Expired";
        header('Location: signout.php');
    }
} else {
    header('Location: sign-in.php');
    exit();
}
