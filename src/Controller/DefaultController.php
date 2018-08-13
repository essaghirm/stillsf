<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function index()
    {
        $number = random_int(0, 100);

        $array = [
        	[
        		'fname' => 'Mouhcine',
        		'lname' => 'Essaghir',
        		'age' => '27'
        	],
        	[
        		'fname' => 'Khalid',
        		'lname' => 'Bekrim',
        		'age' => '29'
        	],
        	[
        		'fname' => 'Anouar',
        		'lname' => 'Harchi',
        		'age' => '21'
        	],
        	[
        		'fname' => 'Samira',
        		'lname' => 'fathi',
        		'age' => '25'
        	]
        ];

  //       $object = new stdClass();
  //       foreach ($array as $key => $value)
		// {
		//     $object->$key = $value;
		// }

        $json =  json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        // var_dump($json);
        // die();
        $response = new Response($json);
    	$response->headers->set('Content-Type', 'application/json');
    	return $response;
    }
}