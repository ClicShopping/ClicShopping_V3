<?php

namespace LLPhant\Experimental\Agent\Render;

use LLPhant\Experimental\Agent\Task;

class CLIOutputUtils implements OutputAgentInterface
{
    public function render(string $message, bool $verbose): void
    {
        $message = self::truncateString($verbose, $message);
        echo $message.PHP_EOL;
    }

    public function renderTitle(string $title, string $message, bool $verbose): void
    {
        $message = self::truncateString($verbose, $message);

        $separator = \str_repeat('*', 80);

        $this->render($separator, $verbose);
        $this->render($title.' *** '.$message.' ***', $verbose);
        $this->render($separator, $verbose);
    }

    public function renderTitleAndMessageGreen(string $title, string $message, bool $verbose): void
    {
        $message = self::truncateString($verbose, $message, $title);
        $this->renderTitle('🍏 '.$title, $message, $verbose);
    }

    public function renderTitleAndMessageOrange(string $title, string $message, bool $verbose): void
    {
        $message = self::truncateString($verbose, $message, $title);
        $this->renderTitle('🔸 '.$title, $message, $verbose);
    }

    public function renderResult(string $result): void
    {
        $this->renderTitle('🏆️ Success! 🏆️ Result:', $result, true);
    }

    /**
     * @param  Task[]  $tasks
     */
    public function printTasks(bool $verbose, array $tasks, ?Task $currentTask = null): void
    {
        $separator = '------------------'.PHP_EOL;
        $liItems = $separator.'Tasks'.PHP_EOL;
        foreach ($tasks as $task) {
            if ($currentTask === $task) {
                $liItems .= "\t⚙️ - {$task->name} ({$task->description})".PHP_EOL;

                continue;
            }

            if (is_null($task->result)) {
                $liItems .= "\t⚪️ - {$task->name} ({$task->description})".PHP_EOL;

                continue;
            }

            $result = self::truncateString($verbose, $task->result, $task->name);

            if ($task->wasSuccessful) {
                $liItems .= "\t🟢 - {$task->name} ({$task->description}) - {$result}".PHP_EOL;
            } else {
                $liItems .= "\t🔴 - {$task->name} ({$task->description})".PHP_EOL;
            }
        }
        $liItems .= $separator;

        $this->render($liItems, $verbose);
    }

    private static function truncateString(bool $verbose, string $message, ?string $title = null): string
    {
        $maxSize = 250;
        if ($title) {
            $maxSize -= strlen($title);
        }

        if (! $verbose) {
            $message = str_replace('\n', '', $message);
            $message = str_replace('\r', '', $message);
            if (strlen($message) > $maxSize) {
                $message = substr($message, 0, $maxSize).'...';
            }
        }

        return $message;
    }
}
