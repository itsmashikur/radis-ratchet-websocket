<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebSocket Client</title>
</head>
<body>
    <h1>WebSocket Client</h1>
    <p id="message"></p>

    <script>

        // Create a new WebSocket connection
        const socket = new WebSocket('ws://localhost:8080');

        // Connection opened
        socket.addEventListener('open', function (event) {
            console.log('WebSocket is open now.');
        });

        // Listen for messages
        socket.addEventListener('message', function (event) {
            console.log('Message from server ', event.data);

            // Display the message inside the <p> tag
            const messageElement = document.getElementById('message');
            messageElement.textContent = event.data;
        });

        // Connection closed
        socket.addEventListener('close', function (event) {
            console.log('WebSocket is closed now.');
        });

        // Handle errors
        socket.addEventListener('error', function (event) {
            console.error('WebSocket error observed:', event);
        });
        
    </script>
</body>
</html>
