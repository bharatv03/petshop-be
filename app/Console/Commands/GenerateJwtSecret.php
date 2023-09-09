<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateJwtSecret extends Command
{
    protected $signature = 'jwt:secret';

    protected $description = 'Generate a JWT secret key jwt package being used';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $key = Str::random(64);

        $this->setJwtSecretInEnvironmentFile($key);

        $this->info("JWT secret key set successfully.");
    }

    protected function setJwtSecretInEnvironmentFile($key)
    {
        file_put_contents(base_path('.env'), "\nJWT_SECRET=$key\n", FILE_APPEND);
    }
}
