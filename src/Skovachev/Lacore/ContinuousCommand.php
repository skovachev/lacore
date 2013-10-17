<?php namespace Skovachev\Lacore;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ContinuousCommand extends Command {

    const WAIT_TIME_OPTION_NAME = 'wait';
    const RUN_ONCE_OPTION_NAME = 'once';

    public function __construct()
    {
        parent::__construct();

        $options = array(
            array(self::WAIT_TIME_OPTION_NAME, 'w', InputOption::VALUE_OPTIONAL, 'Wait time between runs in seconds', 60),
            array(self::RUN_ONCE_OPTION_NAME, null, InputOption::VALUE_NONE, 'Run once flag')
        );

        foreach ($options as $option) {
            call_user_func_array(array($this, 'addOption'), $option);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->option('once'))
        {
            while (true)
            {
                $this->fire();
                sleep($this->option(self::WAIT_TIME_OPTION_NAME));
            }
        }
        else
        {
            return $this->fire();
        }
    }
}