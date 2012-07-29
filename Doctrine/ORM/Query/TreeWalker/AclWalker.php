<?php
namespace Neutron\AdminBundle\Doctrine\ORM\Query\TreeWalker;

use Doctrine\ORM\Query\Exec\SingleSelectExecutor;

use Doctrine\ORM\Query\AST\SelectStatement;

use Doctrine\ORM\Query\SqlWalker;

class AclWalker extends SqlWalker
{
    const HINT_ACL = '__neutron_admin.acl.orm.hint';
    
    protected $conn;
    
    protected $platform;
    
    protected $attributes;
    
    
    public function __construct($query, $parserResult, array $queryComponents)
    {
        parent::__construct($query, $parserResult, $queryComponents); 
        $this->conn = $this->getConnection();
        $this->platform = $this->getConnection()->getDatabasePlatform();
        $this->extractAttributes($queryComponents);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getExecutor($AST)
    {
        if (!$AST instanceof SelectStatement) {
            throw new \UnexpectedValueException('Translation walker should be used only on select statement');
        }

        return new SingleSelectExecutor($AST, $this);
    }
    
    /**
     * {@inheritDoc}
     */
    public function walkWhereClause($whereClause)
    {
        $result = parent::walkWhereClause($whereClause);
        return $this->appendAcl($result);
    }
    
    /**
     * 
     *
     * @param array $queryComponents
     * @return void
     */
    private function extractAttributes(array $queryComponents)
    {
      
        foreach ($queryComponents as $alias => $comp) {
            if ($comp['parent'] === null && $comp['nestingLevel'] == 0 && isset($comp['metadata'])) {
                $this->attributes = array();
                $meta = $comp['metadata'];
                $this->attributes['tableAlias'] = $this->getSQLTableAlias($meta->getTableName(), $alias);
                $this->attributes['identifier'] = 
                    $meta->getQuotedColumnName($meta->getSingleIdentifierFieldName(), $this->platform);
                $this->attributes['class'] = $meta->getName();
                break;
            }  
        }
    }
    
    private function appendAcl($whereClause)
    {
        if (!$this->attributes){
            return $whereClause;
        }
        
        $subquery = $this->getEntitiesMatchingRoleMaskSql($this->attributes['class'], array('ROLE_BLOCK_USER'), 12);
        
        if ($whereClause == ''){
            $whereClause = " WHERE {$this->attributes['tableAlias']}.{$this->attributes['identifier']} IN ({$subquery})";
        } else {
            $whereClause .= " AND {$this->attributes['tableAlias']}.{$this->attributes['identifier']} IN ({$subquery})";
        }
        
        return $whereClause;
    }
    
    private function getEntitiesMatchingRoleMaskSql($class, array $roles, $mask)
    {
        $mask = (int) $mask;
        
        $qb = $this->conn->createQueryBuilder();
        
        $orX = $qb->expr()->orX();
        
        foreach ($roles as $role){
            $orX->add($qb->expr()->eq('s.identifier', $this->conn->quote($role)));
        }
        
        $qb
            ->select('oid.object_identifier')
            ->from('acl_entries', 'e')
            ->innerJoin('e', 'acl_object_identities', 'oid', 'oid.id = e.object_identity_id')
            ->innerJoin('e', 'acl_security_identities', 's', 's.id = e.security_identity_id')
            ->innerJoin('e', 'acl_classes', 'class', 'class.id = e.class_id')
            ->andWhere("e.mask & {$mask}")
            ->andWhere($orX)
            ->andWhere($qb->expr()->eq('class.class_type', $this->conn->quote($class)))
            ->groupBy('oid.object_identifier')
        ;
        
        return $qb->getSql();
        
    }
}