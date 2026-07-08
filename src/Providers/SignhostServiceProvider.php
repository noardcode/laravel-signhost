<?php

namespace Noardcode\LaravelSignhost\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Noardcode\LaravelSignhost\Console\Commands\FakeSignhostIdProofWebhookCommand;
use Noardcode\LaravelSignhost\Console\Commands\FakeSignhostWebhookCommand;

/**
 * Class SignhostServiceProvider
 *
 */
class SignhostServiceProvider extends ServiceProvider
{
    /**
     * The migrations that should be published.
     *
     * @var array
     */
    protected array $migrations = [
        'create_sh_transactions_table.php',
        'create_sh_transaction_files_table.php',
        'create_sh_transaction_file_links_table.php',
        'create_sh_transaction_receivers_table.php',
        'create_sh_transaction_activities_table.php',
        'update_sh_transactions_table_add_finalized.php',
    ];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->consoleFunctionality();
        }

        $this->loadRoutesFrom($this->getPackageBasePath('/routes/web.php'));
        $this->mergeConfigFrom($this->getPackageBasePath('/config/disks.php'), 'filesystems.disks');

        $this->commands([
            FakeSignhostWebhookCommand::class,
            FakeSignhostIdProofWebhookCommand::class,
        ]);
    }

    /**
     * Merge the signhost configuration file with the application's configuration.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom($this->getPackageBasePath('/config/signhost.php'), 'signhost');
    }

    /**
     * Module functionality only available from the console.
     *
     * @return void
     */
    protected function consoleFunctionality(): void
    {
        $timeStamp = Carbon::now();
        foreach ($this->migrations as $migrationFilename) {
            if ($this->migrationFileExists($migrationFilename)) {
                continue;
            }

            $timeStamp->addSecond();
            $this->publishes([
                $this->getPackageBasePath('database/migrations/'.$migrationFilename) => database_path('migrations/'.$timeStamp->format('Y_m_d_His').'_'.$migrationFilename),
            ], ['signhost-migrations', 'signhost-assets']);
        }

        $this->publishes([
            $this->getPackageBasePath('/config/signhost.php') => config_path('signhost.php'),
        ], ['signhost-config', 'signhost-assets']);
    }

    /**
     * Check if the migration file already exists in the migrations directory.
     *
     * @param  string  $migrationFilename
     * @return bool
     */
    protected function migrationFileExists(string $migrationFilename): bool
    {
        $len = strlen($migrationFilename);

        foreach (glob(database_path('migrations/*.php')) as $filename) {
            if ((substr($filename, -$len) === $migrationFilename)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  string|null  $file
     * @return string
     */
    public function getPackageBasePath(?string $file = null): string
    {
        $path = __DIR__.'/../../';
        $path .= ! empty($file) ? ltrim($file, '/') : '';

        return $path;
    }
}
