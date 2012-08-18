<?php
namespace Neutron\AdminBundle\Model;

interface SettingsManagerInterface
{
    const GROUP_DEFAULT = 'default';
    
    public function createOption($name, $value, $group = self::GROUP_DEFAULT);
    
    public function getOptions($useCache = false);
    
    public function getOption($name, $useCache = false);
    
    public function getOptionsByGroup($group);
    
    public function updateSettings(array $options);
 
}