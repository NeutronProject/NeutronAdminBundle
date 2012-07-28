<?php
namespace Neutron\AdminBundle\Acl;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;

interface AclManagerInterface
{
    public function setObjectPermissions(ObjectIdentityInterface $objectIdentity, array $roles);
    
    public function deleteObjectPermissions(ObjectIdentityInterface $objectIndentity);
    
    public function getPermissions(ObjectIdentityInterface $objectIndentity);
   
}