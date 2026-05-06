<?php

$clients = array();
$messages = array();

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_bind($socket, '127.0.0.1', 4000);

socket_listen($socket);

socket_set_nonblock($socket);

echo "Server is running on port 4000\n";

while(true) {
    if(($newSocket = socket_accept($socket)) !== false) {
        var_dump($newSocket);
        
        echo "Client has connected\n";
        
        $clients[] = $newSocket;
    }

    foreach($clients as $key => $client) {
        $data = socket_read($client, 1024);

        var_dump("data:");
        var_dump($data);
        echo PHP_EOL;
        
        if($data === false) {
            // echo "Client $key has disconnected\n";
            
            // unset($clients[$key]);
            
            continue;
        }

        $dados = explode(PHP_EOL, $data);


        $requestMethod = explode(' ', $dados[0])[0];
        $requestUri = explode(' ', $dados[0])[1];
        $requestBody = json_decode($dados[6], true);

        if ($requestMethod == 'POST' && $requestUri == '/api/v1/events') {
            
            $message = json_encode([
                'event' => $requestBody['event'], 
                'channel' => $requestBody['channel']
            ]);
            
            foreach($clients as $key2 => $client2) {

                if ($key2 == $key) {
                    continue;
                }

                $result = socket_write(
                    $client2, 
                    $message
                );
    
                if ($result === false) {
                    echo "Failed to send message to client2\n";
                }
            }
        }

        exit;
    }
}