<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Commands\Reusables\DatabaseRelatedTrait;
use Artisan;

class ResetCaeruDatabase extends Command
{
    use DatabaseRelatedTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caeru_db:reset {company_code=main}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset database of caeru project. It will revert all migration files, effectively drop all tables.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $company_code = $this->argument('company_code');
        $migration_folder = "";

        if ($company_code === 'main') {

            $this->info('Reseting main database of Caeru project...');

            $migration_folder = $this->changeDatabaseConnectionTo();

        } else {

            $this->info('Reseting database of company: ' . $company_code . '...');

            if (!$migration_folder = $this->changeDatabaseConnectionTo($company_code)) {
                $this->info('Sorry, the database: ' . $company_code . ' does not exists!');
                return;
            }

        }

        Artisan::call('migrate:reset', [
            '--path' => $migration_folder,
        ]);

        $this->info('Reseting complete sucessfully!');

    }
}
