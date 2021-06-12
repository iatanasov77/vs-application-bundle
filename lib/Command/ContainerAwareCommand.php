<?php namespace VS\ApplicationBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ContainerAwareCommand extends Command implements ContainerAwareInterface
{
    private $container;
    
    public function __construct( ContainerInterface $container )
    {
        parent::__construct();
        $this->container    = $container;
    }
    
    /**
     * @return ContainerInterface
     *
     * @throws \LogicException
     */
    protected function getContainer()
    {
        if ( null === $this->container ) {
            $application    = $this->getApplication();
            if ( null === $application ) {
                throw new \LogicException( 'The container cannot be retrieved as the application instance is not yet set.' );
            }
            
            $this->container    = $application->getKernel()->getContainer();
        }
        
        return $this->container;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setContainer( ContainerInterface $container = null )
    {
        $this->container    = $container;
    }
}
    