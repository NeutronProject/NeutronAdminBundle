<?php
namespace Neutron\AdminBundle\Acl;

use Neutron\UserBundle\Model\BackendRoles;

use Symfony\Component\Security\Core\SecurityContextInterface;

use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;

use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;

class AclManager implements AclManagerInterface
{
    protected $aclProvider;
    
    protected $securityContext;
    
    public function __construct(MutableAclProviderInterface $aclProvider, 
            SecurityContextInterface $securityContext)
    {
        $this->aclProvider = $aclProvider;
        $this->securityContext = $securityContext;
    }
    
    public function setObjectPermissions(ObjectIdentityInterface $objectIdentity, array $roles)
    {
        $acl = $this->createAcl($objectIdentity);
        
        foreach ($roles as $role => $permissions){
            $securityIdentity = new RoleSecurityIdentity($role);
            $acl->insertObjectAce($securityIdentity, $this->getMask($permissions));
        }
        
        $this->aclProvider->updateAcl($acl);
    }
    
    public function deleteObjectPermissions(ObjectIdentityInterface $objectIndentity)
    {
        $this->aclProvider->deleteAcl($objectIndentity);
    }
    
    public function getPermissions(ObjectIdentityInterface $object)
    {
        $permissions = array();
        
        try {
            $acl = $this->aclProvider->findAcl($object);
        } catch (\Symfony\Component\Security\Acl\Exception\Exception $e) {
            return $permissions;
        }
    
        $aces = $acl->getObjectAces();
    
        foreach ($aces as $ace){
            $permissions[$ace->getSecurityIdentity()->getRole()] = array(
                'VIEW' => (($ace->getMask() & MaskBuilder::MASK_VIEW) == true) ? 'VIEW' : null,
                'CREATE' => (($ace->getMask() & MaskBuilder::MASK_CREATE) == true) ? 'CREATE' : null,
                'EDIT' => (($ace->getMask() & MaskBuilder::MASK_EDIT) == true) ? 'EDIT' : null,
                'DELETE' => (($ace->getMask() & MaskBuilder::MASK_DELETE) == true) ? 'DELETE' : null,
                'UNDELETE' => (($ace->getMask() & MaskBuilder::MASK_UNDELETE) == true) ? 'UNDELETE' : null,
            );
        }
    
        return $permissions;
    }
    
    public function isGranted($object, $administrativeMode = false)
    {      
        if ($administrativeMode){
            return true;
        }
        
        $user = $this->securityContext->getToken()->getUser();
        if ($user != 'anon.' && count(array_intersect($user->getRoles(), BackendRoles::getAdministrativeRoles())) > 0) {
            return true;
        }
        
        return $this->securityContext->isGranted('VIEW', $object);
    }
   
    
    protected function createAcl(ObjectIdentityInterface $objectIdentity)
    {   
        $this->aclProvider->deleteAcl($objectIdentity);
        $acl = $this->aclProvider->createAcl($objectIdentity);
        return $acl;
    }
    
    protected function getMask(array $permissions)
    {
        $supportedPermissions = array(
            'VIEW'     => MaskBuilder::MASK_VIEW,
            'CREATE'   => MaskBuilder::MASK_CREATE,
            'EDIT'     => MaskBuilder::MASK_EDIT,
            'DELETE'   => MaskBuilder::MASK_DELETE,
            'UNDELETE' => MaskBuilder::MASK_UNDELETE,
        );
    
        $builder = new MaskBuilder();

        foreach ($permissions as $permission){
            if (!array_key_exists($permission, $supportedPermissions)){
                throw new \InvalidArgumentException(sprintf('Mask "%s" is not supported!', $permission));
            }
    
            $builder->add($supportedPermissions[$permission]);
        }
    
        $mask = $builder->get();
    
        return $mask;
    }
}