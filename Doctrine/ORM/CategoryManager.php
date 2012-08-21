<?php
namespace Neutron\AdminBundle\Doctrine\ORM;

use Neutron\AdminBundle\Model\CategoryManagerInterface;

use Doctrine\ORM\EntityManager;

class CategoryManager implements CategoryManagerInterface
{
    
    protected $em;
    
    protected $repository;
    
    protected $className;
    
    public function __construct(EntityManager $em, $className)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($className);
        $this->className = $className;
    }
    
    public function findCategoryBySlug($slug, $useCache, $locale)
    {
        return $this->repository->findBySlug($slug, $useCache, $locale);
    }
    

}