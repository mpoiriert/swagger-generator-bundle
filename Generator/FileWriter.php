<?php

namespace Draw\SwaggerGeneratorBundle\Generator;

/**
 * @author Martin Poirier Theoret <mpoiriert@gmail.com>
 */
class FileWriter 
{
    const FILE_STRATEGY_APPEND = 'append';
    const FILE_STRATEGY_INDIVIDUAL = 'individual';

    private $renderedFiles = array();

    public function reset()
    {
        $this->renderedFiles = array();
    }

    public function writeFile($fileContent, $fileName, $strategy, $overwrite)
    {
        $first = !in_array($fileName, $this->renderedFiles);

        switch(true) {
            case !file_exists($fileName):
            case $overwrite:
            case $strategy == self::FILE_STRATEGY_APPEND:
                break;
            default:
                return;
        }

        if($first && file_exists($fileName)) {
            unlink($fileName);
        }

        $dirName = dirname($fileName);
        if(!is_dir($dirName)) {
            mkdir($dirName,0777, true);
        }

        if($strategy == self::FILE_STRATEGY_APPEND && file_exists($fileName)) {
            $fileContent = file_get_contents($fileName) . $fileContent;
        }

        file_put_contents($fileName, $fileContent);
        $this->renderedFiles[] = $fileName;
    }
} 