<?php

use Firebase\Auth\Token\Exception\InvalidToken;
use Kreait\Firebase\Exception\Messaging\InvalidArgument;

session_start();
include('dbcon.php');

if(isset($_POST['signin_btn'])){
    $email = $_POST['user-emailaddress'];
    $password = $_POST['user-password'];
    try {
        try {
            $user = $auth->getUserByEmail("$email");
    
            try{
                $signInResult = $auth->signInWithEmailAndPassword($email, $password);
                $idTokenString = $signInResult->idToken();
                    try {
                        $verifiedIdToken = $auth->verifyIdToken($idTokenString);
                        $uid = $verifiedIdToken->claims()->get('sub');
                        $_SESSION['verified-uid'] = $uid;
                        $_SESSION['idTokenString'] = $idTokenString;
                        $_SESSION['status'] = "Logged in successfully! ";
                        header('Location: pages/dashboard.php');
                        exit();
                    } catch (InvalidToken $e) {
                        echo 'The token is invalid: '.$e->getMessage();
                    }
            }catch(Exception $e){
                $_SESSION['error-status'] = "Invalid credentials.";
                header('Location: pages/sign-in.php');
                exit();
            }
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
            $_SESSION['error-status'] = "Invalid credentials.";
            header('Location: pages/sign-in.php');
            exit();
        }
    }catch(InvalidArgumentException $e){
        $_SESSION['error-status'] = "Invalid credentials.";
            header('Location: pages/sign-in.php');
            exit(); 
    }
 

}else{
    $_SESSION['status'] = "You do not have permission to do that.";
    header('Location: pages/sign-in.php');
    exit();
}



?>