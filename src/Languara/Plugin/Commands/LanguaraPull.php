<?php namespace Languara\Plugin\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LanguaraPull extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'languara:pull';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Pull your content from the Languara server and adds it to the local lang directories.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{        
        $obj_languara = new \Languara\Plugin\Library\LanguaraWrapper();
        
        try
        {
            $obj_languara->download_and_process();
        }
        catch (\Exception $ex)
        {
            return $this->error($ex->getMessage());
        }
        
		return $this->info('Content pulled from the server successuflly!');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
//			array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
//			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
