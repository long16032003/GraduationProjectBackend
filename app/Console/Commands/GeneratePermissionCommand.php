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

        $data = [
            'tree' => Permission::structure(),
            'permissions' => Permission::all(),
        ];

        /** @var \Illuminate\Filesystem\Filesystem $files */
        $files = $this->laravel['files'];

        $files->replace(
            $this->laravel->configPath('permission.php'),
            '<?php return ' . var_export($data, true) . ';'
        );

        return Command::SUCCESS;
    }

    private function registerPermissions(): void
    {
        Permission::group(['web' => 'Web'], static function() {
            Permission::resource(['post' => 'Post'], static function() {
                Permission::actions(['create', 'delete']);
                Permission::resource(['comment' => 'Comment'], ['create', 'delete']);
            });
        });
        Permission::group(['api' => 'Api Group'], static function() {
            Permission::resource(['news' => 'News'], static function() {
                Permission::actions(['create', 'delete']);
                Permission::resource(['comment' => 'Comment'], ['create', 'delete']);
            });
        });
    }
}
