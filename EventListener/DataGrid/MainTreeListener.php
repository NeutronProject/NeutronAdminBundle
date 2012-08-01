<?php
namespace Neutron\AdminBundle\EventListener\DataGrid;

use Neutron\Bundle\DataGridBundle\Event\DataEvent;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class MainTreeListener
{
    
    protected $translator;
    
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }
    
    public function onDataResponse(DataEvent $event)
    {
        $name = $event->getName();
        $data = $event->getData();
     
        if ($name == 'tree_management'){
            $event->stopPropagation();
            $data = $this->translateData($data);
            $event->setData($data);
        }
    }
    
    private function translateData(array $data)
    {   
    
        $translatedData = array();
        foreach ($data['rows'] as $idx => $row){
            $translatedData['rows'][$idx]['cell'][2] = 
                $this->translator->trans($data['rows'][$idx]['cell'][2], array(), 'NeutronAdminBundle');
        }
  
        return array_replace_recursive($data, $translatedData);
    }
}