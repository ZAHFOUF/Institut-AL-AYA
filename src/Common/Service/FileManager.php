<?php

namespace AlAya\Common\Service ;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileManager 
{

    public $slugger;

    public function __construct(  SluggerInterface $slugger) {
        $this->slugger = $slugger;
    }


       /**
     * Upload and save your files into the server
     *
     * @param mixed    $file    file 
     * @param path     $path    path you want save that file (without / in  the end)
     *                          
     */

    function upload($file,$path) : string
      {

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        $file->move($path , $newFilename);

    
            return $newFilename ;

    }


       /**
     * Download a file from a path
     *
     * @param path     $path    path to the file
     *                          
     */
    

    function download($filePath) : Response
    
    {

        
        $basenameFile = basename($filePath);

        return new \Symfony\Component\HttpFoundation\Response(
            file_get_contents($filePath), 200, array(
            'Content-Type' => 'application/xml',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $basenameFile)
            )
            );
        
    }
    
}
