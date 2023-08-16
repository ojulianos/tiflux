<?php

namespace Juliano\Tiflux\Trait;

trait FileTrait
{
    private function saveFileData(string $file_name, array $json_data)
    {
        $json_file = "./{$file_name}_data.json";

        file_exists($json_file) ? unlink($json_file) : null;

        $file = fopen($json_file, 'w');
        fwrite($file, json_encode($json_data));

        fclose($file);
    }

    /**
     * Retorna os dados do json salvo no servidor
     *
     * @return void
     */
    private function getJsonData(string $filename)
    {
        header('Content-type: application/json; charset=utf-8');
    
        $file = filter_var($filename, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (empty($file)) {
            throw new Exception('É obrigatório informar o parametro json');
        }

        $json_file = "./{$file}_data.json";
        if (!file_exists($json_file)) {
            throw new Exception('Arquivo JSON não localizado!');
        }

        $json_data = file_get_contents($json_file);
        return $json_data;
    }
}
