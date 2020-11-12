<?php
namespace Ruthvens;

defined('PATH') or die('path failed.');

use \SendGrid;
use \SendGrid\Mail\To;
use \SendGrid\Mail\Cc;
use \SendGrid\Mail\Bcc;
use \SendGrid\Mail\From;
use \SendGrid\Mail\Content;
use \SendGrid\Mail\Mail;
use \SendGrid\Mail\Personalization;
use \SendGrid\Mail\Subject;
use \SendGrid\Mail\Header;
use \SendGrid\Mail\CustomArg;
use \SendGrid\Mail\SendAt;
use \SendGrid\Mail\Attachment;
use \SendGrid\Mail\Asm;
use \SendGrid\Mail\MailSettings;
use \SendGrid\Mail\BccSettings;
use \SendGrid\Mail\SandBoxMode;
use \SendGrid\Mail\BypassListManagement;
use \SendGrid\Mail\Footer;
use \SendGrid\Mail\SpamCheck;
use \SendGrid\Mail\TrackingSettings;
use \SendGrid\Mail\ClickTracking;
use \SendGrid\Mail\OpenTracking;
use \SendGrid\Mail\SubscriptionTracking;
use \SendGrid\Mail\Ganalytics;
use \SendGrid\Mail\ReplyTo;

class Random
{
	public $config;

	public function __construct($config)
	{
		$this->config = $config;
	}

	public function path($file)
	{
		return PATH . '/ruthvens.lib/config/' . $file;
	}

	public function input($word)
	{
		return ($word == '' || $word == NULL ? 'empty_value' : $word);
	}

	public function bolstr($a)
	{
		return ($a ? 'TRUE' : 'FALSE');
	}

	public function write($file, $text, $type)
	{
		$fp = fopen($file, $type);
		return fwrite($fp, $text);
		fclose($fp);
	}

	public function errors($code)
	{
		$array = [
			200 => "OK | Server OK.",
			202 => "ACCEPTED | Your message will be delivered.",
			400 => "BAD REQUEST",
			401 => "UNAUTHORIZED | You do not have authorization to make the request.",
			403 => "FORBIDDEN",
			404 => "NOT FOUND | The resource you tried to locate could not be found or does not exist.",
			405 => "METHOD NOT ALLOWED",
			413 => "PAYLOAD TOO LARGE | The JSON payload you have included in your request is too large.",
			415 => "UNSUPPORTED MEDIA TYPE",
			429 => "TOO MANY REQUESTS | The number of requests you have made exceeds SendGrid’s rate limitations",
			500 => "SERVER UNAVAILABLE | An error occurred on a SendGrid server.",
			503 => "SERVICE NOT AVAILABLE | The SendGrid v3 Web API is not available.",
		];

		return ['code' => $code, "msg" => $array[$code]];
	}

	public function filenameToType($filename)
	{
		$qpos = strpos($filename, '?');
		if (false !== $qpos) {
			$filename = substr($filename, 0, $qpos);
		}
		$ext = $this->mb_pathinfo($filename, PATHINFO_EXTENSION);

		return $this->mimes($ext);
	}

	public function mb_pathinfo($path, $options = null)
	{
		$ret = ['dirname' => '', 'basename' => '', 'extension' => '', 'filename' => ''];
		$pathinfo = [];
		if (preg_match('#^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^.\\\\/]+?)|))[\\\\/.]*$#m', $path, $pathinfo)) {
			if (array_key_exists(1, $pathinfo)) {
				$ret['dirname'] = $pathinfo[1];
			}
			if (array_key_exists(2, $pathinfo)) {
				$ret['basename'] = $pathinfo[2];
			}
			if (array_key_exists(5, $pathinfo)) {
				$ret['extension'] = $pathinfo[5];
			}
			if (array_key_exists(3, $pathinfo)) {
				$ret['filename'] = $pathinfo[3];
			}
		}
		switch ($options) {
			case PATHINFO_DIRNAME:
			case 'dirname':
				return $ret['dirname'];

			case PATHINFO_BASENAME:
			case 'basename':
				return $ret['basename'];

			case PATHINFO_EXTENSION:
			case 'extension':
				return $ret['extension'];

			case PATHINFO_FILENAME:
			case 'filename':
				return $ret['filename'];

			default:
				return $ret;
		}
	}

