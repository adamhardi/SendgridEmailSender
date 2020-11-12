<?php
defined('PATH') or die('path failed.');

require PATH . '/ruthvens.lib/plugin/climate.library/autoload.php';
require PATH . '/ruthvens.lib/plugin/sendgrid.library/autoload.php';
require PATH . '/ruthvens.lib/System/ruthvensfunction.php';
require PATH . '/config.php';

use \League\CLImate\CLImate as ruthvens_print;
use \Sendgrid as SG;
use \Ruthvens\RuthvensFunction as func;

/**
 *
 */
class Prepare
{
	public $climate;
	public $ruthfunc;

	public function __construct($config)
	{
		$this->climate = new ruthvens_print;
		$this->ruthfunc = new func($config);

		$this->start();
	}

	public function start()
	{
		$this->ruthfunc->banner();
		$this->climate->White()->out("Yo! Before we start, let me check your SMTP first.. Please set it on config.php :)");

		if (file_exists($this->ruthfunc->path('api/' . $this->ruthfunc->config['api'])) AND !empty(file_get_contents($this->ruthfunc->path('api/' . $this->ruthfunc->config['api'])))) {
			$this->climate->error()->inline($this->ruthfunc->config['api']);
			$this->climate->LightGreen()->out(' Injected!');
			$this->climate->White()->out("All Ok!");
			sleep(2);
			$this->climate->clear();
		} else {
			$input = $this->climate->LightRed()->confirm('Create SMTP Config?');
			if ($input->confirmed()) {
				$banyak = $this->climate->LightGreen()->input('How many?');
				$banyak = $banyak->prompt();
				$this->ruthfunc->CreateSMTP($banyak);
				$this->start();
			} else {
				$this->climate->error()->out('You must create Api Sendgrid! Exit...');
				exit();
			}
		}

		$this->Menu();
	}

	public function Menu()
	{
		$this->climate->clear();
		$this->ruthfunc->banner();
		$this->climate->shout()->flank('Menu', '!');
		$this->climate->LightCyan()->out("1. Check API Balance");
		$this->climate->LightCyan()->out("2. Check Config");
		$this->climate->LightCyan()->out("3. Continue send >");
		$this->climate->LightCyan()->out("0. Exit");
		$input = $this->climate->LightGreen()->input('Choose: ');
		$input->accept([1, 2, 3, 0]);

		$response = $input->prompt();
		if ($response == 1) {
			$this->ruthfunc->accountCheck();
		} else if ($response == 2) {
			$this->ruthfunc->CheckConfig();
		} else if ($response == 0) {
			$this->climate->error()->out('Exit...');
			exit;
		} else {
			$this->ruthfunc->sendOut();
		}

		$this->menu();
	}
}

