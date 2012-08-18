<?php
namespace Neutron\AdminBundle\Command;

use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class SettingsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('neutron:admin:settings')
            ->setDescription('Creates settings options.')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
       
        $dialog = $this->getHelperSet()->get('dialog');
        $dialog->askConfirmation(
            $output,
            '<question>You are  about to delete all options (if any) and recreate them again. Do you want to continue?</question>'     
        );
        
        if (!$dialog){
            return;
        }
        
        $manager = $this->getContainer()->get('neutron_admin.settings_manager');
        
        $manager->emptyOptions();
        
        foreach ($this->availableOptions() as $group => $options){
            foreach ($options as $name => $value){
                $manager->createOption($name, $value, $group);
            }
        }
        
        $this->getContainer()->get('doctrine.orm.entity_manager')->flush();
        $output->writeln('<info>Settins options have beed created.</info>');
    }
    
    
    protected function availableOptions()
    {
        return array(
            'search_engines' => array(
                'google' => null,
                'yahoo' => null,      
                'alexa' => null,    
           ),     
            'statistic' => array( 
                'googleAnalytics' => null
           ),     
            'general' => array(
                'author'     => 'Nikolay Georgiev',    
                'copyrights' => 'NeutronLabs',
                'publisher' => 'NeutronLabs',
                'robots'    => 'index, follow, all',
            ),
        ); 
    }
}
