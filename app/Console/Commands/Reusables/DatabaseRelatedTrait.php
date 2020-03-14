<?php

namespace App\Console\Commands\Reusables;

use Config;
use DB;

trait DatabaseRelatedTrait
{
    /**
     * The path to the migration files of caeru_main database
     */
    private static $main_migration_folder = "/database/migrations/main/";
    
     /**
     * The path to the migration files of the sub caeru database of each company
     */
    private static $sub_migration_folder = "/database/migrations/sub/";


    /**
     * Change the database connection to either 'main' or 'sub' connection.
     * We have to change the connection settings accordingly.
     * And at the end return the path to the right migration files ('main' folder or 'sub' folder)
     *
     * @param   string      $company_code   the unique code of a company, default will be 'main'.
     * @return  string      the path to the migration files.
     */
    private function changeDatabaseConnectionTo($company_code='main')
    {
        $connection = $company_code == 'main' ? 'main' : 'sub';

        if ($company_code == 'main') {

            return $this::$main_migration_folder;

        } else {

            if (!DB::table('companies')->where('company_code', $company_code)->exists())
                return false;

            Config::set('database.default', 'sub');

            Config::set('database.connections.sub.database', 'caeru_' . $company_code);
            Config::set('database.connections.sub.prefix', 'caeru_' . $company_code . '_');

            DB::reconnect('sub');

            return $this::$sub_migration_folder;

        }
    }
}