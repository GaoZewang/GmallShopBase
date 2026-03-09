<?php
/**
 * @Project   Gmall
 * @File      BaseCommand.php
 * @Author    MrGao
 * @Date      2026/2/1 20:16
 */

namespace app\command;


use support\Db;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
class BaseCommand extends Command
{
    protected static $defaultName = 'test:command';
    protected static $defaultDescription = '测试命令';


    protected function configure()
    {
        $this
            ->addArgument('module', InputArgument::REQUIRED, 'module name')
            ->addArgument('table', InputArgument::REQUIRED, 'table name')
            ->addArgument('class', InputArgument::OPTIONAL, 'class name')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing file');
    }


    protected function  check(InputInterface $input, OutputInterface $output,$type) : array{
        if(!in_array($type,['controller','model','validate','service'])){
            return [
                'code'  => 201,
                'msg'    => 'type error',
            ];
        }
        $module = strtolower($input->getArgument('module'));
        $table  = env('DB_PREFIX', '').$input->getArgument('table');
        $force  = $input->getOption('force');
        $class  = ucwords ($input->getArgument('class'));
        $paths=[
            'controller'=> base_path("app".DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR."controller"),
            'model'=>base_path("app".DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR."model"),
            'validate'=>base_path("app".DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR."validate"),
            'service'=>base_path("app".DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR."service")
        ];
        $files=[
            'controller'=>"$paths[$type]/{$class}Controller.php",
            'model'=>"$paths[$type]/{$class}Model.php",
            'validate'=>"$paths[$type]/{$class}Validate.php",
            'service'=>"$paths[$type]/{$class}Service.php"
        ];

        /*
        |--------------------------------------------------------------------------
        | 检查表是否存在
        |--------------------------------------------------------------------------
        */
        $exists = Db::select("
            SELECT TABLE_NAME
            FROM information_schema.tables
            WHERE table_schema = DATABASE()
            AND table_name = ?
        ", [$table]);

        if (!$exists) {
            return [
                'code'  => 202,
                'msg'    => 'Table does not exist',
            ];
        }
        // ========= 获取字段 =========
        $columns = Db::select("SHOW COLUMNS FROM `$table`");

        $fields  = array_column($columns, 'Field');

        /*
        |--------------------------------------------------------------------------
        | 路径
        |--------------------------------------------------------------------------
        */

        // 创建目录（如果不存在）
        if (!is_dir($paths[$type])) {
            mkdir($paths[$type], 0777, true);
        }
        /*
        |--------------------------------------------------------------------------
        | 文件存在判断
        |--------------------------------------------------------------------------
        */
        if (file_exists($files[$type]) && !$force) {
            return [
                'code'  => 203,
                'msg'    => 'File already exists: '.$files[$type].' (use -f to overwrite)',
            ];
        }
        return [
            'code'=>200 ,
            'module' => $module,
            'table'  => $input->getArgument('class'),
            'force'  => $force,
            'class'  => $class,
            'file'  => $files[$type],
            'fields' => $fields,
        ];
    }


}