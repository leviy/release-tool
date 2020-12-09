<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console;

use Leviy\ReleaseTool\Configuration\CredentialsConfiguration;
use Leviy\ReleaseTool\Configuration\MissingConfigurationException;
use Leviy\ReleaseTool\GitHub\GitHubRepositoryParser;
use Leviy\ReleaseTool\Vcs\Git;
use RuntimeException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;
use function file_exists;
use function file_get_contents;
use function getenv;
use function rtrim;
use function sprintf;

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

    /**
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    private function loadUserConfiguration(ContainerBuilder $container): void
    {
        $homeDirectory = $this->getHomeDirectory();

        $configFile = $homeDirectory . '/.release-tool/auth.yml';

        if (!file_exists($configFile)) {
            throw new MissingConfigurationException(
                sprintf('The file %s needs to exist and contain a GitHub access token.', $configFile)
            );
        }

        $yamlContents = file_get_contents($configFile);

        if (!$yamlContents) {
            throw new RuntimeException('Error reading the configuration file');
        }

        $config = Yaml::parse($yamlContents);

        $processor = new Processor();
        $configuration = new CredentialsConfiguration();
        $processedConfiguration = $processor->processConfiguration(
            $configuration,
            $config
        );

        $container->setParameter('credentials.github.token', $processedConfiguration['github']['token']);
    }

    private function getHomeDirectory(): string
    {
        $homeDirectory = getenv('HOME') ?: getenv('USERPROFILE');

        if (!$homeDirectory) {
            throw new RuntimeException('Unable to determine the home directory from HOME or USERPROFILE');
        }

        return rtrim($homeDirectory, '/\\');
    }
}
