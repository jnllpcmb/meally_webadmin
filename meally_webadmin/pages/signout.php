<?php
session_start();

unset($_SESSION['verified-uid']);
unset($_SESSION['idTokenString']);
if(isset($_SESSION['expired-status'])){
    $_SESSION['status'] = "Session Expired.";
}else{

    $_SESSION['logoutstatus'] = "Logged out successfully! ";
}
header('Location: sign-in.php');

exit();
?>