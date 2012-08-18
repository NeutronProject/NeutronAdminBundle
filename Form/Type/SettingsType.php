<?php
namespace Neutron\AdminBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('general', 'neutron_settings_general');
        $builder->add('seo', 'neutron_seo');
        $builder->add('search_engines', 'neutron_settings_search_engines');
        $builder->add('statistic', 'neutron_settings_statistic');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'    => false,
            'cascade_validation' => true,
        ));
    }
    
    public function getName()
    {
        return 'neutron_settings';
    }
}