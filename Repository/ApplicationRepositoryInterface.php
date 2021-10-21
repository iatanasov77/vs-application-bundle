<?php namespace VS\ApplicationBundle\Repository;

use VS\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ApplicationRepositoryInterface extends RepositoryInterface
{
    public function findOneByHostname( string $hostname ) : ?ApplicationInterface;
    
    public function findOneByCode( string $code ) : ?ApplicationInterface;
    
    /**
     * @return iterable|ApplicationInterface[]
     */
    public function findByName( string $name ) : iterable;
}
