<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

// see https://github.com/ikoene/symfony-micro
class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    const CONFIG_EXTS = '.{yaml}';

    /*
     * Set an Environment Variable in Apache Configuration
     *   SetEnv APP_ENVIRONMENT prod
     * for production setting instead of having www/app.php and www/app_dev.php
     * This approach is described in
     *   https://www.pmg.com/blog/symfony-no-app-dev/
     */
    public static function fromEnvironment()
    {
        $env = getenv('APP_ENVIRONMENT');
        if (false === $env) {
            $env = 'dev';
            $debug = true;
        }
        else {
            $debug = filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN);
        }

        return new self($env, $debug);
    }

    /*
     * {@inheritDoc}
     */
    public function registerBundles(): iterable
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),

            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Twig\Extra\TwigExtraBundle\TwigExtraBundle(),

            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),

            // $slug = $this->get('cocur_slugify')->slugify('Hello World!');
            // see https://github.com/cocur/slugify#user-content-symfony2
            new \Cocur\Slugify\Bridge\Symfony\CocurSlugifyBundle(),

            new \Symfony\Bundle\MonologBundle\MonologBundle(), // required by JMS\TranslationBundle\JMSTranslationBundle

            // translate routes
            new \JMS\I18nRoutingBundle\JMSI18nRoutingBundle(),
            // not required, but recommended for better extraction
            new \JMS\TranslationBundle\JMSTranslationBundle(),

            // https://github.com/a-r-m-i-n/scssphp-bundle
            new \Armin\ScssphpBundle\ScssphpBundle(),

            // menu
            // see http://symfony.com/doc/current/bundles/KnpMenuBundle/index.html
            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),

            // converturls filter
            new \Liip\UrlAutoConverterBundle\LiipUrlAutoConverterBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
        }

        return $bundles;
    }

    // see https://github.com/symfony/symfony-standard/blob/master/app/AppKernel.php
    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->getEnvironment();
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var/logs';
    }

    public function getConfigDir()
    {
        return $this->getProjectDir() . '/config';
    }

    /*
     * {@inheritDoc}
     */
    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $loader->load($this->getConfigDir() . '/config_' . $this->getEnvironment() . '.yaml');
        $loader->load($this->getConfigDir() . '/services.yaml');
    }

    /*
     * {@inheritDoc}
     */
    protected function configureRoutes(RoutingConfigurator $routes)
    {
        $confDir = $this->getConfigDir();

        $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS);
        $routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS);
        $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS);
    }
}
