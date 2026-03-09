<?php
/**
 * @Project   Gmall
 * @File      TestComand.php
 * @Author    MrGao
 * @Date      2026/2/1 20:16
 */

namespace app\command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestComand extends BaseCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 在这里添加你的命令逻辑
        $data=$this->check($input, $output,'controllerss');
        if($data['code']!=200){
            $msg=$data['msg'];
            $output->writeln("<error>>Controller overwritten: $msg</error>");
        }

        // ========= 模板 =========
        $templatePath = base_path()
            .DIRECTORY_SEPARATOR.'console'
            .DIRECTORY_SEPARATOR.'generate'
            .DIRECTORY_SEPARATOR.'template'
            .DIRECTORY_SEPARATOR.'controller_template.txt';
        if (!$templatePath || !file_exists($templatePath)) {
            echo "Template file not found: " . $templatePath . "\n";
            exit(1);
        }
        $template = file_get_contents($templatePath);
        // 替换模板中的占位符
        $content = str_replace(
            ['{{module}}', '{{class}}','{{table}}'],
            [$data['module'], $data['class'] , $data['table']],
            $template
        );
        $file=$data['file'];
        file_put_contents($file, $content);
        if ($data['force'] && file_exists($data['file'])) {
            $output->writeln("<error>Controller overwritten: $file</error>");
        } else {
            $output->writeln("<error>Controller created: $file</error>");
        }
        return self::SUCCESS;
    }
}