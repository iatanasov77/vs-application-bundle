<?php namespace VS\ApplicationBundle\DataFixtures\VankosoftApplicationFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use VS\ApplicationBundle\DataFixtures\AbstractResourceFixture;

final class ApplicationSiteFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'application_site';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode( 'title' )->end()
        ;
    }
}
