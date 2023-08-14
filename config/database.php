<?php

require 'config/constants.php';

// coccent to the dataabase
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// check connection

if(mysqli_errno($connection)){
    die(mysqli_error($connection));
}