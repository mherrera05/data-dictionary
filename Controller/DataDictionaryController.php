<?php

namespace DataDictionaryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class DataDictionaryController extends Controller
{
	private $files = array();
    public function showAction()
    {
    	$bundles = $this->get('kernel')->getBundles();

    	foreach ($bundles as $bundle) {
	        $destPath = $bundle->getPath();
	        $destPath .= '/Resources/config/dictionary';

	        if(is_dir($destPath)){
	        	$this->getJsonFile($destPath);
	        }
    		
    	}

        return $this->render('DataDictionaryBundle:DataDictionary:show.html.twig', array('tables'=>$this->files));
    }

    public function getJsonFile($path)
    {
    	$files = scandir($path); 

    	foreach ($files as $file) {
    		if($file !== '.' && $file !== '..'){
    			$name = explode(".", $file);
    			if($name[2] === 'json'){
    				$this->files[] = $name[0];
    			}
    		}
    	}
    }
}
