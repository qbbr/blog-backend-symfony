<?php

namespace App\Command;

use App\Repository\TagRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupTagsCommand extends Command
{
    protected static $defaultName = 'app:cleanup-tags';
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Remove unused tags');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = $this->tagRepository->removeAllEmpty();
        $output->writeln(sprintf('Removed: <fg=green>%d</>', $count));

        return Command::SUCCESS;
    }
}
