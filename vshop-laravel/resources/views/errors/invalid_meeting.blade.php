<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Link Invalid</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8d7da; color: #721c24; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; text-align: center; }
        .container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 500px; width: 90%; }
        h1 { margin-top: 0; font-size: 24px; }
        p { font-size: 16px; margin-bottom: 20px; }
        a { display: inline-block; padding: 10px 20px; background-color: #0d6efd; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; }
        a:hover { background-color: #0b5ed7; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Invalid Meeting Link</h1>
        <p>{{ $message ?? 'This meeting link is no longer valid.' }}</p>
        <a href="{{ url('/') }}">Return to Home</a>
    </div>
</body>
</html>
