<?php
namespace Neutron\AdminBundle\Form\Type\Settings;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class StatisticType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('googleAnalytics', 'textarea', array(
                'label' => 'form.google_analytics',
                'translation_domain' => 'NeutronAdminBundle'
            ))
        ;
    }
    
    public function getName()
    {
        return 'neutron_settings_statistic';
    }
}