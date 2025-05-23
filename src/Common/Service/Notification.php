<?php


/*
 *  
 * Notify users via different channels in the Symfony app
 * 
 */


 namespace AlAya\Common\Service ;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Notification
{
    private $mailer;
    public $params;
    public $manager ;
    public $doctrine ;

    public function __construct(MailerInterface $mailer,ParameterBagInterface $p,EntityManagerInterface $e,ManagerRegistry $m)
    {
        $this->mailer = $mailer;
        $this->params = $p;
        $this->manager = $e;
        $this->mailer = $mailer;
        $this->doctrine = $m ;
    }

    
    public function sendNotificationEmail(string $client,string $subject,string $template ,$params = [],$files=  []) : void
    {

        $email = (new TemplatedEmail())->from(new Address($this->params->get("mail.address"), $this->params->get("from.mail"))) ;



            $email->to( new Address($client)) 
           ->subject($subject)
           ->htmlTemplate($template) 
           ->context($params) ;
        
           if ($files and  is_array($files)) {
             foreach ($files as $file) {
                
                if ($file instanceof UploadedFile) {
                    $email->attachFromPath($file->getPathname(), $file->getClientOriginalName());
                }else{
                    $email->attachFromPath($file,basename($file));
                }
                }
           }



        try {
            $this->mailer->send($email);
        }
        catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
      
    }


}
