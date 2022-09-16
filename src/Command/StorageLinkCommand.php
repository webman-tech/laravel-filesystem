<?php

namespace WebmanTech\LaravelFilesystem\Command;

use WebmanTech\LaravelFilesystem\Facades\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @link https://github.com/laravel/framework/blob/8.x/src/Illuminate/Foundation/Console/StorageLinkCommand.php
 */
class StorageLinkCommand extends Command
{
    protected static $defaultName = 'storage:link';
    protected static $defaultDescription = 'Create the symbolic links configured for the application';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addOption('relative', null, InputOption::VALUE_NONE, 'Create the symbolic link using relative paths');
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Recreate existing symbolic links');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $relative = $input->getOption('relative');

        foreach ($this->links() as $link => $target) {
            if (file_exists($link) && !$this->isRemovableSymlink($link, $input->getOption('force'))) {
                $output->writeln("Error: The [$link] link already exists.");
                continue;
            }

            if (is_link($link)) {
                File::delete($link);
            }

            if ($relative) {
                File::relativeLink($target, $link);
            } else {
                File::link($target, $link);
            }

            $output->writeln("The [$link] link has been connected to [$target].");
        }

        $output->writeln('The links have been created.');

        return self::SUCCESS;
    }

    /**
     * Get the symbolic links that are configured for the application.
     *
     * @return array
     */
    protected function links()
    {
        return config('plugin.kriss.webman-filesystem.filesystems.links', [
            public_path() . '/storage' => storage_path() . '/app/public',
        ]);
    }

    /**
     * Determine if the provided path is a symlink that can be removed.
     *
     * @param string $link
     * @param bool $force
     * @return bool
     */
    protected function isRemovableSymlink(string $link, bool $force): bool
    {
        return is_link($link) && $force;
    }
}
