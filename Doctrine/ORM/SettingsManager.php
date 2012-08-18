<?php
namespace Neutron\AdminBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManager;

use Neutron\AdminBundle\Model\SettingsManagerInterface;

class SettingsManager implements SettingsManagerInterface
{
    const SETTINGS = 'Neutron\\AdminBundle\\Entity\\Settings';
    
    protected $em;
    
    protected $repository;
    
    protected $cache;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(self::SETTINGS);
        $this->cache = $em->getConfiguration()->getResultCacheImpl();
    }
    
    public function createOption($name, $value, $group = self::GROUP_DEFAULT)
    {
        $class = self::SETTINGS;
        $entity = new $class($name, $value, $group);
        $this->em->persist($entity);
        return $entity;
    }
    
    public function getOption($name, $useCache = false)
    {
        $options = $this->getOptions($useCache);
        if (!isset($options[$name])){
            throw new \InvalidArgumentException(sprintf('Option "%s" does not exist.'));
        }
        
        return $options[$name];
    }
    
    public function getOptions($useCache = false)
    {
        return $this->repository->getOptions($useCache);
    }
    

    public function getOptionsByGroup($group)
    {
        return $this->repository->getOptionsByGroup($group);
    }
    
    public function updateSettings(array $options)
    {
        $this->repository->updateSettings($options);
        $this->em->flush();
    }
    
    public function emptyOptions()
    {
        $options = $this->repository->findAll();
        foreach ($options as $option){
            $this->em->remove($option);
        }
        
        $this->em->flush();
    }
    
    public function getObjectManager()
    {
        return $this->em;
    }
}