<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console\Command;

use Humbug\SelfUpdate\Strategy\GithubStrategy;
use Humbug\SelfUpdate\Updater;
use Leviy\ReleaseTool\Console\Application;
use Leviy\ReleaseTool\Console\VersionHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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

        $strategy = new GithubStrategy();
        $strategy->setPackageName('leviy/release-tool');
        $strategy->setPharName('release-tool.phar');
        $strategy->setCurrentLocalVersion(Application::VERSION);

        $updater->setStrategyObject($strategy);

        $this->updater = $updater;
    }

    protected function configure(): void
    {
        $this
            ->setName('self-update')
            ->setDescription('Updates release-tool.phar to the latest version')
            ->addOption(
                'rollback',
                'r',
                InputOption::VALUE_NONE,
                'Revert to an older version of the release tool'
            )
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command checks for newer versions of the release
tool and if found, installs the latest.

<info>%command.full_name%</info>
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('rollback')) {
            $output->writeln('Rolling back to the previous version.');

            $this->updater->rollback();

            return 0;
        }

        if (!$this->updater->hasUpdate()) {
            $output->writeln(
                sprintf(
                    '<info>You are already using version %s.</info>',
                    VersionHelper::removeVersionPrefix(Application::VERSION)
                )
            );

            return 0;
        }

        $output->writeln(
            sprintf(
                'Updating to version <info>%s</info>.',
                VersionHelper::removeVersionPrefix($this->updater->getNewVersion())
            )
        );
        $output->writeln('Downloading...');

        $this->updater->update();

        $output->writeln(
            sprintf(
                'Use <info>release-tool self-update --rollback</info> to revert to version <comment>%s</comment>.',
                VersionHelper::removeVersionPrefix($this->updater->getOldVersion())
            )
        );

        return 0;
    }
}
