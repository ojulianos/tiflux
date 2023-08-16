<?php


use Juliano\Tiflux\Trait\FileTrait;
use Juliano\Tiflux\TiFlux;

require_once __DIR__ . '/vendor/autoload.php';

class Api
{
    
    use FileTrait;

    private TiFlux $tiflux;

    public function __construct()
    {
        $this->tiflux = new TiFlux(
            'usuario',
            'senha'
        );
    }


    /**
     * Retorna os dados do json salvo no servidor
     *
     * @return void
     */
    public function getData()
    {
        try {
            echo $this->getJsonData($_REQUEST['json']);
        } catch (\Throwable $th) {
            echo $th->getMessage() . PHP_EOL;
        }
    }

    /**
     * Gera arquivo JSON com os trickets do TiFluxconso
     *
     * @return void
     */
    public function saveData()
    {
        try {
            if (!is_cli()) {
                throw new Exception('Esse programa não pode ser executado no navegador');
            }

            $offset = 0;
            $json_data = [];

            echo "Inicio Processo" . PHP_EOL;
            while ($tickets = $this->tiflux->getTickets($offset)) {
                foreach ($tickets as $ticket) {
                    if (isset($ticket['entities']) && count($ticket['entities']) > 0) {
                        $entities = $ticket['entities'][0]['fields'];
                        foreach($entities as $field) {
                            $snakeCase = toSnakeCase($field['field_name']);
                            $ticket[$snakeCase]['name'] = $field['field_name'];
                            $ticket[$snakeCase]['value'] = $field['options'][0]['option_name'];
                        }
                    }
                    $json_data[] = $ticket;
                }
                $offset++;
                echo "Etapa {$offset}" . PHP_EOL;
                sleep(1);
            }
            
            $this->saveFileData('tiflux', $json_data);

            echo "Fim Processo" . PHP_EOL;
        } catch (\Throwable $th) {
            echo $th->getMessage() . PHP_EOL;
            echo 'Erro no Processo' . PHP_EOL;
        }
    }
}

$page = is_cli() ? $argv[1] : (isset($_GET['page']) ? $_GET['page'] : 'index');
$api = new Api;
if (method_exists($api, $page)) {
    return $api->$page();
}