	public function mimes($ext = '')
	{
		$mimes = [
			'xl' => 'application/excel',
			'js' => 'application/javascript',
			'hqx' => 'application/mac-binhex40',
			'cpt' => 'application/mac-compactpro',
			'bin' => 'application/macbinary',
			'doc' => 'application/msword',
			'word' => 'application/msword',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
			'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
			'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'class' => 'application/octet-stream',
			'dll' => 'application/octet-stream',
			'dms' => 'application/octet-stream',
			'exe' => 'application/octet-stream',
			'lha' => 'application/octet-stream',
			'lzh' => 'application/octet-stream',
			'psd' => 'application/octet-stream',
			'sea' => 'application/octet-stream',
			'so' => 'application/octet-stream',
			'oda' => 'application/oda',
			'pdf' => 'application/pdf',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',
			'smi' => 'application/smil',
			'smil' => 'application/smil',
			'mif' => 'application/vnd.mif',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',
			'wbxml' => 'application/vnd.wap.wbxml',
			'wmlc' => 'application/vnd.wap.wmlc',
			'dcr' => 'application/x-director',
			'dir' => 'application/x-director',
			'dxr' => 'application/x-director',
			'dvi' => 'application/x-dvi',
			'gtar' => 'application/x-gtar',
			'php3' => 'application/x-httpd-php',
			'php4' => 'application/x-httpd-php',
			'php' => 'application/x-httpd-php',
			'phtml' => 'application/x-httpd-php',
			'phps' => 'application/x-httpd-php-source',
			'swf' => 'application/x-shockwave-flash',
			'sit' => 'application/x-stuffit',
			'tar' => 'application/x-tar',
			'tgz' => 'application/x-tar',
			'xht' => 'application/xhtml+xml',
			'xhtml' => 'application/xhtml+xml',
			'zip' => 'application/zip',
			'mid' => 'audio/midi',
			'midi' => 'audio/midi',
			'mp2' => 'audio/mpeg',
			'mp3' => 'audio/mpeg',
			'm4a' => 'audio/mp4',
			'mpga' => 'audio/mpeg',
			'aif' => 'audio/x-aiff',
			'aifc' => 'audio/x-aiff',
			'aiff' => 'audio/x-aiff',
			'ram' => 'audio/x-pn-realaudio',
			'rm' => 'audio/x-pn-realaudio',
			'rpm' => 'audio/x-pn-realaudio-plugin',
			'ra' => 'audio/x-realaudio',
			'wav' => 'audio/x-wav',
			'mka' => 'audio/x-matroska',
			'bmp' => 'image/bmp',
			'gif' => 'image/gif',
			'jpeg' => 'image/jpeg',
			'jpe' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'png' => 'image/png',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'webp' => 'image/webp',
			'heif' => 'image/heif',
			'heifs' => 'image/heif-sequence',
			'heic' => 'image/heic',
			'heics' => 'image/heic-sequence',
			'eml' => 'message/rfc822',
			'css' => 'text/css',
			'html' => 'text/html',
			'htm' => 'text/html',
			'shtml' => 'text/html',
			'log' => 'text/plain',
			'text' => 'text/plain',
			'txt' => 'text/plain',
			'rtx' => 'text/richtext',
			'rtf' => 'text/rtf',
			'vcf' => 'text/vcard',
			'vcard' => 'text/vcard',
			'ics' => 'text/calendar',
			'xml' => 'text/xml',
			'xsl' => 'text/xml',
			'wmv' => 'video/x-ms-wmv',
			'mpeg' => 'video/mpeg',
			'mpe' => 'video/mpeg',
			'mpg' => 'video/mpeg',
			'mp4' => 'video/mp4',
			'm4v' => 'video/mp4',
			'mov' => 'video/quicktime',
			'qt' => 'video/quicktime',
			'rv' => 'video/vnd.rn-realvideo',
			'avi' => 'video/x-msvideo',
			'movie' => 'video/x-sgi-movie',
			'webm' => 'video/webm',
			'mkv' => 'video/x-matroska',
		];
		$ext = strtolower($ext);
		if (array_key_exists($ext, $mimes)) {
			return $mimes[$ext];
		}

		return 'application/octet-stream';
	}

