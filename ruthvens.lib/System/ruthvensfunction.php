<?php
namespace Ruthvens;
defined('PATH') or die('path failed.');

require PATH . '/ruthvens.lib/System/Random.function.php';

use \SendGrid;

class RuthvensFunction extends Random
{
	public $climate;

	public $list;
	public $api_s;

	public function __construct($config)
	{
		parent::__construct($config);
		$this->climate = new \League\CLImate\CLImate;
	}

	public function CreateSMTP($brp)
	{
		$smtp = [];
		$this->climate->error()->flank('Creating API Config', '!');
		$this->climate->border('*', 50);
		for ($i = 0; $i < $brp; $i++) {
			$this->climate->white()->out('Input SendgridApi No-' . ($i + 1));
			$api = $this->climate->tab()->LightMagenta()->input('Apikey?');
			$api = $api->prompt();
			$dmm = $this->climate->tab()->LightCyan()->input('Domain?');
			$dmm = $dmm->prompt();
			$this->climate->border('*', 50);
			$smtp[$i] = ["Apikey" => $this->input($api), "Domain" => $this->input($dmm)];
		}
		$this->climate->error()->flank('Task done', '!');
		return $this->write_ini_file($smtp, PATH . '/ruthvens.lib/config/api/api.ini', true);
	}

	public function CheckConfig()
	{
		$this->banner();
		$this->climate->clear();
		$this->banner();
		$this->climate->error()->flank('Config', '!');
		$this->climate->White()->out('API FILE : ' . $this->config['api']);
		$this->climate->White()->out(
			'LIST FILE : ' . $this->config['list']['file'] . ' | REMOVE AFTER SEND : ' . $this->bolstr($this->config['list']['remove_send']). ' | REMOVE DUPPLICATE : ' . $this->bolstr($this->config['list']['remove_dupp'])
		);

		$this->climate->White()->out(' ');
		$this->climate->White()->out('[SETTING]');
		$this->climate->White()->out('SEND AS : ' . $this->config['setup']['send_as']);
		$this->climate->White()->out(
			'SEND : ' . $this->config['setup']['send'] . ' PER CONNECTION | SLEEP EVERY : ' . $this->config['setup']['sleep'] . ' SECOND'
		);
		$this->climate->White()->out(
			'CLICK TRACKING : ' . $this->bolstr($this->config['setup']['click_track']) . ' | OPEN TRACKING : ' . $this->bolstr($this->config['setup']['open_track'])
		);
		$this->climate->White()->out('SEND TYPE : ' . $this->config['setup']['send_type']);
		$this->climate->White()->out(
			'EMAIL TEST EVERY SEND : ' . ($this->config['setup']['emailtest'] == '' ? 'NONE' : $this->config['setup']['emailtest'])
		);
		$this->climate->White()->out('RANDOM PARM : ' . $this->bolstr($this->config['setup']['randomparam']));
		$this->climate->White()->out('LINK : ' . ($this->config['setup']['link'] == '' ? 'NONE' : $this->config['setup']['link']));
		$this->climate->White()->out('HEADER : ' . $this->bolstr($this->config['setup']['header']));

		if ($this->config['setup']['header']) {
			$this->climate->White()->out(' ');
			$this->climate->White()->out('[HEADER]');
			foreach ($this->config['header'] as $key => $val) {
				$this->climate->White()->out($key . ': ' . $val);
			}
		}

		$this->climate->White()->out(' ');
		$this->climate->White()->out('[CUSTOM TAG]');
		foreach ($this->config['tag'] as $key => $val) {
			$this->climate->White()->out('##custom_tag_' . $key . '## => ' . $val);
		}

		$this->climate->White()->out(' ');
		$this->climate->White()->out('[SENDING]');
		$this->climate->White()->out('TO : ' . ($this->config['setting']['to'] == '' ? 'NONE' : $this->config['setting']['to']));
		$this->climate->White()->out('FROM NAME : ' . $this->config['setting']['fname']);
		$this->climate->White()->out('FROM MAIL : ' . $this->config['setting']['fmail']);
		$this->climate->White()->out('SUBJECT : ' . $this->config['setting']['subject']);
		$this->climate->White()->out('LETTER : ' . $this->config['setting']['letter']);
		$this->climate->White()->out(
			'ATTACHMENT NAME : ' . ($this->config['setting']['attach_file'] == '' ? 'NONE' : $this->config['setting']['attach_file']) . " => " . ($this->config['setting']['attach_name'] == '' ? 'NONE' : $this->config['setting']['attach_name'])
		);
		$this->climate->White()->out(' ');
		$this->file_check();
		$this->climate->White()->out(' ');
		$stop = $this->climate->input('Press enter to continue...');
		$stop->prompt();
	}

