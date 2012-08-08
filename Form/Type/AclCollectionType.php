<?php
namespace Neutron\AdminBundle\Form\Type;

use Symfony\Component\Form\FormView;

use Neutron\Bundle\AsseticBundle\Controller\AsseticController;

use Neutron\UserBundle\Model\RoleManagerInterface;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormInterface;

class AclCollectionType extends AbstractType
{

    protected $roleManager;
    
    protected $assetic;
    
    public function __construct(RoleManagerInterface $roleManager, AsseticController $assetic)
    {   
        $this->roleManager =  $roleManager;
        $this->assetic = $assetic;
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->assetic->prependJavascript('bundles/neutronadmin/js/acl.js');
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->roleManager->getRoles() as $role){
            $builder
                ->add($role->getRole(), 'neutron_admin_form_acl', array(
                    'label' => $role->getName(),
                    'choices' => $options['masks'],
                    'translation_domain' => 'messages'
                ))
            ;
        }
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'masks' => array(
                'UNDELETE' => 'UnDelete',
                'DELETE'   => 'Delete',
                'EDIT'     => 'Edit',
                'CREATE'   => 'Create',
                'VIEW'     => 'View',
            ),
            'csrf_protection' => false,
            
        ));
    }
    
    public function getParent()
    {
        return 'form';
    }
    
    public function getName()
    {
        return 'neutron_admin_form_acl_collection';
    }
    

}