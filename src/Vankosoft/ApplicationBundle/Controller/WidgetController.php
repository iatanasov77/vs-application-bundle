<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Contracts\Cache\CacheInterface;
use Doctrine\Persistence\ManagerRegistry;
use Pd\WidgetBundle\Controller\WidgetController as PdWidgetController;
use Pd\WidgetBundle\Repository\WidgetUserRepository;

class WidgetController extends PdWidgetController
{
    /** @var CacheInterface */
    protected $cache;
    
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var WidgetUserRepository */
    protected $widgetUserRepo;
    
    public function __construct(
        CacheInterface $cache,
        ManagerRegistry $doctrine,
        WidgetUserRepository $widgetUserRepo
    ) {
        parent::__construct( $widgetUserRepo, $cache );
        
        $this->cache            = $cache;
        $this->doctrine         = $doctrine;
        $this->widgetUserRepo   = $widgetUserRepo;
    }
    
    protected function getDoctrine()
    {
        return $this->doctrine;
    }
}