<?php

namespace AlAya\Common\Twig;

use AlAya\Common\Entity\Agent;
use AlAya\Common\Entity\Message;
use AlAya\Common\Entity\Prestation;
use AlAya\Common\Entity\Setting;
use AlAya\Common\Repository\AgentRepository;
use Symfony\Component\Asset\Packages;
use AlAya\Common\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{

    public array $settings ;
    
    function __construct(private EntityManagerInterface $manager, private AgentRepository $agentRepository,private Packages $packages,private Registry $workflowRegistry)
    {
        
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('userCan', [$this, 'userHasPermission']),
            new TwigFunction('getLogo', [$this, 'getLogo']),
            new TwigFunction('getLogoProvider', [$this, 'getLogoProvider']),
            new TwigFunction('getMonth', [$this, 'getMonth']),
            new TwigFunction('generateUserId', [$this, 'generateUserId']),
            new TwigFunction('witchUser', [$this, 'witchUser']),
            new TwigFunction('generateUrl', [$this, 'generateUrl']),
            new TwigFunction('calcPaypalAmount',[$this, 'calcPaypalAmount']),
            new TwigFunction('unReadMsg',[$this, 'unReadMsg']),
            new TwigFunction("calculerTotalPrestation",[$this,"calculerTotalPrestation"]),
            new TwigFunction("amountBill",[$this,"amountBill"]),
            new TwigFunction('workflow', [$this, 'workflow']) ,
            new  TwigFunction('getServiceName', [$this, 'getServiceName']),
            new TwigFunction('toChart', [$this, 'toChart']),
            new TwigFunction('toChartMonth', [$this, 'toChartMonth']),
            new TwigFunction('toChartYear', [$this, 'toChartYear'])
        ];
    }
    

    public function getFilters(): array
    {
        return [
            new TwigFilter('truncate_words', [$this, 'truncateWords']),
            new TwigFilter('countProjects', [$this, 'countProjects']),
            new TwigFilter('array_values', [$this, 'array_values']),
            new TwigFilter('array_keys', [$this, 'array_keys']),
            new TwigFilter('contains', [$this, 'contains']),
        ];
    }

    public function userHasPermission(string $attribute, Agent $user)
    {
        $permissions = $this->agentRepository->findPermissions($user);
        return in_array($attribute, [...$permissions]);
    }

    

    public function truncateWords(string $text, int $limit = 30): string
    {
        $words = explode(' ', $text);
        if (count($words) > $limit) {
            $words = array_slice($words, 0, $limit);
            return implode(' ', $words) . '...';
        }
        return $text;
    }

    public function getLogoProvider() : string
    {
        return $this->packages->getUrl('bundles/techprovidertheme/images/projects-freelance.png');
    }


    public function getLogo() : string
    {
        return $this->packages->getUrl('bundles/techcustomerthemebundle2/images/logo.png');
    }

    public function getMonth($month) : string
    {
        if ($month < 1 || $month > 12) {
            return 'Invalid month';
        }
        if ($month < 10) {
            $month = ltrim($month, '0'); // Remove leading zero if present
        }
        $months = [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre',
        ];
        return $months[$month];
    }

    public function array_values($array)  {
        return array_values($array) ;
}

public function array_keys($array)  {
     return array_keys($array) ;
 }

 public function contains($array, $value) {
    return in_array($value, $array);
}


public function generateUserId(object $user) {
       $prefix = $this->witchUser($user);
       $id = $user->getId();
       return $prefix . $id ;
}

public function generateUrl($user, $route) {
   $prefix = $this->witchUser($user);
   return $prefix . $route; ;
}

public function unReadMsg($user) {
    return $this->manager->getRepository(Message::class)->unReadMsg($user);
}

public function witchUser(object $user) {
   return $user instanceof Agent ? 'A_' : 'S_';
}

   

  public function getSettings($key) : ?string
  {
    $setting = $this->manager->getRepository(Setting::class)->findOneBy(['param' => $key]);
    if ($setting) {
        return $setting->getValue();
    }
    return null;
  }

  public function calcPaypalAmount($amount)  {
      return ($amount * 2.9 / 100) + $amount + 0.35;
  }

  public function calculerTotalPrestation($prestation) : float
  {
      return calculerTotalPrestation($prestation);
  }

  public function amountBill($bill) : float
  {
      return amountBill($bill);
  }

  public function workflow (object $entity){
        $data = [];
        $trans = $this->workflowRegistry->get($entity)->getEnabledTransitions($entity) ;
        foreach ($trans as $transition) {
            $data[] = [
                "name" => $transition->getName(),
                ...$this->workflowRegistry->get($entity)->getMetadataStore()->getTransitionMetadata($transition)
            ];
        }
        return $data;
    }

    public function getServiceName(Prestation $prestation) : string
    {
       return getServiceName($prestation);
    }

     public function toChart (array $data)
  {
      $chartData = [];
      $chartData[] = ["data" => [...array_map(fn($item) => $item["Total recettes cours"] ?? 0, $data)] , "name" => "Total recettes cours" , "type" => "line"];
    $chartData[] = [
        "data" => [...array_map(fn($item) => $item["Révenue total"] ?? 0, $data)],
        "name" => "Révenue total",
        "type" => "bar"
    ];
    $chartData[] = [
        "data" => [...array_map(fn($item) => $item["Total recettes supports de cours"] ?? 0, $data)],
        "name" => "Total recettes supports de cours",
        "type" => "line"
    ];
      return json_encode($chartData);
  }

   public function toChartMonth (array $data) {
          $chartData = [...array_map(fn($item) => $item["Mois"] ?? 0, $data)];
          return json_encode($chartData);
   }

   public function toChartYear (array $data) {
          $chartData = [
                 "Total recettes cours" => array_sum(array_map(fn($item) => (int)$item["Total recettes cours"] ?? 0, $data)),
                 "Révenue total" => array_sum(array_map(fn($item) => (int)$item["Révenue total"] ?? 0, $data)),
                 "Total recettes supports de cours" => array_sum(array_map(fn($item) => (int)$item["Total recettes supports de cours"] ?? 0, $data))
          ];

          return $chartData;
   }


}
