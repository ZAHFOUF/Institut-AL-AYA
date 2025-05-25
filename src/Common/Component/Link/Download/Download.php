<?php

namespace AlAya\Common\Component\Link\Download;

use AlAya\Common\Service\AuthChecker;
use AlAya\Common\Service\Encryptor;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: "@componentsTemplates/Link/Download/Download.twig",name:"Link:Download")]
class Download
{
    public string $file;
    public string $access = "open";
    public bool $visible = true;

    function __construct(private AuthChecker $authChecker,private Encryptor $encryptor,private UrlGeneratorInterface $router,private UriSigner $signer)
    {
        
    }
  

    public function mount(string $file,string $access = "open")
    {
       /* if (!is_null($access) and !$this->authChecker->checkPermission($access)) {
            # code...
        } */
    }

    public function generateUrl()  {


        $url = $this->router->generate("api_download",
        [
            'file' => $this->file,
            'access' => $this->encryptor->encrypt($this->access),
        ]);


        $signedUrl = $this->signer->sign($url);

        return $signedUrl;

        
    }

}
