<?php

use Firebase\Auth\Token\Exception\InvalidToken;
use Google\Cloud\Core\Exception\BadRequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\RejectedPromise;
use Kreait\Firebase\Exception\AuthApiExceptionConverter;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\Messaging\InvalidArgument;

session_start();
include('dbcon.php');

// edit user type 
if (isset($_POST['usertype_btn'])) {
    $uid = $_POST['usertype-user-id'];
    $roles = $_POST['roles'];

    if ($roles == "admin") {
        $auth->setCustomUserClaims($uid, ['admin' => true]);
        $msg = "User role has been set to Administrator";
    } elseif ($roles == "staff") {
        $auth->setCustomUserClaims($uid, ['staff' => true]);
        $msg = "User role has been set to Staff";
    } else {
    }
    if ($msg) {
        $_SESSION['usersuccess'] = $msg;
        header('Location: systemusers.php');
        exit();
    } else {
        $_SESSION['userrolefail'] = "Failed! Invalid input.";
        header("Location: update-systemuser.php?id=$uid");
        exit();
    }
}


// enable or disable user 
if (isset($_POST['eduser_btn'])) {
    $enable_disable = $_POST['enabledisable '];
    $uid = $_POST['enabledisable-user-id'];
    if ($enable_disable == "disable") {
        $updatedUser = $auth->disableUser($uid);
        $msg = "Success! Account Disabled!";
    } else {
        $updatedUser = $auth->enableUser($uid);
        $msg = "Success! Account Enabled!";
    }
    if ($updatedUser) {
        $_SESSION['enabledisablesuccess'] = $msg;
        header('Location: systemusers.php');
        exit();
    } else {
        $_SESSION['enabledisablefailed'] = "Failed! Invalid input.";
        header('Location: systemusers.php');
        exit();
    }
}
// update userpassword 
if (isset($_POST['updateuserpassword_btn'])) {
    $newpassowrd = $_POST['newpassword'];
    $retypepassword = $_POST['retypepassword'];
    $uid = $_POST['changepass-user-id'];

    if ($newpassowrd == $retypepassword) {
        $updatedUser = $auth->changeUserPassword($uid, $newpassowrd);
        if ($updatedUser) {
            $_SESSION['passwordchangessuccess'] = "Success!";
            header('Location: systemusers.php');
            exit();
        } else {
            $_SESSION['passwordchangesfailed'] = "Failed!";
            header('Location: systemusers.php');
            exit();
        }
    } else {
        $_SESSION['newpass'] = "Failed!";
        header("Location: update-systemuser.php?id=$uid");
        exit();
    }
}
// update user account info
if (isset($_POST['updateuser_btn'])) {
    $email = $_POST['display_useremail'];
    $displayName = $_POST['display_userfullname'];
    $uid = $_POST['user-id'];
    $properties = [
        'displayName' => $displayName,
        'email' =>  $email,
    ];

    $updatedUser = $auth->updateUser($uid, $properties);
    // $updatedUser = $auth->changeUserEmail($uid, $properties);

    if ($updatedUser) {
        $_SESSION['updatedusersucess'] = "Success!";
        header('Location: systemusers.php');
        exit();
    } else {
        $_SESSION['updateduserfailed'] = "Failed!";
        header('Location: systemusers.php');
        exit();
    }
}
// remove user 
if (isset($_POST['removeuser_btn'])) {

    $uid = $_POST['removeuser_btn'];
    try {
        $auth->deleteUser($uid);
        $_SESSION['deletedusersuccess'] = "Success!";
        header('Location: systemusers.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['deleteduserfailed'] = "Failed!";
        header('Location: systemusers.php');
        exit();
    }
}

// register user 
if (isset($_POST['registeruser_btn'])) {
    $useremail = $_POST['useremail'];
    $userfullaname = $_POST['userfname'] . ' ' . $_POST['userlname'];
    $roles = $_POST['role'];
    $userpassword = $_POST['userpassword'];
    // generates random id for user
    $randomgen = base64_encode(random_bytes(10));
    $milliseconds = floor(microtime(true) * 1000);
    $customuiid = $randomgen . $milliseconds;
    $userProperties = [
        'uid' => $customuiid,
        'email' => $useremail,
        'emailVerified' => false,
        'password' => $userpassword,
        'displayName' => $userfullaname,
        'disabled' => false,
    ];
    try {
        $createdUser = $auth->createUser($userProperties);
        if ($createdUser) {
            if ($roles == "admin") {
                $auth->setCustomUserClaims($customuiid, ['admin' => true]);
                $msg = "User role has been set to Administrator";
            } elseif ($roles == "staff") {
                $auth->setCustomUserClaims($customuiid, ['staff' => true]);
                $msg = "User role has been set to Staff";
            }
            $_SESSION['createdusersuccess'] = "Success!";
            header('Location: systemusers.php');
            exit();
        } else {
            $_SESSION['createduserfailed'] = "Failed!";
            header('Location: systemusers.php');
            exit();
        }
    } catch (InvalidArgumentException $e) {
        $_SESSION['invalidemail'] = "Failed!";
        header('Location: systemusers.php');
        exit();
    } catch (AuthException $error) {
        $_SESSION['invalidemail'] = "Failed!";
        header('Location: systemusers.php');
        exit();
    }
}

// sign-in 
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
                    $claims = $auth->getUser($uid)->customClaims;
                    if (isset($claims['admin']) == true) {
                        $_SESSION['verified-admin'] = true;
                        $_SESSION['verified-uid'] = $uid;
                        $_SESSION['idTokenString'] = $idTokenString;
                    } elseif (isset($claims['staff']) == true) {
                        $_SESSION['verified-uid'] = $uid;
                        $_SESSION['idTokenString'] = $idTokenString;
                    }

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
