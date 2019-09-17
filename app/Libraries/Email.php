<?php
namespace App\Libraries;

use Log;

require_once "Mail.php";
require_once "Mail/mime.php";
error_reporting(E_ERROR | E_PARSE);

class Email
{
    public $to = '';
    public $subject = '';
    public $html = '';
    public $from = '';
    public $images = [];
    public $attachments = [];

    private $smtp_host 		= 'smtp.gmail.com';
    private $smtp_username 	= 'noreply@makeadiff.in';
    private $smtp_password 	= 'noreplygonemad';

    public function send()
    {
        $headers = [
            'From'      => $this->from,
            'To'        => $this->to,
            'Subject'   => $this->subject
        ];

        $mime = new \Mail_mime(array('eol' => "\n"));

        $image_index = 0;
        foreach ($this->images as $key => $image) {
            $mime->addHTMLImage($image, mime_content_type($image), $key);
            $cid = $mime->_html_images[$image_index]['cid'];
            $file = basename($image);
            $this->html = str_replace("%CID-$file%", $cid, $this->html);
            $image_index++;
        }

        foreach ($this->attachments as $attachment_file) {
            if ($attachment_file and file_exists($attachment_file)) {
                $mime->addAttachment($attachment_file, mime_content_type($attachment_file));
            }
        }

        $smtp = \Mail::factory('smtp', [
                    'host'     => $this->smtp_host,
                    'auth'     => true,
                    'username' => $this->smtp_username,
                    'password' => $this->smtp_password
                ]);

        $mime->setHTMLBody($this->html);
        $body = $mime->get();
        $headers = $mime->headers($headers);

        $mail = $smtp->send($this->to, $headers, $body);

        if (\PEAR::isError($mail)) {
            Log::error("Error sending email '{$this->subject}' to '{$this->to}' : " . $mail->getMessage());
            return false;
        }

        return true;
    }

    public function queue()
    {
        $message = [
            'type'		=> 'email',
            'to'		=> $this->to,
            'from'		=> $this->from,
            'subject'	=> $this->subject,
            'body'		=> $this->html,
            'images'	=> json_encode($this->images),
            'attachments'=>json_encode($this->attachments),
            'added_on'	=> date('Y-m-d H:i:s'),
            'status'	=> 'pending'
        ];
        app('db')->table('Message_Queue')->insert($message);

        return true;
    }
}
