<?php
namespace Neutron\AdminBundle\Form\Type\Category;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormInterface;

class AddType extends AbstractType
{
    
    protected $subscriber;
    
    protected $class;
    
    public function __construct(EventSubscriberInterface $subscriber, $class)
    {
        $this->subscriber = $subscriber;
        $this->class = $class;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'form.title',
                'translation_domain' => 'NeutronAdminBundle'
            ))
            
            ->add('slug', 'text', array(
                'label' => 'form.slug',
                'translation_domain' => 'NeutronAdminBundle'
            ))
            
            ->add('externalUri', 'url', array(
                'label' => 'form.external_uri',
                'attr' => array(),
                'empty_data' => null,
                'translation_domain' => 'NeutronAdminBundle'
            ))
            
            ->add('linkTarget', 'choice', array(
                'choices' => array('_self' => 'from.target.self', '_blank' => 'form.target.blank'),
                'preferred_choices' => array('_self' => 'from.target.self'),
                'multiple' => false,
                'expanded' => false,
                'attr' => array('class' => 'uniform'),
                'label' => 'form.link_target',
                'translation_domain' => 'NeutronAdminBundle'
            ))
            
            ->add('type', 'choice', array(
		        'choices' => $this->getTypes(),
		        'multiple' => false,
		        'expanded' => false,
		        'attr' => array('class' => 'uniform'),
		        'label' => 'form.type',
                'empty_value' => 'form.empty_value',
                'translation_domain' => 'NeutronAdminBundle'
		    ))
            
            ->add('enabled', 'checkbox', array(
                'label' => 'form.enabled', 
                'value' => 1,
                'required' => true,
                'translation_domain' => 'NeutronAdminBundle'
            ))
            
            ->add('displayed', 'checkbox', array(
                'label' => 'form.displayed', 
                'value' => 1,
                'required' => false,
                'translation_domain' => 'NeutronAdminBundle'
            ))
            
        ;
        
        $builder->addEventSubscriber($this->subscriber);
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'csrf_protection' => false,
            'validation_groups' => function(FormInterface $form){
                $data = $form->getData();
                
                $validationGroups = array();
                
                if ($data->getId()){
                    $validationGroups = array_merge($validationGroups, array('update'));
                } else {
                    $validationGroups = array_merge($validationGroups, array('create'));
                }
                
                if ($data->getType() == 'neutron.plugin.external'){
                    $validationGroups = array_merge($validationGroups, array('category.external'));
                } else {
                    $validationGroups = array_merge($validationGroups, array('category.slug'));
                }
                
                return $validationGroups;
            },
        ));
    }
    
    public function getName()
    {
        return 'neutron_admin_form_category_add';
    }
    
    private function getTypes()
    {
        return array(
            'neutron.plugin.external' => 'external',        
            'neutron.plugin.page' => 'page',        
            'neutron.plugin.news' => 'news',        
        );
    }
}