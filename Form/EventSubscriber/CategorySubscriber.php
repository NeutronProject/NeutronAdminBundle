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

use Neutron\PluginBundle\Provider\PluginProviderInterface;

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
    protected $pluginProvider;
    
    public function __construct(PluginProviderInterface $pluginProvider)
    {
        $this->pluginProvider = $pluginProvider;
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
            
            
            $parentType = $data->getParent()->getType();
            
            if ($parentType == 'root'){
                return;
            }
            
            $plugin = $this->pluginProvider->get($parentType);
            $opt = $plugin->getTreeOptions();
            
            if ($opt['children_strategy'] == 'self'){
                $form->remove('type');
                $data->setType($this->getParentNode()->getType());
            } elseif ($opt['children_strategy'] == 'none'){
                throw new \RuntimeException('Node can NOT be created!');
            }
            
        } else {
            $form->remove('type');
        }
        

    }
    
    /**
     * Subscription for Form Events
     */
    static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
        );
    }

}