<?php

namespace AlAya\Common\Twig;

use AlAya\Common\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class TwigGlobals extends AbstractExtension implements GlobalsInterface
{
    private array $globals;

    public function __construct(private EntityManagerInterface $manager)
    {
        $this->globals = [
            'method_paypal' => (bool)$manager->getRepository(Setting::class)->findOneBy(['param' => 'method_paypal'])?->getValue() ?? false,
            'method_rib' => (bool) $manager->getRepository(Setting::class)->findOneBy(['param' => 'method_rib'])?->getValue() ?? false,
        ];
    }
  

    public function getGlobals(): array
    {
        return $this->globals;
    }
}
