<?php

namespace Drupal\cars\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class CarsController extends ControllerBase
{
    /**
     * @return Response
     */
    public function index()
    {
        
        return new Response('it works!');
    }

    public function content()
    {
        return [
            '#type' => 'markup',
            '#markup' => t('this is my custom linked page')
        ];
    }
}