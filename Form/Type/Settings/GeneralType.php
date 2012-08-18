<?php
namespace Neutron\AdminBundle\Form\Type\Settings;

use Symfony\Component\Validator\Constraints\MaxLength;

use Symfony\Component\Validator\Constraints\MinLength;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class GeneralType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author', 'text', array(
                'label' => 'form.author',
                'constraints' => array(
                    new NotBlank(array('message' => 'form.author.not_blank')),
                    new MinLength(array('limit' => 2, 'message' => 'form.author.min')),
                    new MaxLength(array('limit' => 64, 'message' => 'form.author.max'))
                ),
                'translation_domain' => 'NeutronAdminBundle'
            ))
            ->add('copyrights', 'text', array(
                'label' => 'form.copyright',
                'constraints' => array(
                    new NotBlank(array('message' => 'form.copyright.not_blank')),
                    new MinLength(array('limit' => 2, 'message' => 'form.copyright.min')),
                    new MaxLength(array('limit' => 64, 'message' => 'form.copyright.max'))
                ),
                'translation_domain' => 'NeutronAdminBundle'
            ))
            ->add('publisher', 'text', array(
                'label' => 'form.publisher',
                'constraints' => array(
                    new NotBlank(array('message' => 'form.publisher.not_blank')),
                    new MinLength(array('limit' => 2, 'message' => 'form.publisher.min')),
                    new MaxLength(array('limit' => 64, 'message' => 'form.publisher.max'))
                ),
                'translation_domain' => 'NeutronAdminBundle'
            ))
            ->add('robots', 'choice', array(
                'choices' => array(
                     'INDEX, FOLLOW' => 'meta.index_folow',  
                     'NOINDEX, FOLLOW' => 'meta.no_index_folow',  
                     'INDEX, NOFOLLOW' => 'meta.index_nofolow',  
                     'NOINDEX, NOFOLLOW' => 'meta.noindex_nofolow',  
                 ),
                'constraints' => array(
                    new NotBlank(array('message' => 'form.robots.not_blank')),
                ),
                'multiple' => false,
                'expanded' => false,
                'attr' => array('class' => 'uniform'),
                'label' => 'form.robots',
                'empty_value' => 'form.empty_value',
                'translation_domain' => 'NeutronAdminBundle'
            ))
        ;
    }
    
    
    public function getName()
    {
        return 'neutron_settings_general';
    }
}