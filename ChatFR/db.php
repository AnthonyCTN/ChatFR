<?php
 $host_name = 'db5015916199.hosting-data.io';
 $database = 'dbs12973372';
 $user_name = 'dbu1244979';
 $password = 'Chatfr1@,,';
 $conn = new mysqli($host_name, $user_name, $password, $database);
 
 if ($conn->connect_error) {
     die("Connexion échouée: " . $conn->connect_error);
 }
 ?>