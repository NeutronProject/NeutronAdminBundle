<?php
/*
 * This file is part of NeutronAdminBundle
 *
 * (c) Zender <azazen09@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Neutron\AdminBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;

use Doctrine\ORM\Mapping as ORM;

/**
 * @Gedmo\TranslationEntity(class="Neutron\AdminBundle\Entity\Translation\SettingsTranslation")
 * @ORM\Table(name="neutron_settings")
 * @ORM\Entity(repositoryClass="Neutron\AdminBundle\Entity\Repository\SettingsRepository")
 * 
 */
class Settings 
{
    /**
     * @var integer 
     *
     * @ORM\Id @ORM\Column(name="id", type="integer")
     * 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string 
     *
     * @ORM\Column(type="string", name="option_name", length=64, nullable=false, unique=true)
     */
    protected $optionName;
    
    /**
     * @var string 
     *
     * @Gedmo\Translatable
     * @ORM\Column(type="text", name="option_value", nullable=true)
     */
    protected $optionValue;
    
    /**
     * @var string 
     *
     * @ORM\Column(type="string", name="option_group", length=64, nullable=false, unique=false)
     */
    protected $group = 'default';
    
    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;
    
    public function __construct($name, $value, $group)
    {
        $this->setOptionName($name);
        $this->setOptionValue($value);
        $this->setGroup($group);
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setOptionName($name)
    {
        $this->optionName = (string) $name;
        return $this;
    }
    
    public function getOptionName()
    {
        return $this->optionName;
    }
    
    public function setOptionValue($value)
    {
        $this->optionValue = (string) $value;
        return $this;
    }
    
    public function getOptionValue()
    {
        return $this->optionValue;
    }
    
    public function setGroup($group)
    {
        $this->group = (string) $group;
        return $this;
    }
    
    public function getGroup()
    {
        return $this->group;
    }
    
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
    
}