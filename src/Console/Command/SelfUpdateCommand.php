<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console\Command;

use Humbug\SelfUpdate\Strategy\GithubStrategy;
use Humbug\SelfUpdate\Updater;
use Leviy\ReleaseTool\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function sprintf;

final class SelfUpdateCommand extends Command
{
    /**
     * @var Updater
     */
    private $updater;

    public function __construct(Updater $updater)
    {
        parent::__construct();

        $this->updater = $updater;

        /** @var GithubStrategy $strategy */
        $strategy = $this->updater->getStrategy();
        $strategy->setPackageName('leviy/release-tool');
        $strategy->setPharName('release-tool.phar');
        $strategy->setCurrentLocalVersion(Application::VERSION);
    }

    protected function configure(): void
    {
        $this
            ->setName('self-update')
            ->setDescription('Updates release-tool.phar to the latest version');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->updater->hasUpdate()) {
            $output->writeln('<info>You are already using the latest version.</info>');

            return 0;
        }

        $output->writeln(sprintf('Updating to version <info>%s</info>.', $this->updater->getNewVersion()));
        $output->writeln('Downloading...');

        $this->updater->update();

        return 0;
    }
}