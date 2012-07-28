<?php
namespace Neutron\AdminBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\AbstractType;


class AclType extends AbstractType
{
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'multiple' => true,         
            'expanded' => true, 
        ));
    }
    
    public function getParent()
    {
        return 'choice';
    }
    
    public function getName()
    {
        return 'neutron_admin_form_acl';
    }

}