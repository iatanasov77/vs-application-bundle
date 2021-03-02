<?php namespace VS\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use VS\ApplicationBundle\Component\Slug;
use VS\ApplicationBundle\Form\TaxonForm;

class TaxonomyTaxonsController extends Controller
{
    use TaxonomyTreeDataTrait;
    
    public function index( Request $request ): Response
    {
        return new Response( "NOT IMPLEMENTED !!!" );
    }
    
    public function editTaxon( $taxonomyId, Request $request ): Response
    {
        $locale     = $request->getLocale();
        $rootTaxon  = $this->getTaxonomyRepository()->find( $taxonomyId )->getRootTaxon();
        
        $oTaxon     = $this->get( 'vs_application.factory.taxon' )->createNew();
        $oTaxon->setCurrentLocale( $locale );
        
        $form   = $this->createForm( TaxonForm::class, $oTaxon, [
            'data'      => $oTaxon,
            'method'    => 'POST',
            'rootTaxon' => $rootTaxon
        ]);
        
        return $this->render( '@VSApplication/Taxon/form/taxon.html.twig', [
            'form'          => $form->createView(),
            'taxonomyId'    => $request->attributes->get( 'taxonomyId' )
        ]);
    }
    
    public function handleTaxon( Request $request ): Response
    {
        $locale     = $request->getLocale();
        $form       = $this->createForm( TaxonForm::class );
        
        if ( $request->isMethod( 'POST' ) ) {
            $parentTaxon    = $this->getTaxonRepository()->find( $_POST['taxon_form']['parentTaxon'] );
            
            $form->submit( $request->request->get( $form->getName() ) );
            
            if ( $form->isSubmitted()  ) { // && $form->isValid()
                $em         = $this->getDoctrine()->getManager();
                $oTaxon     = $form->getData();
                $oTaxon->setParent( $parentTaxon );
                
                // @NOTE Force generation of slug
                $oTaxon->getTranslation( $locale )->setSlug( Slug::generate( $oTaxon->getTranslation()->getName() ) );
                $oTaxon->getTranslation( $locale )->setTranslatable( $oTaxon );
                
                $em->persist( $oTaxon );
                $em->flush();
                
                $taxonomyId = $request->attributes->get( 'taxonomyId' );
                return $this->redirect( $this->generateUrl( 'vs_application_taxonomy_update', ['id' => $taxonomyId] ) );
            }
        }
        
        return new Response( 'The form is not submited properly !!!', 500 );
    }
    
    public function gtreeTableSource( $taxonomyId, Request $request ): Response
    {
        $parentId       = (int)$request->query->get( 'taxonId' );
        
        return new JsonResponse( $this->gtreeTableData( $taxonomyId, $parentId ) );
    }
    
    public function easyuiComboTreeSource( $taxonomyId, Request $request ): Response
    {
        return new JsonResponse( $this->easyuiComboTreeData( $taxonomyId ) );
    }
}
