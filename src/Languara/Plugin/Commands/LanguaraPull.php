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
