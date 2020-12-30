<?php
include ('../srv/Certs.php');

$certi= new Certs();
$file = $certi->genssh();

var_dump($file);