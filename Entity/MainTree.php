<?php
namespace Neutron\AdminBundle\Entity;

use Neutron\TreeBundle\Model\TreeNodeInterface;

use Gedmo\Mapping\Annotation as Gedmo;

use Doctrine\ORM\Mapping as ORM;

/**
 * @Gedmo\Tree(type="nested")
 * @Gedmo\TranslationEntity(class="Neutron\AdminBundle\Entity\Translation\MainTreeTranslation")
 * @ORM\Table(name="main_tree")
 * @ORM\Entity(repositoryClass="Neutron\AdminBundle\Entity\Repository\MainTreeRepository")
 * 
 */
class MainTree implements TreeNodeInterface 
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
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=64, nullable=false)
     */
    protected $title;
    
    /**
     * @var string
     * 
     * @Gedmo\Translatable
     * @ORM\Column(type="string", name="slug", length=255, nullable=false, unique=true)
     */
    protected $slug;
    
    /**
     * @var string 
     *
     * @ORM\Column(type="string", name="type", length=255, nullable=false, unique=false)
     */
    protected $type = 'default';
    
    /**
     * @var string 
     *
     * @ORM\Column(type="text", name="link_target", nullable=false)
     */
    protected $linkTarget = '_self';
    
    /**
     * @var boolean 
     *
     * @ORM\Column(type="boolean", name="enabled")
     */
    protected $enabled = false;
    
    /**
     * @var boolean 
     *
     * @ORM\Column(type="boolean", name="displayed")
     */
    protected $displayed = false;
    
    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    protected $lft;
    
    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    protected $lvl;
    
    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    protected $rgt;
    
    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    protected $root;
    
    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="MainTree", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="MainTree", mappedBy="parent", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;
    
    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setSlug($slug)
    {
        $this->slug = (string) $slug;
    }
    
    public function getSlug()
    {
        return $this->slug;
    }
    
    public function setType($type)
    {
        $this->type = (string) $type;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function setLinkTarget($target)
    {
        $this->linkTarget = (string) $target;
    }
    
    public function getLinkTarget()
    {
        return $this->linkTarget;
    }
    
    public function setEnabled($bool)
    {
        $this->enabled = (bool) $bool;
    }
    
    public function isEnabled()
    {
        return $this->enabled;
    }
    
    public function setDisplayed($bool)
    {
        $this->displayed = (bool) $bool;
    }
    
    public function isDisplayed()
    {
        return $this->displayed;
    }
    
    public function setParent(TreeNodeInterface $parent = null)
    {
        $this->parent = $parent;
    }
    
    public function getParent()
    {
        return $this->parent;
    }
    
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
}