<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Vankosoft\CmsBundle\Model\PageCategoryInterface;
use Vankosoft\ApplicationBundle\Component\SlugGenerator;

class PageCategoriesExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var RepositoryInterface */
    private $taxonomyRepository;
    
    /** @var FactoryInterface */
    private $taxonFactory;
    
    /** @var FactoryInterface */
    private $pageCategoriesFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    /** @var SlugGenerator */
    private $slugGenerator;
    
    public function __construct(
        RepositoryInterface $taxonomyRepository,
        FactoryInterface $taxonFactory,
        FactoryInterface $pageCategoriesFactory,
        SlugGenerator $slugGenerator
    ) {
        $this->taxonomyRepository       = $taxonomyRepository;
        $this->taxonFactory             = $taxonFactory;
        $this->pageCategoriesFactory    = $pageCategoriesFactory;
        $this->slugGenerator            = $slugGenerator;
        
        $this->optionsResolver          = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): PageCategoryInterface
    {
        $options                    = $this->optionsResolver->resolve( $options );
        var_dump( $options['locale'] ); die;
        $taxonomyRootTaxonEntity    = $this->taxonomyRepository->findByCode( $options['taxonomy_code'] )->getRootTaxon();
        $pageCategoryEntity         = $this->pageCategoriesFactory->createNew();
        
        $taxonEntity                = $this->taxonFactory->createNew();
        $slug                       = $this->slugGenerator->generate( $options['title'] );
        
        $taxonEntity->setCode( $slug );
        $taxonEntity->setCurrentLocale( $options['locale'] );
        $taxonEntity->getTranslation()->setName( $options['title'] );
        $taxonEntity->getTranslation()->setDescription( $options['description'] );
        $taxonEntity->getTranslation()->setSlug( $slug );
        $taxonEntity->getTranslation()->setTranslatable( $taxonEntity );
        
        $taxonEntity->setParent( $taxonomyRootTaxonEntity );
        $pageCategoryEntity->setTaxon( $taxonEntity );
        
        return $pageCategoryEntity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'title', null )
            ->setAllowedTypes( 'title', ['string'] )
            
            ->setDefault( 'description', null )
            ->setAllowedTypes( 'description', ['string'] )
            
            ->setDefault( 'locale', 'en_US' )
            ->setAllowedTypes( 'locale', ['string'] )
            
            ->setDefault( 'taxonomy_code', null )
            ->setAllowedTypes( 'taxonomy_code', ['string'] )
        ;
    }
    
}
