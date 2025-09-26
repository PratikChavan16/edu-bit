<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ListTables extends Command
{
    protected $signature = 'list:tables';
    protected $description = 'List all database tables';

    public function handle()
    {
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
        
        $this->info("Existing tables:");
        foreach ($tables as $table) {
            $this->line("- {$table->name}");
        }
    }
}
