<?php

namespace Draw\SwaggerGeneratorBundle\Generator;

use Draw\Swagger\Schema\Swagger;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Yaml\Yaml;
use Twig_Environment;

/**
 * @author Martin Poirier Theoret <mpoiriert@gmail.com>
 */
class Generator
{
    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var FileWriter
     */
    private $fileWriter;

    /**
     * @var Registry
     */
    private $registry;

    private function defaultParameters()
    {
        return array(
          'module_name' => 'LookLike',
          'references' => array()
        );
    }

    public function __construct(Twig_Environment $twig, $templateDirectory)
    {
        $this->twig = $twig;
        $this->templateDirectory = $templateDirectory;
        $this->fileWriter = new FileWriter();
        $this->registry = new Registry();
    }

    public function setSwaggerSchema(Swagger $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @param Swagger $schema
     */
    public function generate(Swagger $schema, $path, $template)
    {
        $template = 'type-script-angular';
        $config = $this->loadConfiguration($template);

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($config['files'] as $fileName => $fileConfiguration) {
            $context = array(
              'swagger' => $schema,
              'file_name' => $fileName,
              'current_directory' => '@draw_swagger_generator/' . $template . '/' . dirname($fileName),

            );
            if (isset($fileConfiguration['with'])) {
                foreach ($fileConfiguration['with'] as $key => $configuration) {
                    if(is_string($configuration)) {
                        $context[$key] = $propertyAccessor->getValue($context, $configuration);
                    }
                }
            }

            if (!isset($fileConfiguration['for'])) {
                $this->renderToFile($path, $template, $fileName, $context, $fileConfiguration);
            } else {
                $for = $fileConfiguration['for'];
                $key = $for['key'];
                $value = $for['value'];
                $in = $for['in'];
                foreach ($propertyAccessor->getValue($context, $in) as $_key => $_value) {
                    $context[$key] = $_key;
                    $context[$value] = $_value;
                    $this->renderToFile($path, $template, $fileName, $context, $fileConfiguration);
                }
            }
        }
    }

    private function getFileName($context, $realFileName, $fileConfiguration, $currentResult)
    {
        if (isset($currentResult['current.output_file'])) {
           return $currentResult['current.output_file'];
        }

        if (isset($fileConfiguration['fileName'])) {
            $currentLoader = $this->twig->getLoader();
            $this->twig->setLoader(new \Twig_Loader_String());
            $fileName = $this->twig->render($fileConfiguration['fileName'], $context);
            $this->twig->setLoader($currentLoader);
            return $fileName;
        }

        return  substr($realFileName, 0, - strlen('.twig'));
    }

    private function renderToFile($path, $template, $fileName, $context, $fileConfiguration)
    {
        $context['parameters'] = $this->defaultParameters();

        $fileContent = $this->twig->render(
          '@draw_swagger_generator/' . $template . '/' . $fileName,
          $context
        );

        $result = $this->cleanEnvironment();

        $outputFilePath = $path . '/' . $this->getFileName($context, $fileName, $fileConfiguration, $result);

        $overwrite = !isset($fileConfiguration['overwrite']) || $fileConfiguration['overwrite'];
        $strategy = isset($fileConfiguration['fileStrategy']) ? $fileConfiguration['fileStrategy'] : FileWriter::FILE_STRATEGY_INDIVIDUAL;

        $registry = $this->getRegistry();
        if (!$registry->has('files')) {
            $registry->set('files', array());
        }

        $output = !isset($fileConfiguration['output']) ? true : $fileConfiguration['output'];
        $files = $registry->get('files');
        $files['.' . substr($outputFilePath, strlen($path))] = array(
            'path' => '.' . substr($outputFilePath, strlen($path)),
            'content' => $fileContent,
            'output' => $output,
        );

        $registry->set('files', $files);

        if (!$output) {
            return;
        }

        $this->fileWriter->writeFile($fileContent, $outputFilePath, $overwrite, $strategy);
    }

    private function cleanEnvironment()
    {
        $registry = $this->getRegistry();

        $result = array();
        foreach ($registry->getArrayCopy() as $key => $value) {
            if (strpos($key, 'current.') === 0) {
                $result[$key] = $value;
                unset($registry[$key]);
            }
        }

        return $result;
    }

    public function extractOperations(Swagger $swagger)
    {
        $result = array();
        foreach($swagger->paths as $path => $pathItem) {
            foreach($pathItem as $method => $operation) {
                $result[] = compact('path','pathItem','method','operation');
            }
        }

        return $result;
    }

    /**
     * @return Registry
     */
    private function getRegistry()
    {
        $globals = $this->twig->getGlobals();
        return $globals['dsg']['registry'];
    }

    private function loadConfiguration($template)
    {
        return Yaml::parse($this->templateDirectory . '/' . $template . '/config.yml');
    }
}