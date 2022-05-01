<?php



require __DIR__.'/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;

$factory = (new Factory)
    ->withServiceAccount('meally-efd55-firebase-adminsdk-2rann-2956a348a7.json')
    ->withDatabaseUri('https://meally-efd55-default-rtdb.firebaseio.com/');

    $database = $factory->createDatabase();
    $auth = $factory->createAuth();
?>