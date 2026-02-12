<?php
define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h1>Debug Mail Configuration</h1>";

echo "<h3>1. Environment (.env)</h3>";
echo "MAIL_MAILER: " . env('MAIL_MAILER') . "<br>";
echo "MAIL_HOST: " . env('MAIL_HOST') . "<br>";
echo "MAIL_PORT: " . env('MAIL_PORT') . "<br>";
echo "MAIL_USERNAME: " . env('MAIL_USERNAME') . "<br>";
echo "MAIL_ENCRYPTION: " . env('MAIL_ENCRYPTION') . "<br>";

echo "<h3>2. Config (Runtime)</h3>";
$config = config('mail');
echo "Default Mailer: " . $config['default'] . "<br>";
if (isset($config['mailers']['smtp'])) {
    echo "SMTP Host: " . $config['mailers']['smtp']['host'] . "<br>";
    echo "SMTP Port: " . $config['mailers']['smtp']['port'] . "<br>";
    echo "SMTP Username: " . $config['mailers']['smtp']['username'] . "<br>";
    echo "SMTP Encryption: " . $config['mailers']['smtp']['encryption'] . "<br>";
} else {
    echo "SMTP config not found in mailers array.<br>";
}
echo "Legacy Driver Config (mail.driver): " . config('mail.driver') . "<br>";
echo "Legacy Host Config (mail.host): " . config('mail.host') . "<br>";

echo "<h3>3. Sending Test Email</h3>";
$to = 'VIRAJGURJAR789@GMAIL.COM';
try {
    Illuminate\Support\Facades\Mail::raw('This is a test email from debug_mail.php to verify SMTP settings.', function ($message) use ($to) {
        $message->to($to)
                ->subject('SMTP Debug Test');
    });
    echo "<strong style='color:green'>SUCCESS: Email sent to $to</strong>";
} catch (\Throwable $e) {
    echo "<strong style='color:red'>FAILURE: " . $e->getMessage() . "</strong><br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
