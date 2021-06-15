<?php namespace VS\ApplicationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait PrependDoctrineMigrationsTrait
{
    private function prependDoctrineMigrations( ContainerBuilder $container ): void
    {
        if (
            ! $container->hasExtension( 'doctrine_migrations' )
            //|| ! $container->hasExtension( 'vankosoft_application_doctrine_migrations_extra' )
        ) {
            return;
        }
            
        if (
            $container->hasParameter( 'vankosoft_application.prepend_doctrine_migrations' ) &&
            ! $container->getParameter( 'vankosoft_application.prepend_doctrine_migrations' )
        ) {
            return;
        }
                
        $doctrineConfig = $container->getExtensionConfig( 'doctrine_migrations' );
        $container->prependExtensionConfig( 'doctrine_migrations', [
            'migrations_paths' => \array_merge( \array_pop( $doctrineConfig )['migrations_paths'] ?? [], [
                $this->getMigrationsNamespace() => $this->getMigrationsDirectory(),
            ]),
        ]);
        
        $container->prependExtensionConfig( 'vankosoft_application_doctrine_migrations_extra', [
            'migrations' => [
                $this->getMigrationsNamespace() => $this->getNamespacesOfMigrationsExecutedBefore(),
            ],
        ]);
        
        //file_put_contents( '/tmp/debug_doctrine_migrations_config_end', print_r( $this->debugExtensionConfig( $container, 'doctrine_migrations' ), true ) );
    }

    abstract protected function getMigrationsNamespace(): string;

    abstract protected function getMigrationsDirectory(): string;

    abstract protected function getNamespacesOfMigrationsExecutedBefore(): array;
}
