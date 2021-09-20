<?php
namespace App\Libraries;

use Log;
use DOMDocument;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
        $mail = new PHPMailer(true);

        if(stripos($this->from, 'donations@makeadiff.in') !== false) {
            $this->smtp_username = "donations@makeadiff.in";
            $this->smtp_password = "Fell-chose-5";
        } elseif(stripos($this->from, 'madapp@makeadiff.in') !== false) {
            $this->smtp_username = "madapp@makeadiff.in";
            $this->smtp_password = "madappgonemad";
        }

        try {
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;               //Enable verbose debug output
            $mail->isSMTP();                                        //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                   //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                               //Enable SMTP authentication
            $mail->Username   = $this->smtp_username;                     //SMTP username
            $mail->Password   = $this->smtp_password;                     //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            if(isset($this->from)) {
                $mail->setFrom($this->from, 'Make A Difference');
            } else {
                $mail->setFrom($this->smtp_username, "Make A Difference");
            }
            $mail->addAddress($this->to);

            // Extract images
            $doc = new DOMDocument();
            @$doc->loadHTML($this->html);
            $images = $doc->getElementsByTagName('img');
            foreach($images as $image_ele) {
                $image = $image_ele->getAttribute('src');
                $image_path = realpath($image);
                $image_filename = basename($image_path);
                $this->html = str_replace($image, "cid:$image_filename", $this->html); // $image should NOT be $image_path
                
                $mail->AddEmbeddedImage($image_path, $image_filename);
            }

            //Attachments
            foreach($this->attachments as $attachment) {
                $mail->addAttachment($attachment);
            }
            
            //Content
            $mail->isHTML(true); //Set email format to HTML
            $mail->Subject = $this->subject;
            $mail->Body    = $this->html;
            $mail->AltBody = \Soundasleep\Html2Text::convert($this->html) . "--\nMake a Difference\nhttp://makeadiff.in/";

            $mail->send();
            return [true, ''];

        } catch (Exception $e) {
            return [false, "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
        }
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
