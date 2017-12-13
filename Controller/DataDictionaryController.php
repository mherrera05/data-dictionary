<?php

namespace DataDictionaryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\HttpKernel;

class DataDictionaryController extends Controller
{
	private $files = array();

    public function showAction()
    {
        $kernel = $this->get('kernel');
    	$bundles = $kernel->getBundles();
        $array = array();
        $tablesArray = array();

    	foreach ($bundles as $bundle) {
	        $destPath = $bundle->getPath();
	        $destPath .= '/Resources/config/dictionary';

	        if(is_dir($destPath)){
	        	$this->getJsonFile($destPath);

                foreach ($this->files as $file) {
                    $content = file_get_contents($destPath.'/'.$file.'.orm.json');
                    $content = json_decode($content, true);
                    $array[] = $content;
                    $tablesArray[] = array('name'=>$file, 'table'=>$content['table']);
                }
	        }
    	}

        return $this->render('@DataDictionaryBundle/DataDictionary/show.html.twig', array('tables'=>$tablesArray, 'content'=>$array));
    }

    protected function getJsonFile($path)
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
