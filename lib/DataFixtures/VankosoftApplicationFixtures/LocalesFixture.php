<?php namespace VS\ApplicationBundle\DataFixtures\VankosoftApplicationFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use VS\ApplicationBundle\DataFixtures\AbstractResourceFixture;

final class LocalesFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'locales';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode( 'code' )->end()
        ;
    }
}
