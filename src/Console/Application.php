<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console;

use Leviy\ReleaseTool\Configuration\CredentialsConfiguration;
use Leviy\ReleaseTool\GitHub\GitHubRepositoryParser;
use Leviy\ReleaseTool\Vcs\Git;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;
use function file_get_contents;
use function getenv;
use function rtrim;

final class Application extends SymfonyApplication
{
    private const NAME = 'Leviy Release Tool';

    public const VERSION = '@package_version@';

    public function __construct(ContainerBuilder $container)
    {
        parent::__construct(self::NAME, VersionHelper::removeVersionPrefix(self::VERSION));

        $this->buildContainer($container);
    }

    private function buildContainer(ContainerBuilder $container): void
    {
        $this->loadConfigurationFiles($container);
        $this->registerCompilerPasses($container);
        $this->parseGithubRepositoryDetails($container);
        $this->loadUserConfiguration($container);

        $container->compile();

        /** @var CommandLoaderInterface $commandLoader */
        $commandLoader = $container->get('console.command_loader');
        $this->setCommandLoader($commandLoader);
    }

    private function loadConfigurationFiles(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');
        $loader->load('github.yaml');
        $loader->load('commands.yaml');
    }

    private function registerCompilerPasses(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AddConsoleCommandPass());
    }

    private function parseGithubRepositoryDetails(ContainerBuilder $container): void
    {
        $githubParser = new GitHubRepositoryParser();
        $url = Git::execute('remote get-url origin')[0];
        $container->setParameter('github.owner', $githubParser->getOwner($url));
        $container->setParameter('github.repo', $githubParser->getRepository($url));
    }

    private function loadUserConfiguration(ContainerBuilder $container): void
    {
        $homeDirectory = rtrim(getenv('HOME') ?: getenv('USERPROFILE'), '/\\');

        $config = Yaml::parse(
            file_get_contents($homeDirectory . '/.release-tool/auth.yml')
        );

        $processor = new Processor();
        $configuration = new CredentialsConfiguration();
        $processedConfiguration = $processor->processConfiguration(
            $configuration,
            $config
        );

        $container->setParameter('credentials.github.token', $processedConfiguration['github']['token']);
    }
}
