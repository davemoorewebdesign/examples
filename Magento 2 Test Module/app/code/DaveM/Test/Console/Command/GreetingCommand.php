<?php
namespace DaveM\Test\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class GreetingCommand
 */
class GreetingCommand extends Command
{
    protected function configure()
    {
        $this->setName('greeting');
        $this->setDescription('Displays a greeting.');
        $this->setDefinition([
            new InputArgument(
                'name',
                InputArgument::OPTIONAL,
                'Name'
            )
        ]);
        
        parent::configure();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        if ($name) {
           $name = ' '.$name;
        }
		
        $output->writeln("Hello".$name."!");
    }
}