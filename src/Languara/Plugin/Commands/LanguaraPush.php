<?php namespace Languara\Plugin\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LanguaraPush extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'languara:push';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Pushes your content from the local lang directories to the Languara server.';

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
            $obj_languara->upload_local_translations();
        }
        catch (\Exception $ex)
        {
            return $this->error($ex->getMessage());
        }
        
		return $this->info('Content pushed to the server successfully!');
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
