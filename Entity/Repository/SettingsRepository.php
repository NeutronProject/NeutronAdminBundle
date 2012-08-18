<?php
/*
 * This file is part of NeutronAdminBundle
 *
 * (c) Zender <azazen09@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Neutron\AdminBundle\Entity\Repository;

use Gedmo\Translatable\Entity\Repository\TranslationRepository;

class SettingsRepository extends TranslationRepository
{
    
    public function getOptionsQueryBuilder()
    {
        return $this->createQueryBuilder('o');
    }
    
    public function getOptionsQuery($useCache)
    {
        $query = $this->getOptionsQueryBuilder()->getQuery();
        $query->useResultCache($useCache, 7200, $this->generateCacheId());
        return $query;
    }
    
    public function getOptions($useCache)
    {
        return $this->getOptionsQuery($useCache)->getArrayResult();
    }
    
    
    public function getOptionsByGroupQueryBuilder($group)
    {
        $qb = $this->createQueryBuilder('o');
        $qb->where('o.group = ?1');
        $qb->setParameter(1, $group);
        return $qb;
    }
    
    public function getOptionsByGroupQuery($group)
    {
        $query = $this->getOptionsByGroupQueryBuilder($group)->getQuery();
        return $query;
    }
    
    public function getOptionsByGroup($group)
    {
        $rowOptions =  $this->getOptionsByGroupQuery($group)->getArrayResult();
        $options = array();
        
        foreach ($rowOptions as $rowOption){
            $options[$rowOption['optionName']] = $rowOption['optionValue'];
        }
        
        return $options;
    }
    
    public function updateSettings(array $options)
    {
        $this->getCache()->delete($this->generateCacheId());
        
        $fetchedOptions = $this->findAll();
        foreach ($fetchedOptions as $option){
            if (array_key_exists($option->getOptionName(), $options)){
                $option->setOptionValue($options[$option->getOptionName()]);
            }
        }
        
        return $this;
    }
    
    public function getCache()
    {
        return $this->getEntityManager()->getConfiguration()->getResultCacheImpl();
    }
    
    public function generateCacheId()
    {
        return md5($this->getClassName());
    }
}