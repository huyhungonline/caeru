<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Commands\Reusables\DatabaseRelatedTrait;
use Artisan;

class SeedCaeruDatabase extends Command
{
    use DatabaseRelatedTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caeru_db:seed {company_code=main} {--develop}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed data into databases of caeru project. It can be the main database or sub (client) database.';

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
        $develop_mode = $this->option('develop');
        $migration_folder = "";

        if ($company_code === 'main') {

            $this->info('Seeding main database of Caeru project...');

            $this->changeDatabaseConnectionTo();

            // Do nothing in this case, for now.
            $this->info('Sorry, this part has not been implemented yet!');
            return;

        } else {

            $this->info('Seeding database of company: ' . $company_code . '...');

            if ($this->changeDatabaseConnectionTo($company_code) === false) {
                $this->info('Sorry, the database: ' . $company_code . ' does not exists!');
                return;
            }

        }

        if ($develop_mode) {

            Artisan::call('db:seed', [
                '--class' => 'DevelopDatabaseSeeder',
            ]);

        } else {

            Artisan::call('db:seed');
        }

        $this->info('Seeding complete sucessfully!');
    }
}
