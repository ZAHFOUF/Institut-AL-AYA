<?php

namespace AlAya\Common\Api;

use AlAya\Common\Controller\BaseController;
use AlAya\Common\Service\AuthChecker;
use AlAya\Common\Service\Export;
use AlAya\Common\Service\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin',name: 'api_')]
class ApiController extends BaseController
{

    #[Route('/delete/{entity?}/{id?}', name: 'delete' )]
    public function delete($entity,$id,Request $request,AuthChecker $authChecker)
    {

     //   $authChecker->denyIfNoPermission(concat("delete","_",lcfirst($entity)),$this->getUser());

        if (is_null($entity)) {
           $entity = $request->query->get('entity');
        }

        if (is_null($id)) {
              $id = $request->query->get('id');
        }
        
       $record =  $this->doctrine->getRepository(getEntityClass($entity))->find($id);

       if (property_exists($record,"deleted")) {
           $record->softDelete();
           $this->doctrine->persist($record);
       }else{
            $this->doctrine->remove($record);
       }

       $this->doctrine->flush() ;
       return $this->redirect($request->headers->get("referer"));

    }

    #[Route('/export/{entity}/{action}/{format}', name: 'export' )]
    public function export($entity,$action,$format,Request $request,Export $exporter,AuthChecker $authChecker)
    {

        $authChecker->denyIfNoPermission(concat("export","_",lcfirst($entity)),$this->getUser());

        $repo = $this->doctrine->getRepository(getEntityClass($entity));
        $data = $repo->{$action}($request->query);

        if (!$this->isCsrfTokenValid($entity,$request->query->get("token"))) {
            throw new NotFoundHttpException("Not found !");
        }

        try {
            if ($request->query->has("excludeExport") and is_array(explode(",",$request->query->get('excludeExport'))) ) {
                $exclude = explode(",",$request->query->get('excludeExport'));
                $filtredData = [];
                
                    foreach ($data as $item) {
                        foreach ($exclude as $ex) {
                            unset($item[$ex]);
                       }
                        $filtredData[] = $item;
                    }
                $data = $filtredData;
            }
        } catch (\Throwable $th) {
        }
        
        return $exporter->{"to".ucfirst($format)}($data,$entity,$action);
    }

    #[Route('/download/{file}', name: 'download' )]
    public function download($file,FileManager $fileManager)
    {
       
    }


    #[Route('/depend', name: 'depend' )]
    public function depend(Request $request)
    {
          $dependEntity = $request->query->get('dependEntity');
          $dependField = $request->query->get('dependField');
          $dependValue = $request->query->get('dependValue');
          $dependName = $request->query->get('dependName');
          $data = $this->doctrine->getRepository(getEntityClass($dependEntity))->findBy([ $dependField => $dependValue]);
          $mappedData = [];
            foreach ($data as $item) {
                $mappedData[] = [
                    'id' => $item->getId(),
                    'name' => $item->{"get".ucfirst($dependName)}()
                ];
            }
          return $this->json($mappedData,200);
    }



    #[Route('/generate-url', name: 'generate_url' )]
    public function generateUrlApi(Request $request)
    {
        $url = $this->generateUrl($request->query->get('route'),$request->query->all("params") ?? [], 0);
        return $this->json($url,200);
    }

}
