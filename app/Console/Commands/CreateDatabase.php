<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:database {dbname} {connection?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'We have crated this console to create DB so we dont have create database manually.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $dbname = $this->argument('dbname');

            $conn = mysqli_connect(
                config('database.connections.mysql.host'),
                env('DB_USERNAME'),
                env('DB_PASSWORD')
            );
            if (!$conn) {
                $this->info("Connection failure");
            }

            $sql = 'CREATE Database IF NOT EXISTS '.$dbname;

            mysqli_query($conn, $sql);
            $this->info("Database '$dbname' created if not existed");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
