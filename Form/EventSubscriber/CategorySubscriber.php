<?php
/*
 * This file is part of NeutronAdminBundle
 *
 * (c) Zender <azazen09@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Neutron\AdminBundle\Form\EventSubscriber;

use Neutron\TreeBundle\Model\TreeNodeInterface;

use Symfony\Component\Form\FormEvent;

use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\FormFactoryInterface;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Neutron user event subscriber
 *
 * @author Nikolay Georgiev <azazen09@gmail.com>
 * @since 1.0
 */
class CategorySubscriber implements EventSubscriberInterface
{
    protected $parentNode;
    
    public function setParentNode(TreeNodeInterface $parentNode)
    {
        $this->parentNode = $parentNode;
        
        return $this;
    }
    
    public function getParentNode()
    {
        if (null === $this->parentNode){
            throw new \RuntimeException('Parent node is not set');
        }
        
        return $this->parentNode;
    }
    
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        
        if (empty($data)) {
            return;
        }
        
        if (!$data->getId()) {
            $form->remove('enabled');
            $form->remove('displayed');
            
            $parentType = $this->getParentNode()->getType();
            
            if ($parentType == 'root'){
                return;
            }
            
            $opt = $this->getTypeMetadata($parentType);
            
            if ($opt['children_strategy'] == 'self'){
                $form->remove('type');
                $data->setType($this->getParentNode()->getType());
            } elseif ($opt['children_strategy'] == 'none'){
                throw new \RuntimeException('Node can NOT be created!');
            }
            
        } else {
            $form->remove('type');
            
        }
        
        if ($data->getType() == 'neutron.plugin.external'){
            $form->remove('slug');
        }
        
        

    }
    
    public function postBind(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        
        if (empty($data)) {
            return;
        }
        
        if ($data->getType() == 'neutron.plugin.external'){
            $data->setSlug(null);
        } else {
            $data->setExternalUri(null);
        }
    }

    /**
     * Subscription for Form Events
     */
    static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_BIND => 'postBind',
        );
    }
    
    private function getTypeMetadata($type)
    {
        $metadata = $this->getTypesMetadata();
        
        return $metadata[$type];
    }
    
    private function getTypesMetadata()
    {
        return array(
            
           'neutron.plugin.page' => array(
                'name' => 'neutron.plugin.page',
                'children_strategy' => 'all',
                'start_drag' => true,
                'move_node' => true,
                'select_node' => true,
                'hover_node' => true,
                'disable_create_btn' => false,
                'disable_update_btn' => false,
                'disable_delete_btn' => false,
            )
    
        );
    }
}