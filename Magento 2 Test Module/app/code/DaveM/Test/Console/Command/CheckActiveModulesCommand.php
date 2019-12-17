<?php
namespace DaveM\Test\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Module\ModuleListInterface;

/**
 * Class CheckActiveModulesCommand
 */
class CheckActiveModulesCommand extends Command
{
	private $activeModules;
	
    public function __construct(ModuleListInterface $activeModules)
    {
        $this->activeModules = $activeModules;
		
        parent::__construct();
    }
	
    protected function configure()
    {
        $this->setName('check-active');
        $this->setDescription('Displays a list of enabled modules.');
        
        parent::configure();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Active modules:');
        foreach($this->activeModules->getNames() as $name) {
            $output->writeln($name);
        }
    }
}