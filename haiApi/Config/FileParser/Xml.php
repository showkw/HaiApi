<?php

namespace Hai\Config\FileParser;

use Hai\Exception\ConfigException;


class Xml implements FileParserInterface
{

    public function parse($path)
    {
        libxml_use_internal_errors(true);

        $data = simplexml_load_file($path, null, LIBXML_NOERROR);

        if ($data === false) {
            $errors      = libxml_get_errors();
            $latestError = array_pop($errors);
            $error       = array(
                'message' => $latestError->message,
                'type'    => $latestError->level,
                'code'    => $latestError->code,
                'file'    => $latestError->file,
                'line'    => $latestError->line,
            );
            throw new ConfigException($error);
        }

        $data = json_decode(json_encode($data), true);

        return $data;
    }


    public function getSupportedExtensions()
    {
        return array('xml');
    }
}
