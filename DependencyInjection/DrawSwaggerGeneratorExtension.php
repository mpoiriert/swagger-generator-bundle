<?php

namespace Draw\SwaggerGeneratorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DrawSwaggerGeneratorExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $container->setParameter('draw_swagger.template_dir', realpath(__DIR__.'/../Resources/templates'));
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);


        $container->setParameter("draw_swagger_generator.twig_extension", $config['twig_extension']);
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->setParameter('draw_swagger_generator_template', realpath(__DIR__.'/../Resources/templates'));
    }
}
