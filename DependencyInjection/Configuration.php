<?php namespace Vankosoft\ApplicationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use Vankosoft\ApplicationBundle\Model\Locale;
use Vankosoft\ApplicationBundle\Model\Interfaces\LocaleInterface;
use Vankosoft\ApplicationBundle\Controller\LocalesController;
use Vankosoft\ApplicationBundle\Form\LocaleForm;

use Vankosoft\ApplicationBundle\Repository\ApplicationRepository;
use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use Vankosoft\ApplicationBundle\Model\Application;
use Vankosoft\ApplicationBundle\Form\ApplicationForm;

use Vankosoft\ApplicationBundle\Repository\SettingsRepository;
use Vankosoft\ApplicationBundle\Model\Settings;
use Vankosoft\ApplicationBundle\Controller\SettingsController;
use Vankosoft\ApplicationBundle\Form\SettingsForm;

use Vankosoft\ApplicationBundle\Repository\TaxonomyRepository;
use Vankosoft\ApplicationBundle\Model\Taxonomy;
use Vankosoft\ApplicationBundle\Controller\TaxonomyController;
use Vankosoft\ApplicationBundle\Form\TaxonomyForm;

use Vankosoft\ApplicationBundle\Model\TaxonImage;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonImageInterface;

use Vankosoft\ApplicationBundle\Repository\TaxonRepository;
use Vankosoft\ApplicationBundle\Model\Taxon;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;
use Vankosoft\ApplicationBundle\Form\TaxonForm;
//use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonTranslationType;
use Vankosoft\ApplicationBundle\Model\TaxonTranslation;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
//use Sylius\Component\Taxonomy\Factory\TaxonFactory;

use Vankosoft\ApplicationBundle\Model\Translation;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;

use Vankosoft\ApplicationBundle\Model\LogEntry;
use Vankosoft\ApplicationBundle\Repository\LogEntryRepository;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder    = new TreeBuilder( 'vs_application' );
        $rootNode       = $treeBuilder->getRootNode();
        
        // Main Config
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode( 'prepend_doctrine_migrations' )->defaultTrue()->end()
                ->scalarNode( 'orm_driver' )->defaultValue( SyliusResourceBundle::DRIVER_DOCTRINE_ORM )->cannotBeEmpty()->end()
                ->arrayNode( 'taxonomy' )
                    ->scalarPrototype()->end()
                ->end()
                ->scalarNode( 'locale' )->defaultValue( 'en_US' )->cannotBeEmpty()->end()
            ->end()
        ;
        
        // Menu Config
        $rootNode
            ->children()
                ->variableNode( 'menu' )->end()
            ->end()
        ;
        
        // Resources Config
        $this->addResourcesSection( $rootNode );
            
        return $treeBuilder;
    }

    private function addResourcesSection( ArrayNodeDefinition $node ): void
    {
        $node
            ->children()
                ->arrayNode( 'resources' )
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode( 'application' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Application::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( ApplicationInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( ApplicationRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( ApplicationForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode( 'settings' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Settings::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( ResourceInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( SettingsController::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( SettingsRepository::class )->cannotBeEmpty()->end()
                                        //->scalarNode( 'repository' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( SettingsForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode( 'taxonomy' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Taxonomy::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( ResourceInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( TaxonomyController::class )->cannotBeEmpty()->end()
                                        //->scalarNode( 'repository' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( TaxonomyRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( TaxonomyForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'taxon_image' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( TaxonImage::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( TaxonImageInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( ResourceController::class )->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode( 'taxon' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Taxon::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( TaxonInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->cannotBeEmpty()->end()
                                        //->scalarNode( 'repository' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( TaxonRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'form' )->defaultValue( TaxonForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                    
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue( TaxonTranslation::class )->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue( TaxonTranslationInterface::class )->cannotBeEmpty()->end()
                                                ->scalarNode('controller')->defaultValue( ResourceController::class )->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue( Factory::class )->end()
                                                //->scalarNode('form')->defaultValue( TaxonTranslationType::class )->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                
                            ->end()
                        ->end()
                        ->arrayNode( 'translation' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Translation::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( ResourceInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( TranslationRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode( 'logentry' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( LogEntry::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( ResourceInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->defaultValue( LogEntryRepository::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode( 'locale' )
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode( 'options' )->end()
                                ->arrayNode( 'classes' )
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode( 'model' )->defaultValue( Locale::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'interface' )->defaultValue( LocaleInterface::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'controller' )->defaultValue( LocalesController::class )->cannotBeEmpty()->end()
                                        ->scalarNode( 'repository' )->cannotBeEmpty()->end()
                                        ->scalarNode( 'factory' )->defaultValue( Factory::class )->end()
                                        ->scalarNode( 'form' )->defaultValue( LocaleForm::class )->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        
                    ->end()
                ->end()
            ->end()
        ;
    }
}
