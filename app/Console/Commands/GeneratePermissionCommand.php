<?php

namespace App\Console\Commands;

use App\Permission;
use Illuminate\Console\Command;

class GeneratePermissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate permision configuration file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->registerPermissions();

        /** @var \Illuminate\Filesystem\Filesystem $files */
        $files = $this->laravel['files'];

        $files->replace(
            $this->laravel->configPath('permission.php'),
            '<?php return ' . var_export(Permission::structure(), true) . ';'
        );

        return Command::SUCCESS;
    }

    private function registerPermissions(): void
    {
        Permission::group(['web' => 'Web Group'], static function() {
            Permission::resource(['post' => 'Post Management'], static function() {
                Permission::action('create', 'Create post');
                Permission::action('delete', 'Delete post');

                Permission::resource(['comment' => 'Comment Management'], static function() {
                    Permission::action('create', 'Create comment');
                    Permission::action('delete', 'Delete comment');
                });
            });
        });
        Permission::group(['api' => 'Api Group'], static function() {
            Permission::resource(['news' => 'News Management'], static function() {
                Permission::action('update', 'Update existing news');
                Permission::action('delete', 'Delete news');

                Permission::resource(['comment' => 'Comment Management'], static function() {
                    Permission::action('create', 'Create comment');
                    Permission::action('delete', 'Delete comment');
                });
            });
        });
    }
}
