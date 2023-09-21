<?php namespace Vankosoft\ApplicationBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class TagsWhitelistTagsRepository extends EntityRepository
{
    public function getTags(): array
    {
        $query  = $this->createQueryBuilder( 'vt' )->select( 'vt.tag AS tag' )->getQuery();
        
        $resultMapping = new ResultSetMapping(); 
        $resultMapping->addScalarResult( 'tag', 'tag' );
        
        return $query->setResultSetMapping( $resultMapping )->getSingleColumnResult();
    }
    
    public function updateTags( array $tags ): void
    {
        //$entityClass    = $this->getClassName();
        $entityClass    = $this->getEntityName();
        $existingTags   = $this->getTags();
        
        $newTags        = \array_diff( $tags, $existingTags );
        
        $em             = $this->getEntityManager();
        foreach ( $newTags as $tag ) {
            $oTag   = new $entityClass();
            $oTag->setTag( $tag );
            $em->persist( $oTag );
        }
        $em->flush();
    }
}