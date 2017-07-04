<?php
    require __DIR__.'/vendor/autoload.php';

    use App\Pusher;
    use Ratchet\Http\HttpServer;
    use Ratchet\Server\IoServer;
    use Ratchet\Wamp\WampServer;
    use Ratchet\WebSocket\WsServer;
    use React\EventLoop\Factory;
    use React\Socket\Server;
    use React\ZMQ\Context;

    $loop   = Factory::create();
    $pusher = new Pusher();

    // Listen for the web server to make a ZeroMQ push after an ajax request
    $context = new Context($loop);
    $pull = $context->getSocket(ZMQ::SOCKET_PULL);
    $pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself

    $pull->on('message', array($pusher, 'onSend'));

    // Set up our WebSocket server for clients wanting real-time updates
    $webSock = new Server($loop);
    $webSock->listen(6868, '0.0.0.0'); // Binding to 0.0.0.0 means remotes can connect
    $webServer = new IoServer(
        new HttpServer(
            new WsServer(
                $pusher
            )
        ),
        $webSock
    );

    $loop->run();