<?php
namespace Neutron\AdminBundle\Form\Type\Category;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

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
            
            ->add('enabled', 'checkbox', array(
                'label' => 'form.enabled', 
                'value' => 1,
                'required' => false,
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
            'validation_groups' => 'Creation',
        ));
    }
    
    public function getName()
    {
        return 'neutron_admin_form_category_add';
    }
}