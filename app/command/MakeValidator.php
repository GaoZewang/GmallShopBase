<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use support\Db;

class MakeValidator extends Command
{
    protected static $defaultName = 'make:validator';
    protected static $defaultDescription = 'Generate Respect validator from table';

    protected function configure()
    {
        $this
            ->addArgument('module', InputArgument::REQUIRED, 'module name (admin/api/app)')
            ->addArgument('table', InputArgument::REQUIRED, 'table name')
            ->addArgument('class', InputArgument::OPTIONAL, 'class name')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $module = strtolower($input->getArgument('module'));
        $table  = env('DB_PREFIX', '').$input->getArgument('table');
        $force  = $input->getOption('force');
        $class  = ucwords ($input->getArgument('class'));

        /* ========= 检查表 ========= */
        $exists = Db::select("
            SELECT TABLE_NAME
            FROM information_schema.tables
            WHERE table_schema = DATABASE()
            AND table_name = ?
        ", [$table]);

        if (!$exists) {
            $output->writeln("<error>Table [$table] not exists</error>");
            return self::FAILURE;
        }

        /* ========= 获取字段 ========= */
        $columns = Db::select("SHOW FULL COLUMNS FROM `$table`");

        $rulesStr = $this->buildSceneRules($columns);

        /* ========= 模板 ========= */
        // $templatePath = base_path('resources/templates/validator_template.txt');

        $templatePath = base_path()
            .DIRECTORY_SEPARATOR.'console'
            .DIRECTORY_SEPARATOR. 'generate'
            .DIRECTORY_SEPARATOR. 'template'
            .DIRECTORY_SEPARATOR. 'validator_template.txt';

        if (!file_exists($templatePath)) {
            $output->writeln("<error>Template not found: $templatePath</error>");
            return self::FAILURE;
        }

        $namespace = $module === 'app'
            ? "app\\validator"
            : "app\\{$module}\\validate";

        $content = file_get_contents($templatePath);

        $content = str_replace(
            ['{{namespace}}','{{module}}', '{{class}}', '{{rules}}'],
            [$namespace,$module, $class, $rulesStr],
            $content
        );

        /* ========= 写入 ========= */
        $dir = $module === 'app'
            ? base_path("app/validate")
            : base_path("app/$module/validate");

        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $file = "$dir/{$class}Validate.php";

        if (file_exists($file) && !$force) {
            $output->writeln("<comment>File exists, use -f to overwrite</comment>");
            return self::SUCCESS;
        }

        file_put_contents($file, $content);

        $output->writeln(
            $force
                ? "<info>✔ Validator overwritten: $file</info>"
                : "<info>✔ Validator created: $file</info>"
        );

        return self::SUCCESS;
    }


    /* =========================================================
       核心：生成 list/info/add/edit/del 多场景规则
       ========================================================= */
    private function buildSceneRules($columns): string
    {

        $list=$info=$add=$edit=$del=[""];
        $maxLen = 0;

        // 先算最大字段长度（用于对齐）
        foreach ($columns as $col) {
            $maxLen = max($maxLen, strlen($col->Field));
        }

        foreach ($columns as $col) {
            $field = $col->Field;
            $type  = $col->Type;
            $nullable = strtolower($col->Null) === 'yes';
            $rule = $this->mapRule($type, $nullable);
            $pad = str_pad("'$field'", $maxLen + 2); // +2 因为引号
            if ($field === 'id') {
                $idRule = "v::intVal()->positive()";
                $edit[] = "                   $pad => $idRule,";
                $info[] = "                   $pad => $idRule,";
                $del[]  = "                   $pad => $idRule,";
                continue;
            }
            $add[]  = "                    $pad => $rule,";
            $edit[] = "                   $pad => v::optional($rule),";
            $list[] = "                   $pad => v::optional($rule),";
        }



        $scene = function ($name, $rows) {
            if (!$rows) return '';
            $rows = implode("\n", $rows);
            return <<<PHP

                '$name' => [$rows
                ],
PHP;
        };
        return
            $scene('add',  $add ) .
            $scene('edit', $edit) .
            $scene('list', $list) .
            $scene('info', $info) .
            $scene('del',  $del);
    }





    /* =========================================================
       数据库字段 → Respect\Validation 映射
       ========================================================= */
    private function mapRule(string $type, bool $nullable): string
    {
        $rule = "v::alwaysValid()";

        if (stripos($type, 'int') !== false) {
            $rule = "v::intVal()";
        }
        elseif (stripos($type, 'decimal') !== false || stripos($type, 'float') !== false) {
            $rule = "v::numericVal()";
        }
        elseif (stripos($type, 'varchar') !== false || stripos($type, 'text') !== false) {
            preg_match('/\((\d+)\)/', $type, $m);
            $len = $m[1] ?? 255;
            $rule = "v::stringType()->length(0,$len)";
        }
        elseif (stripos($type, 'date') !== false || stripos($type, 'timestamp') !== false) {
            $rule = "v::date()";
        }

        if (!$nullable) {
            $rule .= "->notEmpty()";
        }

        return $rule;
    }
}
