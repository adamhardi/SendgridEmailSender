<?php
$config['api'] = 'api.ini';

$config['list'] = [
	'file' 			=> 'list.txt',
	'remove_send' 	=> false,
	'remove_dupp' 	=> false,
];

$config['setup'] = [
	'send_as'						=> 'to', # bcc / cc / to | default: to
	'send'							=> '1', # Email will stack | default: 1
	'sleep'							=> '3', # default: 0
 	'open_track'					=> true, # default: false
	'click_track' 					=> true, # default: false
	'send_type'  					=> 'html', # html or text | default: html
	'emailtest'						=> '', # leave it empty if no | can be seperated by pipe (|)
	'randomparam'					=> true, # if u using randomparam please input your link | default: false
	'link'							=> 'https://google.com/', # use ##link## tag on your letter
	'header'						=> false, # header |  default: false
];

$config['header'] = [
	'header' 							=> "value",
];

$config['tag'] = [
	# 'name' => 'value', 
	'country'							=> 'mexico|argentina|united states',

	# you can also use default tag. example usage: ##custom_tag_subject##
	// 'randomcustom'						=> '##mix_mix_4##|##number_mix_10##|##letter_upp_12##', 
	// 'subject'                           => 'hi|hello|hello world ##mix_mix_4##',
	// 'attachname'						=> 'blabla.pdf|ok-##mix_mix_4##.pdf|invoice-##mix_mix_12##.pdf',

];

$config['setting'] = [
		'to' 						=> '', # default: first email of sending list. multi to? seperated by pipe (|)
		'fname' 					=> 'Ruthvens Fams', # form name as usual. multi fname? you can use tag.
		'fmail'						=> 'noreply.##mix_mix_12##.##mix_mix_6##', # this wil go fmail@your_domain_api.tld | multi fmail? ? you can use tag.
		'subject' 					=> 'Hello - ##number_mix_10##', # subject, multi subject? you can use tag.
		'letter'					=> 'letter.txt', # letter,  random letter? ? you can use tag.
		'attach_file'				=> '', # leave it empty if no, random file? ? you can use tag.
		'attach_name' 				=> '', # attachment name will change, leave it empty if no, random name? you can use tag.
];

?>
