<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        try{
            $dbname = $this->argument('dbname');
            
            $new_db_name = "DB_".rand()."_".time();
            $new_mysql_username = "root";
            $new_mysql_password = "";

            $conn = mysqli_connect(
                config('database.connections.mysql.host'), 
                env('DB_USERNAME'), 
                env('DB_PASSWORD')
            );
            if(!$conn ) {
                $this->info("Connection failure");
            }

            $sql = 'CREATE Database IF NOT EXISTS '.$dbname;
            
            $exec_query = mysqli_query( $conn, $sql);
            $this->info("Database '$dbname' created if not existed");
        }
        catch (\Exception $e){
            $this->error($e->getMessage());
        }
    }
}