	public function file_check()
	{
		$this->climate->LightGreen()->flank('FILE CHECK', '#');
		$padding = $this->climate->padding(50)->char(' ');
		$padding->label('API')->result((!file_exists($this->path('api/' . $this->config['api'])) ? '[MISSING]' : '[OK]'));
		$padding->label('LIST')->result((!file_exists($this->path('list/' . $this->config['list']['file'])) ? '[MISSING]' : '[OK]'));
		$padding->label('LETTER')->result((!file_exists($this->path('letter/' . $this->config['setting']['letter'])) ? '[MISSING]' : '[OK]'));
		if ($this->config['setting']['attach_file'] != '') {
			$padding->label('ATTACHMENT')->result((!file_exists($this->path('attachment/' . $this->config['setting']['attach_file'])) ? '[MISSING]' : '[OK]'));
		}
	}

	public function accountCheck()
	{
		$this->climate->clear();
		$this->banner();
		if (!file_exists($this->path('api/' . $this->config['api'])) || empty(file_get_contents($this->path('api/' . $this->config['api'])))) {
			$this->climate->error()->out($this->config['api'] . ' NOT INJECTED !');
		} else {
			$this->climate->LightGreen()->flank('API Account', '!');
			$parse = parse_ini_file($this->path('api/' . $this->config['api']), true);
			foreach ($parse as $key => $parseValue) {
				$sg = new \SendGrid($parseValue['Apikey']);
				try {
					$this->climate->White()->out(' ');
					$this->climate->LightGreen()->flank('APIKEY NO-' . ($key + 1), '#');
					$this->climate->White()->out('Apikey: ' . $parseValue['Apikey']);
					$this->climate->White()->out('Domain: ' . $parseValue['Domain']);

					$this->climate->White()->out(' ');
					$this->climate->LightGreen()->flank('ACCOUNT STATUS', '#');
					$response = $sg->client->user()->account()->get();
					$this->climate->White()->out('Response Code: ' . $this->errors($response->statusCode())['code']);
					$this->climate->White()->out('Message: ' . $this->errors($response->statusCode())['msg']);
					if ($response->statusCode() == 200) {
						$get = json_decode($response->body());
						$this->climate->White()->out('Type: ' . $get->type);
						$this->climate->White()->out('Reputation: ' . $get->reputation . '%');
					}

					$this->climate->White()->out(' ');
					$this->climate->LightGreen()->flank('CREDIT BALANCE', '#');
					$response = $sg->client->user()->credits()->get();
					$this->climate->White()->out('Response Code: ' . $this->errors($response->statusCode())['code']);
					$this->climate->White()->out('Message: ' . $this->errors($response->statusCode())['msg']);
					if ($response->statusCode() == 200) {
						$get = json_decode($response->body());
						$this->climate->White()->out('Total: ' . $get->total . ' emails');
						$this->climate->White()->out('Remain: ' . $get->remain . ' emails');
						$this->climate->White()->out('Used: ' . $get->used . ' emails');
						$this->climate->White()->out('Reset: ' . $get->next_reset);
					}
				} catch (Exception $e) {
					echo 'Caught exception: ', $e->getMessage(), "\n";
					exit;
				}
			}
		}
		$this->climate->White()->out(' ');
		$stop = $this->climate->input('Press enter to continue...');
		$stop->prompt();
	}

