<?php namespace Skovachev\Lacore;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ContinuousCommand extends Command {

    public function __construct()
    {
        parent::__construct();

        $options = array(
            array('continuous', null, InputOption::VALUE_OPTIONAL, 'Run the command as a continuous cycle', 60),
        );
        call_user_func_array(array($this, 'addOption'), $options);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $continuous = $this->option('continuous');
        if (!is_null($continuous))
        {
            while (true)
            {
                $this->fire();
                sleep($continuous);
            }
        }
        else
        {
            return $this->fire();
        }
    }
}