	public function write_ini_file($assoc_arr, $path, $has_sections = FALSE)
	{
		$content = "";
		if ($has_sections) {
			foreach ($assoc_arr as $key => $elem) {
				$content .= "\n[" . $key . "]\n";
				foreach ($elem as $key2 => $elem2) {
					if (is_array($elem2)) {
						for ($i = 0; $i < count($elem2); $i++) {
							$content .= $key2 . "[] = \"" . $elem2[$i] . "\"\n";
						}
					} else if ($elem2 == "") $content .= $key2 . " = \n";
					else $content .= $key2 . " = \"" . $elem2 . "\"\n";
				}
			}
		} else {
			foreach ($assoc_arr as $key => $elem) {
				if (is_array($elem)) {
					for ($i = 0; $i < count($elem); $i++) {
						$content .= $key . "[] = \"" . $elem[$i] . "\"\n";
					}
				} else if ($elem == "") $content .= $key . " = \n";
				else $content .= $key . " = \"" . $elem . "\"\n";
			}
		}

		if (!$handle = fopen($path, 'w')) {
			return false;
		}

		$success = fwrite($handle, $content);
		fclose($handle);

		return $success;
	}

	public function getrandom($type)
	{
		switch ($type) {
			case 'country':
				$data = [
					"Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica",
					"Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain",
					"Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bonaire",
					"Bosnia and Herzegovina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam",
					"Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands",
					"Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia",
					"Comoros", "Congo", "Democratic Republic of the Congo", "Cook Islands", "Costa Rica", "Croatia", "Cuba", "Cyprus",
					"Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador",
					"Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji",
					"Finland", "France", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia",
					"Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guernsey",
					"Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard Island and Mcdonald Islands", "Holy See (Vatican City State)",
					"Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran, Islamic Republic of", "Iraq", "Ireland",
					"Isle of Man", "Israel", "Italy", "Jamaica", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kiribati",
					"Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan",
					"Lao People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania",
					"Luxembourg", "Macao", "Macedonia, the Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives",
					"Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico",
					"Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montenegro", "Montserrat", "Morocco",
					"Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "New Caledonia", "New Zealand", "Nicaragua",
					"Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau",
					"Palestine, State of", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland",
					"Portugal", "Puerto Rico", "Qatar", "Romania", "Russian Federation", "Rwanda", "Reunion", "Saint Barthelemy",
					"Saint Helena", "Saint Kitts and Nevis", "Saint Lucia", "Saint Martin (French part)", "Saint Pierre and Miquelon",
					"Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia",
					"Seychelles", "Sierra Leone", "Singapore", "Sint Maarten (Dutch part)", "Slovakia", "Slovenia", "Solomon Islands",
					"Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "South Sudan", "Spain", "Sri Lanka", "Sudan",
					"Suriname", "Svalbard and Jan Mayen", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic",
					"Taiwan, Province of China", "Tajikistan", "United Republic of Tanzania", "Thailand", "Timor-Leste", "Togo", "Tokelau",
					"Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda",
					"Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay",
					"Uzbekistan", "Vanuatu", "Venezuela", "Viet Nam", "British Virgin Islands", "US Virgin Islands", "Wallis and Futuna",
					"Western Sahara", "Yemen", "Zambia", "Zimbabwe", "Aland Islands"
				];
				shuffle($data);
				$res = $data[array_rand($data)];
				break;

			case 'useragent':
				$data = [
					"Mozilla/5.0 (Windows NT 10.0; WOW64; rv:54.0) Gecko/20100101 Firefox/" . rand(38, 56) . ".0",
					"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/" . rand(38, 56) . ".0.3071.115 Safari/537.36",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9) AppleWebKit/537.71 (KHTML, like Gecko) Version/7.0 Safari/537.71"
				];
				shuffle($data);
				$res = $data[array_rand($data)];
				break;

			case 'ip':
				$res = "" . mt_rand(1, 255) . "." . mt_rand(1, 255) . "." . mt_rand(1, 255) . "." . mt_rand(1, 255);
				break;

			case 'os':
				$data = ['Windows', 'Ubuntu', 'Mac OS', 'iOS', 'Android', 'Windows Phone'];
				shuffle($data);
				$res = $data[array_rand($data)];
				break;

			case 'device':
				$data = [
					'iPhone 6s', 'Samsung Galaxy S10+', 'Asus Zenfone 5z', 'Ipad Pro', 'iPhone 7+', 'iPhone 7', 'iPhone 8+',
					'Macbook Retina Pro', 'Samsung Galaxy S9+', 'Samsung Galaxy Note 8', 'Samsung Galaxy S8', 'Samsung Galaxy S8+',
					'Samsung Galaxy Note 9', 'iPhone Xs Max', 'iPhone X'
				];
				shuffle($data);
				$res = $data[array_rand($data)];
				break;

			default:
				$res = '';
				break;
		}
		return $res;
	}

	public function banner()
	{
		$this->climate->LightMagenta()->border('$', 50);
		$this->climate->LightMagenta(
			'██████╗ ██╗   ██╗████████╗██╗  ██╗██╗   ██╗███████╗███╗   ██╗███████╗'
		);
		$this->climate->LightMagenta(
			'██╔══██╗██║   ██║╚══██╔══╝██║  ██║██║   ██║██╔════╝████╗  ██║██╔════╝'
		);
		$this->climate->LightMagenta(
			'██████╔╝██║   ██║   ██║   ███████║██║   ██║█████╗  ██╔██╗ ██║███████╗'
		);
		$this->climate->LightMagenta(
			'██╔══██╗██║   ██║   ██║   ██╔══██║╚██╗ ██╔╝██╔══╝  ██║╚██╗██║╚════██║'
		);
		$this->climate->LightMagenta(
			'██║  ██║╚██████╔╝   ██║   ██║  ██║ ╚████╔╝ ███████╗██║ ╚████║███████║'
		);
		$this->climate->LightMagenta(
			'╚═╝  ╚═╝ ╚═════╝    ╚═╝   ╚═╝  ╚═╝  ╚═══╝  ╚══════╝╚═╝  ╚═══╝╚══════╝'
		);
		$this->climate->LightMagenta('[+] Sendgrid Sender v.1 --- by Dams @ Ruthvens Fams---               ');
		$this->climate->LightMagenta()->border('$', 50);

		$this->climate->out(' ');
	}

	public function randomstr($type = 'mix', $kind = 'mix', $length = 12)
	{
		switch ($type) {
			case 'number':
				$res = '0123456789';
				break;

			case 'mix':
				$res = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;

			case 'text':
				$res = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;

			default:
				$res = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
		}

		$strlen = strlen($res);
		$str    = '';
		for ($i = 0; $i < $length; $i++) {
			$str .= $res[rand(0, $strlen - 1)];
		}

		if ($kind == 'upp') {
			$str = strtoupper($str);
		} else if ($kind == 'low') {
			$str = strtolower($str);
		}

		return $str;
	}

	public function customreplace($key)
	{
		if (!array_key_exists($key, $this->config['tag'])) {
			$body = 'custom tag not found.';
		} else {
			$data = explode('|', $this->config['tag'][$key]);
			shuffle($data);
			$body = $data[array_rand($data)];
		}

		return $body;
	}

	public function replace($text)
	{
		$text = preg_replace_callback(
			"/##custom_tag_(\w+)##/",
			function($matches) {
				return $this->customreplace($matches[1]);
			},
			$text
		);

		$f = [
			"##useragent##",
			"##randip##",
			"##randcountry##",
			"##randos##",
			"##device##",
			"##date_1##",
			"##date_2##",
			"##date_3##",
			"##date_4##"
		];
		$t = [
			$this->getrandom('useragent'),
			$this->getrandom('ip'),
			$this->getrandom('country'),
			$this->getrandom('os'),
			$this->getrandom('device'),
			date('D, F d, Y g:i A'),
			date('D, F d, Y'),
			date('F d, Y g:i A'),
			date('F d, Y')
		];
		$text = str_ireplace($f, $t, $text);

		$text = (($this->config['setup']['randomparam'] == true) ? str_ireplace("##link##", $this->config['setup']['link'] . "?##mix_mix_4##=##mix_mix_12##", $text) : str_ireplace("##link##", $this->config['setup']['link'], $text));

		$text = preg_replace_callback(
			"/##(\w+)_(\w+)_(\d+)##/",
			function($matches) {
				return $this->randomstr($matches[1], $matches[2], $matches[3]);
			},
			$text
		);

		return $text;
	}

	public function formatSend($setting = array(), $lists = array())
	{
		$api = new \Sendgrid($setting['api']);
		$mail = new Mail();

		$mail->setFrom($setting['fmail'], $setting['fname']);
		$mail->setSubject($setting['subject']);

		$receipt = new Personalization();

		if ($setting['to'] != '' or $setting['to'] != NULL) {
			$tos = explode('|', $setting['to']);
			foreach ($tos as $key => $to) {
				$receipt->addTo(new To($to));
			}
		}

		foreach ($lists as $key => $list) {
			if ($setting['to'] == '' or $setting['to'] == NULL) {
				if ($key == 0) {
					$receipt->addTo(new To($list));
				} else {
					if (strtolower($this->config['setup']['send_as']) == 'to') {
						$receipt->addTo(new To($list));
					}

					if (strtolower($this->config['setup']['send_as']) == 'cc') {
						$receipt->addCc(new Cc($list));
					}

					if (strtolower($this->config['setup']['send_as']) == 'bcc') {
						$receipt->addBcc(new Bcc($list));
					}
				}
			} else {
				if (strtolower($this->config['setup']['send_as']) == 'to') {
					$receipt->addTo(new To($list));
				}

				if (strtolower($this->config['setup']['send_as']) == 'cc') {
					$receipt->addCc(new Cc($list));
				}

				if (strtolower($this->config['setup']['send_as']) == 'bcc') {
					$receipt->addBcc(new Bcc($list));
				}
			}
		}

		if ($this->config['setup']['emailtest'] != '' or $this->config['setup']['emailtest'] != NULL) {
			$emailtests = explode('|', $this->config['setup']['emailtest']);
			foreach ($emailtests as $key => $emailtest) {
				if (strtolower($this->config['setup']['send_as']) == 'to') {
					$receipt->addTo(new To($emailtest));
				}

				if (strtolower($this->config['setup']['send_as']) == 'cc') {
					$receipt->addCc(new Cc($emailtest));
				}

				if (strtolower($this->config['setup']['send_as']) == 'bcc') {
					$receipt->addBcc(new Bcc($emailtest));
				}
			}
		}

		// $receipt->addTo(new To("test2@example.com"));
		// $receipt->addCc(new Cc("test3@example.com"));
		// $receipt->addBcc(new Bcc("test5@example.com"));

		if ($this->config['setup']['header'] == true) {
			foreach ($this->config['header'] as $key => $value) {
				$receipt->addHeader(new Header($key, $value));
			}
		}

		$mail->addPersonalization($receipt);

		$konten = (($this->config['setup']['send_type'] == 'text') ? 'text/plain' : 'text/html');
		$content = new Content($konten, base64_decode($setting['letter']));
		$mail->addContent($content);

		if ($this->config['setting']['attach_file'] != '' or $this->config['setting']['attach_file'] != NULL) {
			$attachment = new Attachment();
			$attachment->setContent($setting['attach_file']['content']);
			$attachment->setType($setting['attach_file']['ext']);
			$attachment->setFilename($setting['attach_file']['filename']);
			$mail->addAttachment($attachment);
		}

		$tracking_settings = new TrackingSettings();
		$click_tracking = new ClickTracking();
		$click_tracking->setEnable($this->config['setup']['click_track']);
		$click_tracking->setEnableText($this->config['setup']['click_track']);
		$tracking_settings->setClickTracking($click_tracking);
		$open_tracking = new OpenTracking();
		$open_tracking->setEnable($this->config['setup']['open_track']);
		$tracking_settings->setOpenTracking($open_tracking);
		$mail->setTrackingSettings($tracking_settings);

		$reply_to = new ReplyTo($setting['fmail'], $setting['fname']);
		$mail->setReplyTo($reply_to);

		// echo json_encode($mail, JSON_PRETTY_PRINT), "\n";
		// return $mail;
		try {
			$response = $api->client->mail()->send()->post($mail);
			return ['code' => $this->errors($response->statusCode())['code'], 'msg' => $this->errors($response->statusCode())['msg']];
		} catch (Exception $e) {
			return ['code' => 999, 'msg' => $e->getMessage()];
		}
	}
}
