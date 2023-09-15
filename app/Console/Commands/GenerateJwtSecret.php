<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class GenerateJwtSecret extends Command
{
    protected $signature = 'jwt:secret';

    protected $description = 'Generate a JWT secret key jwt package being used';

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
