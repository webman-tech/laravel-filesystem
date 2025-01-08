<?php

namespace WebmanTech\LaravelFilesystem\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use WebmanTech\LaravelFilesystem\Facades\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WebmanTech\LaravelFilesystem\Helper\ConfigHelper;

/**
 * @link https://github.com/laravel/framework/blob/11.x/src/Illuminate/Foundation/Console/StorageLinkCommand.php
 */
#[AsCommand(name: 'storage:link')]
class StorageLinkCommand extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('storage:link');
        $this->setDescription('Create the symbolic links configured for the application');
        $this->addOption('relative', null, InputOption::VALUE_NONE, 'Create the symbolic link using relative paths');
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Recreate existing symbolic links');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $relative = $input->getOption('relative');

        foreach ($this->links() as $link => $target) {
            if (file_exists($link) && !$this->isRemovableSymlink($link, $input->getOption('force'))) {
                $output->writeln("The [$link] link already exists.");
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
        return ConfigHelper::get('filesystems.links', []);
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
