<?php
namespace Neutron\AdminBundle\EventSubscriber\Doctrine;

use Doctrine\ORM\Event\OnFlushEventArgs;

use Neutron\AdminBundle\Entity\MainTree;

use Doctrine\Common\NotifyPropertyChanged;

use Neutron\ComponentBundle\Util\Filter\SlugFilter;

use Doctrine\ORM\Events;

use Doctrine\Common\EventSubscriber;

class MainTreeSubscriber implements EventSubscriber
{

    const FIELD = 'slug';
    
    protected $slugFilter;
    
    public function __construct(SlugFilter $slugFilter)
    {
        $this->slugFilter = $slugFilter;
    }
    
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        
        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            
            if (!$entity instanceof MainTree){
                return;
            }
            
            $meta = $em->getClassMetadata(get_class($entity));
            $this->updateSlug($entity, $uow, $meta, self::FIELD);
        }
        
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            
            if (!$entity instanceof MainTree){
                return;
            }
            
            $meta = $em->getClassMetadata(get_class($entity));
            
            $changeset = $uow->getEntityChangeSet($entity);
            
            if (isset($changeset[self::FIELD])){
                $this->updateSlug($entity, $uow, $meta, self::FIELD);     
            }
        }

    }
    
    protected function updateSlug($entity, $uow, $meta, $field)
    {

        $property = $meta->getReflectionProperty($field);
        $oldValue = $property->getValue($entity);
        $newValue = $this->slugFilter->filter($oldValue);
        $property->setValue($entity, $newValue);
        $uow->recomputeSingleEntityChangeSet($meta, $entity);
        
        if ($entity instanceof NotifyPropertyChanged) {
            $uow->propertyChanged($entity, $field, $oldValue, $newValue);
        }
    }
    
    public function getSubscribedEvents()
    {
        return array(Events::onFlush);
    }
}