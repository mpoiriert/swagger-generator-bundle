<?php

namespace Draw\SwaggerGeneratorBundle\Tests\Generator;

use Draw\Bundle\DrawTestHelperBundle\Helper\ServiceTestCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GenerateCommandTest extends WebTestCase
{
    use ServiceTestCaseTrait;

    public function test()
    {
        static::commandHelper('dsg:generate')->execute(
            [
                'path' => __DIR__.'/../fixtures/output',
                '--template' => 'angular-api',
                '--swagger-url' => 'http://petstore.swagger.io/v2/swagger.json',
            ]
        );
    }
}