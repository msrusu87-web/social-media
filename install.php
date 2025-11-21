<?php
/**
 * UniPost Installation Script
 * 
 * This script handles the automatic installation of UniPost application
 * Similar to WordPress installation process
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('INSTALLER_VERSION', '1.0.0');
define('MIN_PHP_VERSION', '8.2.0');

class UniPostInstaller {
    private $step = 1;
    
    public function __construct() {
        $this->step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
        
        // Check if already installed
        if (file_exists('.env') && file_exists('storage/installed.lock') && $this->step !== 99) {
            $this->showAlreadyInstalled();
            exit;
        }
    }
    
    public function run() {
        switch($this->step) {
            case 1:
                $this->showRequirementsCheck();
                break;
            case 2:
                $this->showDatabaseConfig();
                break;
            case 3:
                $this->showApplicationConfig();
                break;
            case 4:
                $this->showSocialMediaConfig();
                break;
            case 5:
                $this->processInstallation();
                break;
            case 6:
                $this->showComplete();
                break;
            case 99:
                $this->showDiagnostics();
                break;
            default:
                $this->showRequirementsCheck();
        }
    }
    
    private function getHeader($title = 'Installation') {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPost - {$title}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .installer-container { background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 800px; width: 100%; overflow: hidden; }
        .installer-header { background: linear-gradient(135deg, #764ba2 0%, #667eea 100%); padding: 40px; text-align: center; color: white; }
        .installer-header h1 { font-size: 36px; margin-bottom: 10px; }
        .installer-progress { background: rgba(255,255,255,0.2); height: 6px; border-radius: 3px; margin-top: 20px; }
        .installer-progress-bar { background: white; height: 100%; border-radius: 3px; transition: width 0.3s ease; }
        .installer-body { padding: 40px; }
        .step-indicator { display: flex; justify-content: space-between; margin-bottom: 40px; padding: 0 20px; }
        .step { display: flex; flex-direction: column; align-items: center; position: relative; flex: 1; }
        .step-circle { width: 40px; height: 40px; border-radius: 50%; background: #e0e0e0; display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; margin-bottom: 10px; z-index: 1; }
        .step.active .step-circle { background: #667eea; }
        .step.completed .step-circle { background: #48bb78; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; }
        .btn { padding: 12px 30px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-primary { background: #667eea; color: white; }
        .btn-secondary { background: #e0e0e0; color: #333; margin-right: 10px; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-header">
            <h1>UniPost Installer</h1>
            <div class="installer-progress"><div class="installer-progress-bar" style="width: {$this->getProgress()}%"></div></div>
        </div>
        <div class="installer-body">
HTML;
    }

    private function getFooter() {
        return "</div></div></body></html>";
    }

    private function getProgress() { return ($this->step / 6) * 100; }

    private function showRequirementsCheck() {
        echo $this->getHeader('Requirements');
        $reqs = $this->checkRequirements();
        $canContinue = !in_array(false, array_column($reqs, 'status'));
        
        echo "<h2>System Requirements</h2><br>";
        foreach($reqs as $req) {
            $icon = $req['status'] ? '✅' : '❌';
            echo "<div class='alert " . ($req['status'] ? 'alert-success' : 'alert-error') . "'>{$icon} {$req['name']}</div>";
        }
        
        echo '<br><div class="button-group">';
        if ($canContinue) echo "<button onclick=\"location.href='?step=2'\" class='btn btn-primary'>Continue</button>";
        else echo "<button onclick='location.reload()' class='btn btn-secondary'>Check Again</button>";
        echo "</div>";
        echo $this->getFooter();
    }

    private function checkRequirements() {
        return [
            ['name' => 'PHP 8.2+', 'status' => version_compare(PHP_VERSION, '8.2.0', '>=')],
            ['name' => 'BCMath', 'status' => extension_loaded('bcmath')],
            ['name' => 'Ctype', 'status' => extension_loaded('ctype')],
            ['name' => 'JSON', 'status' => extension_loaded('json')],
            ['name' => 'Mbstring', 'status' => extension_loaded('mbstring')],
            ['name' => 'OpenSSL', 'status' => extension_loaded('openssl')],
            ['name' => 'PDO', 'status' => extension_loaded('pdo')],
            ['name' => 'Tokenizer', 'status' => extension_loaded('tokenizer')],
            ['name' => 'XML', 'status' => extension_loaded('xml')],
        ];
    }

    private function showDatabaseConfig() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['db'] = $_POST;
            header('Location: ?step=3');
            exit;
        }
        echo $this->getHeader('Database');
        echo <<<HTML
        <h2>Database Configuration</h2><br>
        <form method="POST">
            <div class="form-group"><label>Host</label><input name="host" value="localhost" required></div>
            <div class="form-group"><label>Port</label><input name="port" value="3306" required></div>
            <div class="form-group"><label>Database Name</label><input name="name" required></div>
            <div class="form-group"><label>Username</label><input name="user" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="pass"></div>
            <div class="form-group"><label>Prefix</label><input name="prefix" value="up_"></div>
            <button type="submit" class="btn btn-primary">Next</button>
        </form>
HTML;
        echo $this->getFooter();
    }

    private function showApplicationConfig() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['app'] = $_POST;
            header('Location: ?step=4');
            exit;
        }
        echo $this->getHeader('Application');
        echo <<<HTML
        <h2>Application Settings</h2><br>
        <form method="POST">
            <div class="form-group"><label>App Name</label><input name="app_name" value="UniPost" required></div>
            <div class="form-group"><label>App URL</label><input name="app_url" value="http://{$_SERVER['HTTP_HOST']}" required></div>
            <div class="form-group"><label>Admin Email</label><input type="email" name="admin_email" required></div>
            <div class="form-group"><label>Admin Password</label><input type="password" name="admin_pass" required></div>
            <button type="submit" class="btn btn-primary">Next</button>
        </form>
HTML;
        echo $this->getFooter();
    }

    private function showSocialMediaConfig() {
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['social'] = $_POST;
            header('Location: ?step=5');
            exit;
        }
        echo $this->getHeader('Social Keys');
        echo <<<HTML
        <h2>Social API Keys (Optional)</h2><br>
        <form method="POST">
            <div class="form-group"><label>Stripe Key</label><input name="stripe_key"></div>
            <div class="form-group"><label>Stripe Secret</label><input name="stripe_secret"></div>
            <div class="form-group"><label>OpenAI Key</label><input name="openai_key"></div>
            <button type="submit" class="btn btn-primary">Install Now</button>
        </form>
HTML;
        echo $this->getFooter();
    }

    private function processInstallation() {
        echo $this->getHeader('Installing...');
        
        try {
            $this->createEnvFile();
            $this->runMigrations();
            $this->createAdminUser();
            $this->setPermissions();
            file_put_contents('storage/installed.lock', date('Y-m-d H:i:s'));
            echo "<div class='alert alert-success'>Installation Successful!</div>";
            echo "<button onclick=\"location.href='?step=6'\" class='btn btn-primary'>Finish</button>";
        } catch (Exception $e) {
            echo "<div class='alert alert-error'>Error: {$e->getMessage()}</div>";
        }
        echo $this->getFooter();
    }

    private function createEnvFile() {
        $db = $_SESSION['db'];
        $app = $_SESSION['app'];
        $key = 'base64:'.base64_encode(random_bytes(32));
        
        $envContent = "APP_NAME=\"{$app['app_name']}\"\nAPP_ENV=production\nAPP_KEY={\$key}\nAPP_DEBUG=false\nAPP_URL={\$app['app_url']}\n\n";
        $envContent .= "DB_CONNECTION=mysql\nDB_HOST={\$db['host']}\nDB_PORT={\$db['port']}\nDB_DATABASE={\$db['name']}\nDB_USERNAME={\$db['user']}\nDB_PASSWORD={\$db['pass']}\n";
        
        file_put_contents('.env', $envContent);
    }

    private function runMigrations() {
        $db = $_SESSION['db'];
        $pdo = new PDO("mysql:host={\$db['host']};port={\$db['port']};dbname={\$db['name']}", {\$db['user']}, {\$db['pass']});
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Basic Users Table Schema
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            email_verified_at TIMESTAMP NULL,
            password VARCHAR(255) NOT NULL,
            remember_token VARCHAR(100),
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");
    }

    private function createAdminUser() {
        $db = $_SESSION['db'];
        $app = $_SESSION['app'];
        $pdo = new PDO("mysql:host={\$db['host']};port={\$db['port']};dbname={\$db['name']}", {\$db['user']}, {\$db['pass']});
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute(['Admin', $app['admin_email'], password_hash($app['admin_pass'], PASSWORD_DEFAULT)]);
    }

    private function setPermissions() {
        $dirs = ['storage', 'bootstrap/cache'];
        foreach($dirs as $dir) {
            if(is_dir($dir)) @chmod($dir, 0775);
        }
    }

    private function showComplete() {
        echo $this->getHeader('Complete');
        echo "<h2>Installation Complete!</h2><br><p>Please delete install.php for security.</p><br>";
        echo "<a href='/' class='btn btn-primary'>Go to Homepage</a>";
        echo $this->getFooter();
    }
    
    private function showAlreadyInstalled() {
        echo $this->getHeader('Error');
        echo "<div class='alert alert-error'>Application is already installed. Delete storage/installed.lock to reinstall.</div>";
        echo $this->getFooter();
    }

    private function showDiagnostics() {
        echo $this->getHeader('Diagnostics');
        echo "<pre>" . print_r($_SERVER, true) . "</pre>";
        echo $this->getFooter();
    }
}

$installer = new UniPostInstaller();
$installer->run();
?>