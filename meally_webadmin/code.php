<?php

use Firebase\Auth\Token\Exception\InvalidToken;
use Kreait\Firebase\Exception\Messaging\InvalidArgument;

session_start();
include('dbcon.php');


if (isset($_POST['registeruser_btn'])) {
    $useremail = $_POST['useremail'];
    $userfullaname = $_POST['userfname'] . ' ' . $_POST['userlname'];
    //$userprivelege = $_POST['userprivelege'];
    $userpassword = $_POST['userpassword'];

    $userProperties = [
        'email' => $useremail,
        'emailVerified' => false,
        'password' => $userpassword,
        'displayName' => $userfullaname,
    ];

    $createdUser = $auth->createUser($userProperties);
    if ($createdUser) {
        $_SESSION['createdusersuccess'] = "Success!";
        header('Location: systemusers.php');
        exit();
    } else {
        $_SESSION['createdusersuccess'] = "Failed!";
        header('Location: systemusers.php');
        exit();
    }
}



if (isset($_POST['signin_btn'])) {
    $email = $_POST['user-emailaddress'];
    $password = $_POST['user-password'];
    try {
        try {
            $user = $auth->getUserByEmail("$email");

            try {
                $signInResult = $auth->signInWithEmailAndPassword($email, $password);
                $idTokenString = $signInResult->idToken();
                try {
                    $verifiedIdToken = $auth->verifyIdToken($idTokenString);
                    $uid = $verifiedIdToken->claims()->get('sub');
                    $_SESSION['verified-uid'] = $uid;
                    $_SESSION['idTokenString'] = $idTokenString;
                    $_SESSION['status'] = "Logged in successfully! ";
                    header('Location: dashboard.php');
                    exit();
                } catch (InvalidToken $e) {
                    echo 'The token is invalid: ' . $e->getMessage();
                }
            } catch (Exception $e) {
                $_SESSION['error-status'] = "Invalid credentials.";
                header('Location: sign-in.php');
                exit();
            }
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
            $_SESSION['error-status'] = "Invalid credentials.";
            header('Location: sign-in.php');
            exit();
        }
    } catch (InvalidArgumentException $e) {
        $_SESSION['error-status'] = "Invalid credentials.";
        header('Location: sign-in.php');
        exit();
    }
} else {
    $_SESSION['status'] = "You do not have permission to do that.";
    header('Location: sign-in.php');
    exit();
}
?>