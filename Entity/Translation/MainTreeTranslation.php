<?php
namespace Neutron\AdminBundle\Entity\Translation;


use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="main_tree_translations", indexes={
 *      @ORM\Index(name="main_tree_translation_idx", columns={"locale", "object_class", "field", "foreign_key"})
 * })
 * @ORM\Entity(repositoryClass="Gedmo\Translatable\Entity\Repository\TranslationRepository")
 */
class MainTreeTranslation extends AbstractTranslation
{}