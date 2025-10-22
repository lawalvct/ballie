<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debugbar Test - Ballie</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            margin-top: 0;
        }
        .success {
            color: #10b981;
            font-weight: bold;
        }
        .info {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 15px 0;
        }
        .code {
            background: #1f2937;
            color: #10b981;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
        }
        ul {
            line-height: 1.8;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            background: #10b981;
            color: white;
            border-radius: 5px;
            font-size: 12px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🐛 Laravel Debugbar Test <span class="badge">✓ Active</span></h1>

        <p class="success">✅ Debugbar is installed and running!</p>

        <div class="info">
            <strong>📋 What to check:</strong>
            <ul>
                <li>Look at the bottom of this page - you should see the Debugbar</li>
                <li>Click on the "Messages" tab to see the test messages below</li>
                <li>Click on the "Queries" tab to see the database query</li>
                <li>Click on any file path to open it in VSCode</li>
            </ul>
        </div>

        <h2>Debug Messages Sent:</h2>
        <div class="code">
            <div>✓ Info: Debugbar test page loaded</div>
            <div>✓ Warning: Testing VSCode integration</div>
            <div>✓ Error: This is a test error (not a real error!)</div>
            <div>✓ Custom data with context</div>
            <div>✓ Database query executed</div>
        </div>

        <h2>VSCode Integration Test:</h2>
        <p>
            This file is located at: <br>
            <code>{{ __FILE__ }}</code>
        </p>
        <p>
            <strong>Test:</strong> Click any file path in the Debugbar → It should open in VSCode!
        </p>

        <h2>Telescope Integration:</h2>
        <p>
            Debugbar and Telescope work together perfectly! <br>
            Visit Telescope: <a href="/telescope" target="_blank">http://localhost:8000/telescope</a>
        </p>

        <div class="info">
            <strong>💡 Pro Tips:</strong>
            <ul>
                <li>Press <code>ESC</code> to close the Debugbar</li>
                <li>Click tabs to switch between different collectors</li>
                <li>Right-click SQL queries to copy them</li>
                <li>Check the Timeline tab to see request flow</li>
            </ul>
        </div>
    </div>

    <div class="card">
        <h2>Next Steps:</h2>
        <ol>
            <li>Navigate to your dashboard to see Debugbar in action</li>
            <li>Check database queries for performance issues</li>
            <li>Use <code>Debugbar::info()</code> in your code for debugging</li>
            <li>Open Telescope for deeper inspection</li>
        </ol>
    </div>
</body>
</html>
