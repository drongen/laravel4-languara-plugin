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
        $arr_conf           = \Config::get('plugin::conf');
        $arr_endpoints      = \Config::get('plugin::endpoints');
        $language_location  = \Config::get('plugin::language_location');

        if (! $arr_conf || ! $arr_endpoints || ! $language_location)
        {
            return $this->error('Your configuration file is misconfigured, re-configure it and try again!');
        }
        
        $obj_languara = new \Languara\Plugin\Library\Lib_Languara();
        $obj_languara->conf                 = $arr_conf;
        $obj_languara->endpoints            = $arr_endpoints;
        $obj_languara->language_location    = $language_location;
        
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
