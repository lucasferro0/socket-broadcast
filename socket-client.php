<?php

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_connect($socket, '127.0.0.1', 4000);

while(true) {
    $response = socket_read($socket, 1024);

    if($response === false) {
        echo "Server has disconnected\n";
        break;
    }

    echo $response;
}