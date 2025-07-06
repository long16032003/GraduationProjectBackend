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
            'flat' => Permission::all(),
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
        Permission::group(['default' => 'Default'], static function() {
            Permission::resource(['user' => 'Quản lý nhân viên']);
            Permission::resource(['role' => 'Phân quyền']);
            Permission::resource(['customer' => 'Quản lý khách hàng']);
            Permission::resource(['post' => 'Quản lý bài viết']);
            Permission::resource(['order' => 'Quản lý đơn gọi món']);
            Permission::resource(['ingredient' => 'Quản lý nguyên liệu']);
            Permission::resource(['product' => 'Quản lý sản phẩm']);
            Permission::resource(['bill' => 'Quản lý hóa đơn']);
            Permission::resource(['site-setting' => 'Quản lý cấu hình']);
            Permission::resource(['enter-ingredient' => 'Quản lý nhập nguyên liệu']);
            Permission::resource(['export-ingredient' => 'Quản lý xuất nguyên liệu']);
            Permission::resource(['dish' => 'Quản lý món ăn']);
            Permission::resource(['dish-category' => 'Quản lý danh mục món ăn']);
            Permission::resource(['reservation' => 'Quản lý đặt bàn']);
            Permission::resource(['table' => 'Quản lý bàn']);
            Permission::resource(['promotion' => 'Quản lý khuyến mãi']);
            Permission::resource(['staff' => 'Quản lý nhân viên']);
            Permission::resource(['media' => 'Quản lý media'], [], ['create' => 'Tải lên']);
            Permission::resource(['statistics' => 'Thống kê']);
        });

//        Permission::group(['web' => 'Web'], static function () {
//            Permission::resource(['post' => 'Post'], static function () {
//                Permission::action('browse', 'Browse post');
//                Permission::actions(['create', 'delete']);
//                Permission::resource(['comment' => 'Comment'], ['create', 'delete']);
//            });
//        });
    }
}
