<?php

namespace App\Service;

#use Psr\Container\ContainerInterface;

class MailBuilder
{
    private $smtp_host;
    private $smtp_user;
    private $smtp_password;
    private $smtp_port;
    private $smtp_encrypt;


    public function __construct($smtp_host, $smtp_user, $smtp_password, $smtp_port, $smtp_encrypt)
    {
        #$this->container = $container;
        $this->smtp_host = $smtp_host;
        $this->smtp_user = $smtp_user;
        $this->smtp_password = $smtp_password;
        $this->smtp_port = $smtp_port;
        $this->smtp_encrypt = $smtp_encrypt;
    }

    public function getMailer() :\Swift_Mailer
    {
        $transport = new \Swift_SmtpTransport( $this->smtp_host, $this->smtp_port, $this->smtp_encrypt );
        $transport
        ->setUsername($this->smtp_user)
        ->setPassword($this->smtp_password)
        ;

        $mailer = new \Swift_Mailer($transport);

        //Pour le debug
        //$logger = new \Swift_Plugins_Loggers_ArrayLogger();
        //$mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));
        //echo $logger->dump() // apres envoir

        return $mailer;
    }


    public function createMessage(array $options) : \Swift_Message
    {
        $message = new \Swift_Message();
        $message
            ->setSubject($options['objet'])
            ->setFrom($options['from'])
            ->setReplyTo($options['replyTo'])
            ->setTo($options['to'])
            //->setReturnPath("")
            ->setBody($options['html'],'text/html')
            /*
            $this->renderView(
            // templates/emails/registration.html.twig
            'emails/registration.html.twig',
            array('name' => $name)
            ),
            */
            ->addPart($options['text'],'text/plain')
        ;

        //if($options['idCampaign'] != null && $options['trackingId'] != null){
        //    $headers = $message->getHeaders();
            /*$headers->addPathHeader('X-Abuse-Reports-To', 'complaint@actions-pdv-l.fr');
            $headers->addPathHeader('Your-Header-Name', 'person@example.org');
            X-Abuse-Reports-To - abuse
            X-CSA-complaints - whitelist
            $headers->addTextHeader('X-Mine', 'something here');
            $headers->addParameterizedHeader(
            'Header-Name', ' header value'
            array('foo' => 'bar', 'foo2' => 'bar2')
            );
            var_dump($headers->get('Return-Path'));
            */
        //    $headers->addTextHeader('X-CampaignId', $options['idCampaign']);
        //    $headers->addTextHeader('X-TrackingId', $options['trackingId']);
        //}

    return $message;
    }
}
