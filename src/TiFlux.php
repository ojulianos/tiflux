<?php

namespace Juliano\Tiflux;

use Juliano\Tiflux\Http\Base;

class TiFlux extends Base
{
    public function __construct(
        string $user = '',
        string $password = ''
    )
    {
        parent::__construct($user, $password);
    }

    private function cadastrarCliente()
    {
        $postfields = [
            'name' => 'Roberto e Pedro Joalheria',
            'anotations' => 'Any Client annotations here',
            'desk_ids' => [ 43, 44 ],
            'work_folder' => 'Documents',
            'estadual_registration' => '960.016.791.776',
            'municipal_registration' => '74553',
            'quarterly_billing' => 'true',
            'quarterly_bill_client_id' => '2',
            'social' => 'Roberto e Pedro Joalheria Ltda',
            'social_revenue' => '04.615.832/0001-73',
        ];

        $cliente = $this->post('clients?link_desks=&link_technical_groups=', $postfields);
        $endereco = $this->cadastrarEndereco($cliente['id']);
        $emailsTelefones = $this->cadastrarEmailsTelefones($cliente['id']);
        $solicitantes = $this->cadastrarSolicitantes($cliente['id']);
    }

    private function cadastrarEndereco($clientId)
    { 
        $postfields = [
            'cep'=> '89201-700',
            'city'=> 'Joinville',
            'complement'=> 'Casa',
            'neighborhood'=> 'América',
            'number'=> 48,
            'state'=> 'SC',
            'street'=> 'R. Otto Boehm'
        ];

        return $this->post("clients/{$clientId}/addresses", $postfields);
    }

	public function cadastrarTicket()
	{
        $cliente = $this->cadastrarCliente();

        $postfields = [
            'client_id' => $cliente['id'],
            'desk_id' => '1',
            'priority_id' => '8',
            'title' => 'Nice Ticket',
            'description' => '<p>Nice ticket, have we</p>',
            'requestor' => [
                'name' => 'Jane Doe',
                'email' => 'jane.doe@client.com',
                'telephone' => [
                    'number' => '4733333333',
                    'extension' => '333'
                ]
            ],
            'services_catalogs_item_id' => '0',
            'ticket_reference_number' => '11333',
            'number' => '11333',
        ];

        return $this->post('tickets', $postfields);
	}

    public function getTicket($id)
    {        
        return $this->get("tickets/{$id}");
    }

    public function getTickets($offset = 1)
    {
        $paramData = json_encode(["is_closed" => "true", "offset" => $offset, "limit" => "200", "include_entity" => "true"]);
        $response = $this->get("tickets", $paramData);
        if (count($response) > 0) {
            return $response;
        }
        return false;
    }

    public function getClients($phoneNumber)
    {
        return $this->get("clients?offset=1&limit=20&phone={$phoneNumber}");
    }
}