<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallController extends Controller
{
    /**
     * Check if the application is already installed.
     */
    private function isInstalled(): bool
    {
        return File::exists(base_path('.installed'));
    }

    /**
     * Show the requirements check page.
     */
    public function requirements()
    {
        if ($this->isInstalled()) {
            return redirect('/');
        }

        $requirements = [
            'bcmath' => extension_loaded('bcmath'),
            'ctype' => extension_loaded('ctype'),
            'json' => extension_loaded('json'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'pdo' => extension_loaded('pdo'),
            'tokenizer' => extension_loaded('tokenizer'),
            'xml' => extension_loaded('xml'),
        ];

        $allMet = !in_array(false, $requirements);

        return view('installer.requirements', compact('requirements', 'allMet'));
    }

    /**
     * Show the configuration page.
     */
    public function configuration()
    {
        if ($this->isInstalled()) {
            return redirect('/');
        }

        return view('installer.configuration');
    }

    /**
     * Save configuration and create .env file.
     */
    public function saveConfiguration(Request $request)
    {
        if ($this->isInstalled()) {
            return redirect('/');
        }

        $request->validate([
            'website_name' => 'required|string|max:255',
            'db_host' => 'required|string',
            'db_name' => 'required|string',
            'db_user' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        // Read the .env.example file
        $envContent = File::get(base_path('.env.example'));

        // Replace database configuration
        $envContent = str_replace('DB_HOST=127.0.0.1', 'DB_HOST=' . $request->db_host, $envContent);
        $envContent = str_replace('DB_DATABASE=laravel', 'DB_DATABASE=' . $request->db_name, $envContent);
        $envContent = str_replace('DB_USERNAME=root', 'DB_USERNAME=' . $request->db_user, $envContent);
        $envContent = str_replace('DB_PASSWORD=', 'DB_PASSWORD=' . $request->db_password, $envContent);

        // Add website name
        $envContent = str_replace('APP_NAME=Laravel', 'APP_NAME="' . $request->website_name . '"', $envContent);

        // Write to .env file
        File::put(base_path('.env'), $envContent);

        return redirect()->route('install.install');
    }

    /**
     * Show the installation page.
     */
    public function install()
    {
        if ($this->isInstalled()) {
            return redirect('/');
        }

        return view('installer.install');
    }

    /**
     * Run the installation process.
     */
    public function runInstall()
    {
        if ($this->isInstalled()) {
            return response()->json(['success' => false, 'message' => 'Already installed']);
        }

        try {
            // Run migrations
            Artisan::call('migrate', ['--force' => true]);

            // Seed the database
            Artisan::call('db:seed', ['--force' => true]);

            // Create installation marker file
            File::put(base_path('.installed'), date('Y-m-d H:i:s'));

            return response()->json([
                'success' => true,
                'message' => 'Installation completed successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Installation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the completion page.
     */
    public function complete()
    {
        if (!$this->isInstalled()) {
            return redirect()->route('install.requirements');
        }

        return view('installer.complete');
    }
}

