<?php
/**
 * @author alari
 * @since 8/23/12 3:41 PM
 *
 * Layout is //email-views/list-name or //email-views/email-layout
 * Data is mail + ["subscriber"]
 */
class EmailSenderModule extends CWebModule
{
    const NOBODY = "NaN";

    public $host;
    public $port;
    public $security;
    public $username;
    public $password;
    public $from;
    public $fromTitle;
    public $mode = "mail";

    public $subscribeLists = array();

    public $unsubscribeView = "unsubscribe";

    public $adminGenOverride = array("emailSender/mailSubscribe/*");

    public function init()
    {
    }

    /**
     * Puts a new message to a queue
     * @param $to
     * @param $subject
     * @param $body
     * @param $type
     */
    public function put($to, $subject, $body, $type = "text/html")
    {
        Yii::app()->getDb()->createCommand()->insert("{{mail_sender_queue}}", array(
            "mail_to" => $to,
            "mail_subject" => $subject,
            "mail_body" => $body,
            "mail_type" => $type
        ));
    }

    /**
     * Puts a message to a subscription list
     * @param $listName
     * @param $subject
     * @param $body
     * @param string $type
     */
    public function putToList($listName, $subject, $body, $type = "text/html")
    {
        Yii::app()->getDb()->createCommand()->insert("{{mail_sender_queue}}", array(
            "mail_to" => self::NOBODY,
            "mail_subject" => $subject,
            "mail_body" => $body,
            "mail_type" => $type,
            "list" => $listName
        ));
    }

    /**
     * Tries to send unprocessed messages
     * @return array
     */
    public function processQueue(CConsoleCommand $caller)
    {
        $log = array();
        // Loading unprocessed messages
        $mails = Yii::app()->getDb()->createCommand()
            ->select("*")
            ->from("{{mail_sender_queue}}")
            ->where("processed=0")
            ->limit(50)
            ->queryAll();
        $log[] = "Loaded " . count($mails) . " unprocessed mails";
        // Starting processing mails
        if (count($mails)) foreach ($mails as $mail) {
            // Trying to update a message -- if it's not processed in another thread
            if (!Yii::app()->getDb()->createCommand("UPDATE {{mail_sender_queue}} SET processed=1 WHERE id=:id AND processed=0")->execute(array("id" => $mail["id"]))) {
                $log[] = "Cannot update `processed` field for " . $mail["id"] . ", skipping";
                continue;
            }
            // It's a list -- make copies for each recipient
            if ($mail["list"] && $mail['mail_to'] == self::NOBODY) {
                $this->processListMessage($mail);
                $log[] = "Prepared message to a subscribers list: " . $mail["id"];
                // It's a common message
            } else {
                $content = $this->mailContent($mail, $caller);
                if (!$this->send($mail["mail_to"], $mail["mail_subject"], $content, $mail["mail_type"])) {
                    $log[] = "Cannot send " . $mail["id"] . ", making unprocessed again";
                    Yii::app()->getDb()->createCommand("UPDATE {{mail_sender_queue}} SET processed=0 WHERE id=:id AND processed=1", array("id" => $mail["id"]));
                    // Common message not sent
                } else {
                    $log[] = "Sent: " . $mail["id"];
                }
            }
            Yii::app()->getDb()->createCommand("DELETE FROM {{mail_sender_queue}} WHERE processed=1")->execute();
        }
        return $log;
    }

    private function processListMessage($mail)
    {
        $subscribers = Yii::app()->getDb()->createCommand()->select()->from("{{mail_subscribe}}")->where("list=:list", array("list" => $mail["list"]))->queryAll();
        unset($mail["id"]);
        foreach ($subscribers as $s) {
            $a = $mail;
            $a["mail_to"] = $s["email"];
            Yii::app()->getDb()->createCommand()->insert("{{mail_sender_queue}}", $a);
        }
    }

    private function mailContent($mail, CConsoleCommand $caller)
    {
        $layout = $mail["list"] ? $mail["list"] : "email-layout";
        $layout = is_file(getcwd() . "/views/email-views/$layout.php") ? getcwd() . "/views/email-views/$layout.php" : null;
        if (!$layout) return $mail["mail_body"];
        if ($mail["list"]) {
            $mail["subscriber"] = Yii::app()->getDb()->createCommand()
                ->select()
                ->from("{{mail_subscribe}}")
                ->where("list=:list AND email=:email", array("list" => $mail["list"], "email" => $mail["mail_to"]))
                ->queryRow();
        }
        return $caller->renderFile($layout, $mail, true);
    }

    /**
     * Sends a message
     * @param $to
     * @param $subject
     * @param $body
     * @param $type
     * @return int|null
     */
    public function send($to, $subject, $body, $type = "text/html")
    {
        require_once __DIR__ . "/../../components/swiftmailer/lib/swift_required.php";
        Yii::registerAutoloader('_swiftmailer_init');
        try {
            $transport = $this->getTransport();
            $mailer = Swift_Mailer::newInstance($transport);


            $message = Swift_Message::newInstance($subject)
                ->setFrom(array($this->from => $this->fromTitle))
                ->setTo(array($to))
                ->setBody($body, $type);

            return $mailer->send($message);
        } catch (Exception $e) {
            echo $e;
            return null;
        }
    }

    private function getTransport()
    {
        if ($this->mode == "smtp") {
            return Swift_SmtpTransport::newInstance($this->host, $this->port, $this->security)
                ->setUsername($this->username)
                ->setPassword($this->password);
        }
        return Swift_MailTransport::newInstance();
    }

    /**
     * Adds email to a list of subscribers
     * @param $listName
     * @param $email
     */
    public function subscribe($listName, $email)
    {
        $subscribe = new MailSubscribe();
        $subscribe->list = $listName;
        $subscribe->email = $email;
        $subscribe->hashcode = uniqid();
        $subscribe->save();
    }

    public function adminGenLinks()
    {
        return array(
            "label" => Yii::t("app", "Email Subscribe"),
            "url" => "#",
            "items" => array(
                array("label" => Yii::t("app", "Email Subscribe Lists"), "url" => array("/emailSender/mailSubscribe/admin"), 'visible' => !Yii::app()->user->isGuest && count($this->subscribeLists)),
                array("label" => Yii::t("app", "Send Subscribers"), "url" => array("/emailSender/mailSubscribe/send"), 'visible' => !Yii::app()->user->isGuest && count($this->subscribeLists)),
            )
        );

    }
}
