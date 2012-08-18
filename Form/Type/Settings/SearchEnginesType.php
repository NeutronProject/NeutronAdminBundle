<?php
namespace Neutron\AdminBundle\Form\Type\Settings;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class SearchEnginesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('google', 'textarea', array(
                'label' => 'form.google',
                'translation_domain' => 'NeutronAdminBundle'
            ))
            ->add('yahoo', 'textarea', array(
                'label' => 'form.yahoo',
                'translation_domain' => 'NeutronAdminBundle'
            ))
            ->add('alexa', 'textarea', array(
                'label' => 'form.alexa',
                'translation_domain' => 'NeutronAdminBundle'
            ))
        ;
    }
    
    public function getName()
    {
        return 'neutron_settings_search_engines';
    }
}