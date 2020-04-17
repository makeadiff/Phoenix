<?php
namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\User;
// use App\Libraries\SMS;
use App\Libraries\Email;

class sendOtp
{
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user_model = new User;
        $matched_users = $user_model->search($args);
        $user = $matched_users->first();

        // We are making the OTP from the users ID, Email, Phone and today's date(will automatically 'expire' at end of day). 
        //  This will avoid a DB write/adding DB columns. BUT. It will make it PREDICTABLE. 
        //  Right now, I'm condisdering it decent trade off. Later might have to update it.
        $md5 = md5($user->id . $user->email . $user->phone . date("Y-m-d"));
        $md5_otp = substr($md5, -4); // Last 4 chars of the MD5 will be the OTP. 

        if($args['email'] === $user->email) {
            $mail = new Email;
            $mail->from     = "MADApp <madapp@makeadiff.in>";
            $mail->to       = $user->email;
            $mail->subject  = "Email Verification OTP";

            $base_path = app()->basePath();
            $base_url = url('/');

            $html = "<p>To verify your email, please enter this OTP:</p>
                    <p><strong>$md5_otp</strong></p>
                    <p>This OTP will expire by end of day today. Please enter it befor that time.</p>";

            $email_html = file_get_contents($base_path . '/resources/email_templates/template.html');
            $mail->html = str_replace(
                array('%BASE_FOLDER%','%BASE_URL%', '%CONTENT%', '%NAME%', '%DATE_TIME%'),
                array($base_path, $base_url, $html, $user->name, date('d/m/Y H:i:s')),
                $email_html
            );
            $mail->images = [
                $base_path . '/public/assets/header.jpg',
            ];
            $mail->send();
        } 
        // :TODO: SMS Sending.

        return $user_model->find($user->id);
    }
}
