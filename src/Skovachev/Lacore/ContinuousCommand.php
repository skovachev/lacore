<?php namespace Skovachev\Lacore;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ContinuousCommand extends Command {

    const CONTINUOUS_OPTION_NAME = 'continuous';

    public function __construct()
    {
        parent::__construct();

        $continuousOption = array(self::CONTINUOUS_OPTION_NAME, null, InputOption::VALUE_OPTIONAL, 'Run the command as a continuous cycle', 60);
        call_user_func_array(array($this, 'addOption'), $continuousOption);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->input->hasOption(self::CONTINUOUS_OPTION_NAME))
        {
            $continuous = $this->option(self::CONTINUOUS_OPTION_NAME);
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