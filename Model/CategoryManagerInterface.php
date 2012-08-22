<?php
namespace Neutron\AdminBundle\Model;

interface CategoryManagerInterface
{
    public function findCategoryBySlug($slug, $useCache, $locale);

 
}