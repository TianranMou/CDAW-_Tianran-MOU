<?php

require "config.php";

// simplest auto loader cf. doc PHP
// we will revisit that later
spl_autoload_register(function ($class_name) {
    $classFile = $class_name . '.php';
    include $classFile;
});