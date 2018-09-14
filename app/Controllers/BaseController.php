<?php

namespace App\Controllers;

use \Twig_Loader_FileSystem;
use \Twig_Environment;
use Zend\Diactoros\Response\HtmlResponse;

class BaseController
{

    protected $templateEngine;

    public function __construct()
    {
        // crear objeto con la ruta de las vistas
        $loader = new Twig_Loader_FileSystem('../views');

        //se manda a llamar la variable protected para crear un objeto de entorno y cargar twig
        $this->templateEngine = new Twig_Environment($loader, array(
            'debug' => true,
            'cache' => false,
        ));
    }

    //function render para renderizar el archivo html con los datos a enviar, en caso que no se envie
    //nada, tomara el valor por default
    public function renderHTML($file, $data = [])
    {
        return new HtmlResponse($this->templateEngine->render($file, $data));
    }
}