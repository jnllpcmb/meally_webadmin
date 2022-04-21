<?php

require __DIR__.'/vendor/autoload.php'; 
use Kreait\Firebase\Factory;

$factory = (new Factory)
->withServiceAccount('meally-efd55-firebase-adminsdk-2rann-d02f9bf372')
->withDatabaseUri('https://meally-efd55-default-rtdb.firebaseio.com/');

$database = $factory->createDatabase();
?>