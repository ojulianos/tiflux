<?php

namespace Juliano\Tiflux\Http;

/**
 * Base para requisições na API TiFlux
 */
class Base
{
    const API_URL = 'https://api.tiflux.com/api/v1/';
    private string $_USER = '';
    private string $_PASWORD = '';
    
    private string $token;
    private $paramBinary;

    public function __construct(
        string $user = '',
        string $password = ''
    )
    {
        $this->generateToken($user, $password);
    }

    protected function get(string $endpoint, string $params = '')
    {
        $this->paramBinary = $params;
        return $this->http($endpoint);
    }

    protected function post(string $endpoint, array $params)
    {
        $params['method'] = 'post';
        return $this->http($endpoint, $params);
    }

    protected function put(string $endpoint, array $params)
    {
        $params['method'] = 'put';
        return $this->http($endpoint, $params);
    }

    protected function delete(string $endpoint)
    {
        $params['method'] = 'delete';
        return $this->http($endpoint, $params);
    }

    /**
     * Inicia a requisição http
     *
     * @param string $endpoint
     * @param array $postfields
     * @return void
     */
    private function http(string $endpoint, array $postfields = [])
    {
        if (empty($endpoint)) {
            throw new Exception("O endpoint deve ser informado!");
        }

        if (!is_array($postfields)) {
            throw new Exception("Os parametros devem ser enviados como array!");
        }

        $ch = curl_init();
        curl_setopt_array($ch, $this->setPostFields($endpoint, $postfields));
        
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        if ($info['http_code'] != 200) {
            throw new Exception($response);
        }
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Gera o token de autenticação
     *
     * @param string $user
     * @param string $password
     * @return void
     */
    private function generateToken(string $user, string $password)
    {
        if($user && $password) {
            $this->_USER    = $user;
            $this->_PASWORD = $password;
        }
        
        $this->token = base64_encode("$this->_USER:$this->_PASWORD");
    }


    /**
     * Define os parametros enviados via cURL
     *
     * @param string $endpoint
     * @param array $postfields
     * @return array
     */
    private function setPostFields(string $endpoint, array $postfields): array
    {
        $options = [
            CURLOPT_URL => self::API_URL . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic {$this->token}"
            ),
        ];

        if(isset($postfields['method']) && in_array($postfields['method'], ['post', 'put'])) {
            $options[CURLOPT_POST] = TRUE;
            $options[CURLOPT_POSTFIELDS] = json_encode($postfields);
        } else {
            if (isset($postfields['method'])) {
                $options[CURLOPT_CUSTOMREQUEST] = mb_strtoupper($postfields['method']);
            } else {
                $options[CURLOPT_CUSTOMREQUEST] = 'GET';
            }
        }

        if (!empty($this->paramBinary)) {
            $options[CURLOPT_POSTFIELDS] = $this->paramBinary;
        }

        return $options;
    }
}

