<?php namespace VS\ApplicationBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

use VS\ApplicationBundle\Model\Interfaces\SettingsInterface;

class SettingsRepository extends EntityRepository implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    public function getSettings( $site = null ): SettingsInterface
    {
        $qb = $this->createQueryBuilder( 's' )
                    ->select( 's' )
                    ->orderBy( 's.id', 'DESC' )
                    ->setMaxResults( 1 )
                    ->setFirstResult( 0 );
        
        if ( $site == null ) {
            $qb->where( 's.site IS NULL' );
        } else {
            $qb->where( 's.site = :site' )->setParameter( 'site', $site );
        }
        
        return $qb->getQuery()->getResult();
    }
}
