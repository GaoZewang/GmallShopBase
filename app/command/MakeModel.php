<?php

namespace app\command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeModel extends BaseCommand
{
    protected static $defaultName = 'make:model';
    protected static $defaultDescription = 'Generate model from table';
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 在这里添加你的命令逻辑
        $data=$this->check($input, $output,'model');
        if($data['code']!=200){
            $msg=$data['msg'];
            $output->writeln("<error>>Model overwritten: $msg</error>");
        }

        // ========= 模板 =========
        $templatePath = base_path()
            .DIRECTORY_SEPARATOR.'console'
            .DIRECTORY_SEPARATOR. 'generate'
            .DIRECTORY_SEPARATOR. 'template'
            .DIRECTORY_SEPARATOR. 'model_template.txt';
        if (!$templatePath || !file_exists($templatePath)) {
            echo "Template file not found: " . $templatePath . "\n";
            exit(1);
        }
        $template = file_get_contents($templatePath);
        $fillable = implode("',\n        '", $data['fields']);
        $content  = str_replace(
            ['{{module}}', '{{table}}', '{{class}}', '{{fillable}}'],
            [$data['module'], $data['table'], $data['class'], $fillable],
            $template
        );
        $file=$data['file'];
        file_put_contents($file, $content);
        $output->writeln("<info>Model created: $file</info>");
        return self::SUCCESS;
    }

}