	public function sendOut()
	{
		$this->climate->clear();
		$this->banner();
		$this->filter();

		$i = 0;
		$no = 1;
		$sendm = 0;
		$list_temp = $this->list;
		$splits = array_chunk($this->list, $this->config['setup']['send']);

		foreach ($splits as $split) {
			$api = $this->api_s[$i % count($this->api_s)];
			$total = count($split);
			$total_all = count($splits);
			$lastemail = end($split);

			$setting['api'] = $api['Apikey'];
			foreach ($this->config['setting'] as $key => $value) {
				$setting[$key] = $this->replace($value);
			}
			$setting['fmail'] .= '@' . $api['Domain'];
			$setting['letter'] = base64_encode($this->replace(file_get_contents(PATH . '/ruthvens.lib/config/letter/' . $setting['letter'])));

			if ($setting['attach_file'] != '' or $setting['attach_file'] != NULL) {
				$setting['attach_file'] = [
					'content' => base64_encode(file_get_contents(PATH . '/ruthvens.lib/config/attachment/' . $setting['attach_file'])),
					'ext' => $this->filenameToType($setting['attach_file']),
					'filename' => $setting['attach_name']
				];
			}
			unset($setting['attach_name']);

			$this->climate->LightYellow()->inline('[SENDGRID] [' . date('H:i:s') . '] [' . $no . '/' . $total_all . '] Sending ' . $total . ' email(s) with Domain ');
			$this->climate->LightGreen($api['Domain']);
			$this->climate->LightYellow()->inline('[SENDGRID] [' . date('H:i:s') . '] [' . $no . '/' . $total_all . '] Last email : ');
			$this->climate->LightRed($lastemail);
			$send = $this->formatSend($setting, $split);
			$this->climate->LightYellow()->inline('[SENDGRID] [' . date('H:i:s') . '] [' . $no . '/' . $total_all . '] Response Code : ');
			$this->climate->LightMagenta($send['code']);
			$this->climate->LightYellow()->inline('[SENDGRID] [' . date('H:i:s') . '] [' . $no . '/' . $total_all . '] Message : ');
			$this->climate->LightMagenta($send['msg']);
    		  // var_dump($setting);

			foreach ($split as $key => $value) {
				if ($send['code'] < '400') {
					unset($list_temp[$sendm]);
				}
				$sendm++;
			}

			$i++;
			$no++;
			$this->climate->LightRed()->flank('Sleep ' . $this->config['setup']['sleep'] . ' second(s)', '-', 15);
			sleep($this->config['setup']['sleep']);
		}

		if ($this->config['list']['remove_send'] == true) {
			$this->write(PATH . '/ruthvens.lib/config/list/' . $this->config['list']['file'], implode("\r\n", $list_temp), 'w');
		}

		$this->climate->White()->out(' ');
		$stop = $this->climate->input('Press enter to continue...');
		$stop->prompt();
	}

	public function filter()
	{
    // $this->climate->White()->out('Load API from '. $this->config['api']);
		$this->api_s = parse_ini_file(PATH . '/ruthvens.lib/config/api/' . $this->config['api'], true);
    // $this->climate->White()->out('Load list from '. $this->config['list']['file']);
		$list = preg_split('/\n|\r\n?/', trim(file_get_contents(PATH . '/ruthvens.lib/config/list/' . $this->config['list']['file'])));
		if($this->config['list']['remove_dupp'] == true) $list = array_unique($list);
		$list = str_replace(" ", '', $list);
		$list = preg_grep("/[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}/i", $list);
		$this->list = array_values(array_filter($list));
    // var_dump($this->list);
	}
}
