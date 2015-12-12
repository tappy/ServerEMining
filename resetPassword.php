<?php
function __autoload($class_name)
{
    include $class_name . ".php";
}

class resetPassword extends database_manager
{
    private $email;

    function __construct()
    {
        $this->email = filter_input(INPUT_POST, "Email");
        //$this->$email = "kongsin1";
        $this->setNewPass($this->email, $this->genPassword());
    }

    function genPassword()
    {
        $str = "";
        $cha = array();
        for ($i = 'a'; $i <= 'z'; $i++) {
            $cha[] = $i;
        }
        for ($j = 0; $j < 6; $j++) {
            $str .= $cha[rand(0, (count($cha) - 1))];
        }
        return $str;
    }

    function setNewPass($email, $pass)
    {
        $val=array();
        if ($this->connection()) {
            if (count($this->find("customer_table", "cus_email", "cus_email", "'" . $email . "'")) > 0) {
                if ($this->sendMail($email, $this->find("customer_table", "cus_user", "cus_email", "'" . $email . "'")[0]['cus_user'], $pass)) {
                    if ($this->updatePass($email, $pass)) {
                        $val['result'] = "0";
                    } else {
                        $val['result'] = "1";
                    }
                } else {
                    $val['result'] = "2";
                }
            } else {
                $val['result'] = "3";
            }
        } else {
            $val['result'] = "4";
        }
        echo json_encode($val);
    }

    function sendMail($email, $user, $pass)
    {
        /**
         * This example shows settings to use when sending via Google's Gmail servers.
         */

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
        date_default_timezone_set('Etc/UTC');
//Create a new PHPMailer instance
        $mail = new PHPMailer;

//Tell PHPMailer to use SMTP
        $mail->isSMTP();

//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
        $mail->SMTPDebug = 0;

//Ask for HTML-friendly debug output
        $mail->Debugoutput = 'html';

//Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';

//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = 587;

//Set the encryption system to use - ssl (deprecated) or tls
        $mail->SMTPSecure = 'tls';

//Whether to use SMTP authentication
        $mail->SMTPAuth = true;

//Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = "kat.application@gmail.com";

//Password to use for SMTP authentication
        $mail->Password = "katapplication";

//Set who the message is to be sent from
        $mail->setFrom('kat.application@gmail.com', 'Knowledge analysis tool application (KAT)');

//Set an alternative reply-to address
        $mail->addReplyTo($email, 'User of KAT');

//Set who the message is to be sent to
        $mail->addAddress($email, 'User of KAT');

//Set the subject line
        $mail->Subject = 'New password from Knowledge analysis tool (KAT)';

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
        $mail->msgHTML("<!doctype html><html lang='en'><head><meta charset='UTF-8'></head><body><div>นำนำชื่อผู้ใช้และรหัสผ่านด้านล่างนี้เพื่อใช้ในการเข้าสู่ระบบ</div><br/><div>ชื่อผู้ใช้: " . $user . "</div><br/><div>รหัสผ่าน: " . $pass . "</div><br/></body></html>");

        if (!$mail->send()) {
            return false;
        } else {
            return true;
        }
    }

}

$reset = new resetPassword();