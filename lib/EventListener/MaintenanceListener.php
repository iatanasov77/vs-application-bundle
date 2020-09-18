<?php namespace VS\ApplicationBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

use VS\ApplicationBundle\Twig\Alerts;

class MaintenanceListener
{
    protected $container;
    protected $user;
    
    public function __construct( ContainerInterface $container )
    {
        $this->container    = $container;
        
        $token              = $this->container->get( 'security.token_storage' )->getToken();
        if ( $token ) {
            $this->user         = $token->getUser();
        }
    }
    
    public function onKernelRequest(GetResponseEvent $event)
    {
        $schema             = $this->container->get( 'doctrine' )->getConnection()->getSchemaManager();
        if ( $schema->tablesExist( ['VSAPP_Settings'] ) == false ) {
            Alerts::$WARNINGS[]   = 'The table VSAPP_Settings does not exists !';
            return;
        }
        
        $repo               = $this->container->get( 'vs_application.repository.settings' );
        $settings           = $repo->findBy( [], ['id'=>'DESC'], 1, 0 );
        $maintenanceMode    = false;
        $maintenancePage    = false;
        $debug              = false;
        
        if( isset( $settings[0] ) ) {
            $maintenanceMode    = $settings[0]->getMaintenanceMode();
            $maintenancePage    = $settings[0]->getMaintenancePage();
            
            // This will detect if we are in dev environment
            $debug              = in_array( $this->container->get('kernel')->getEnvironment(), ['dev'] );
        }
        
        // If maintenance is active and in prod environment and user is not admin
        if ( $maintenanceMode ) {
            if (
                ( ! $this->user || ! $this->user->hasRole( 'ROLE_ADMIN' ) )
                && ! $debug
            ) {
                $event->setResponse( new Response( $maintenancePage->getText(), 503 ) );
                
                $event->stopPropagation();
            } else {
                Alerts::$WARNINGS[]   = 'The System is in Maintenance Mode !';
            }   
        }
    }
}
