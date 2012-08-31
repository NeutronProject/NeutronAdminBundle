<?php
namespace Neutron\AdminBundle\Entity\Repository;

use Doctrine\ORM\Query;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class MainTreeRepository extends NestedTreeRepository
{
    public function findBySlugQueryBuilder()
    {
        $qb = $this->createQueryBuilder('n');
        $qb
            ->where('n.slug = :slug')
            ->andWhere('n.enabled = :enabled')
        ;
    
        return $qb;
    }
    
    public function findBySlugQuery($slug, $useCache, $locale)
    {
        $query = $this->findBySlugQueryBuilder()->getQuery();
        $query->setParameters(array('slug' => $slug, 'enabled' => true));
        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        $query->setHint(
            \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $locale
        );
        $query->useResultCache($useCache, 7200, $this->generateCacheId($slug, $locale));
        return $query;
    }
    
    public function findBySlug($slug, $useCache, $locale)
    {
        return $this->findBySlugQuery($slug, $useCache, $locale)->getOneOrNullResult();
    }
    
    public function generateCacheId($slug, $locale)
    {
        return md5($this->getClassName()) . '_' . md5($slug . $locale);
    }
}