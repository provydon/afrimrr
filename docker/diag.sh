#!/bin/sh
# Temporary boot diagnostic: boots the HTTP kernel against the live env/DB and
# prints the real status or exception, then idles so logs can be read.
echo "=== DIAG START ==="
cd /app
php -r 'require "/app/vendor/autoload.php"; $app = require "/app/bootstrap/app.php"; try { $k = $app->make(Illuminate\Contracts\Http\Kernel::class); $req = Illuminate\Http\Request::create("/up", "GET"); $resp = $k->handle($req); fwrite(STDERR, "DIAG_STATUS " . $resp->getStatusCode() . "\n"); } catch (\Throwable $e) { fwrite(STDERR, "DIAG_BOOTERR " . get_class($e) . ": " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n"); }'
echo "=== DIAG DONE ==="
sleep 600
