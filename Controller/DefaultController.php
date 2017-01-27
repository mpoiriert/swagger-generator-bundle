<?php

namespace Draw\SwaggerGeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $generator = $this->get("draw_swagger_generator");
        $swagger = $this->get("draw_swagger");
        $schema = $swagger->construct($this->get("draw_swagger.schema.swagger"));
        $generator->generate($schema, __DIR__ . 'temp', 'default');
    }
}
