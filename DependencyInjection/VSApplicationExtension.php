<?php namespace VS\ApplicationBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VSApplicationExtension extends AbstractResourceExtension
{
    /**
     * {@inheritDoc}
     */
    public function load( array $config, ContainerBuilder $container )
    {
        $config = $this->processConfiguration( $this->getConfiguration([], $container), $config );
        $loader = new Loader\YamlFileLoader( $container, new FileLocator( __DIR__.'/../Resources/config' ) );
        $loader->load( 'services.yml' );
        
        // Register resources
        $this->registerResources( 'vs_application', $config['orm_driver'], $config['resources'], $container );
        
        // Set values need to be accesible from controller
        $container->setParameter( 'vs_application.multi_site', $config[ 'multi_site' ] );
        $container->setParameter( 'vs_application.taxonomy', $config[ 'taxonomy' ] );
    }
}
