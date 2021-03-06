<?php namespace VS\ApplicationBundle\DataFixtures\VankosoftApplicationFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use VS\ApplicationBundle\DataFixtures\AbstractResourceFixture;

final class MultiSiteSettingsFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'multisite_settings';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->booleanNode( 'maintenanceMode' )->defaultFalse()->end()
                
                ->scalarNode( 'siteTitle' )->end()
                ->scalarNode( 'theme' )->end()
                ->scalarNode( 'maintenancePage' )->end()
        ;
    }
}
