<?php

class AppKernel extends \Symfony\Component\HttpKernel\Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Draw\SwaggerBundle\DrawSwaggerBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new Draw\DrawBundle\DrawDrawBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(\Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerBaseClass()
    {
        return '\PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer';
    }
}