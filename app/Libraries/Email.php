<?php
namespace App\Libraries;

require_once "Mail.php";
require_once "Mail/mime.php";
error_reporting(E_ERROR | E_PARSE);

class Email
{
	public $to = '';
	public $subject = '';
	public $html = '';
	public $from = '';
	public $images = array();

	private $smtp_host 		= 'smtp.gmail.com';
	private $smtp_username 	= 'noreply@makeadiff.in';
	private $smtp_password 	= 'noreplygonemad';

	function send() {
		$headers = array (  'From'      => $this->from,
							'To'        => $this->to,
							'Subject'   => $this->subject);

		$mime = new \Mail_mime(array('eol' => "\n"));
		$mime->setHTMLBody($this->html);

		foreach($this->images as $image) {
			$name = basename($image);
			$sucess[] = $mime->addHTMLImage($image,"image/png",'',true, $name);
		}

		$smtp = \Mail::factory('smtp',
			array ( 'host'     => $this->smtp_host,
					'auth'     => true,
					'username' => $this->smtp_username,
					'password' => $this->smtp_password));

		$body = $mime->get();
		$headers = $mime->headers($headers);

		$mail = $smtp->send($this->to, $headers, $body);

		if (\PEAR::isError($mail)) {
			//echo("<p>" . $mail->getMessage() . "</p>");
			return false;
		}

		return true;
	}
}