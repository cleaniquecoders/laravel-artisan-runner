<?php

namespace CleaniqueCoders\ArtisanRunner\Commands;

use CleaniqueCoders\ArtisanRunner\Actions\DiscoverCommandsAction;
use CleaniqueCoders\ArtisanRunner\Actions\ResolveCommandsAction;
use Illuminate\Console\Command;

class DiscoverCommandsCommand extends Command
{
    protected $signature = 'artisan-runner:discover
        {--dry-run : List discovered commands without writing to config}
        {--output=table : Output format: table or json}';

    protected $description = 'Discover available Artisan commands and their parameter schemas';

    public function handle(DiscoverCommandsAction $discoverer, ResolveCommandsAction $resolver): int
    {
        $commands = $discoverer->discover();

        if ($this->option('output') === 'json') {
            $this->line(json_encode($commands, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $this->info(sprintf('Discovered %d commands.', count($commands)));
        $this->newLine();

        $rows = [];
        foreach ($commands as $name => $config) {
            $rows[] = [
                $name,
                $config['group'],
                $config['label'],
                count($config['parameters']),
            ];
        }

        $this->table(
            ['Command', 'Group', 'Label', 'Parameters'],
            $rows,
        );

        if ($this->option('dry-run')) {
            $this->info('Dry run — no changes written.');

            return self::SUCCESS;
        }

        $configPath = config_path('artisan-runner.php');

        if (! file_exists($configPath)) {
            $this->error('Config file not found. Run: php artisan vendor:publish --tag=artisan-runner-config');

            return self::FAILURE;
        }

        $this->writeAllowedCommands($configPath, $commands);
        $resolver->flush();

        $this->info('Updated allowed_commands in config/artisan-runner.php');

        return self::SUCCESS;
    }

    protected function writeAllowedCommands(string $configPath, array $commands): void
    {
        $exported = $this->exportArray($commands, 2);

        $content = file_get_contents($configPath);

        $pattern = "/([\'\"]allowed_commands[\'\"]\s*=>\s*)\[.*?\](,?\s*\n)/s";

        $replacement = "$1{$exported}$2";

        $updated = preg_replace($pattern, $replacement, $content, 1);

        file_put_contents($configPath, $updated);
    }

    protected function exportArray(array $array, int $indentLevel = 1): string
    {
        $indent = str_repeat('    ', $indentLevel);
        $innerIndent = str_repeat('    ', $indentLevel + 1);
        $deepIndent = str_repeat('    ', $indentLevel + 2);
        $deeperIndent = str_repeat('    ', $indentLevel + 3);

        $lines = ['['];

        foreach ($array as $command => $config) {
            $lines[] = "{$innerIndent}'{$command}' => [";
            $lines[] = "{$deepIndent}'label' => ".var_export($config['label'], true).',';
            $lines[] = "{$deepIndent}'description' => ".var_export($config['description'], true).',';
            $lines[] = "{$deepIndent}'group' => ".var_export($config['group'], true).',';

            if (empty($config['parameters'])) {
                $lines[] = "{$deepIndent}'parameters' => [],";
            } else {
                $lines[] = "{$deepIndent}'parameters' => [";
                foreach ($config['parameters'] as $param) {
                    $paramParts = [];
                    $paramParts[] = "'name' => ".var_export($param['name'], true);
                    $paramParts[] = "'type' => ".var_export($param['type'], true);
                    $paramParts[] = "'label' => ".var_export($param['label'], true);
                    $paramParts[] = "'default' => ".var_export($param['default'], true);

                    if (isset($param['required'])) {
                        $paramParts[] = "'required' => ".var_export($param['required'], true);
                    }

                    $lines[] = "{$deeperIndent}[".implode(', ', $paramParts).'],';
                }
                $lines[] = "{$deepIndent}],";
            }

            $lines[] = "{$innerIndent}],";
        }

        $lines[] = "{$indent}]";

        return implode("\n", $lines);
    }
}
