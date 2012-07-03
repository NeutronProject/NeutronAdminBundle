<?php
namespace Neutron\AdminBundle\Form\Type\Category;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class AddType extends AbstractType
{
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

        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Neutron\AdminBundle\Entity\MainTree',
            'csrf_protection' => false,
        ));
    }
    
    public function getName()
    {
        return 'neutron_admin_category_add';
    }
}