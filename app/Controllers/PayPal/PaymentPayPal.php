<?php
namespace App\Controllers\PayPal;
use App\Controllers\BaseController;
use Exception;

class PaymentPayPal extends BaseController{
    private $clientId="Afo1X67mlmIXe8BESOACYnXCPypLkyTTZRXqqc5JGUV6n9YgOdH6evg7308GpycVaXJdIWbh0VdWD1l6"; //esta y la de abajo se sacan del entorno de desarrollo de paypal developer
    private $secret="EDUpupIQ8s3Dkx1z3M2ZKOKaNdY3_ZsaRimh7PjdV_YRTWNLU4J4-LTPZ7g83RSX2yWZOOFgDZTdh7yU";
    private $baseurl="https://api-m.sandbox.paypal.com"; //url de testing de paypal en sanbox

    public function index()
    {
        echo view("shopping/paypal",[
            "client_id" => $this->clientId,
            "secret" => $this->secret,
        ]);
    }
    public function process($id=null) //usando el id del pedido, procesamos ese mismo
    {
        try {
            $token=$this->getAccessToken();
            $curl=curl_init($this->baseurl."/v2/checkout/orders/$id/capture"); //nicializa la peticion curl en esta url
            curl_setopt_array($curl,array( //metodo y seguridad
                CURLOPT_CUSTOMREQUEST=>"POST",
                CURLOPT_HTTPHEADER=>array(
                    "Authorization: Bearer ".$token,
                    "Content-Type: application/json"
                )));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,1); //para que no devuelva la pagina entera como json
            $res=json_decode(curl_exec($curl)); //convertimos a json la respuesta
            curl_close($curl);
            if ($res!=null) {
                if ($res->status=="COMPLETED") {
                    return $this->response->setJSON(array("msj"=>"Orden procesada")); //creamos un json para que se haga un fetch con js y deolverselo al cliente
                }    
            }
            return $this->response->setJSON(array("msj"=>"Orden  no procesada")); 
          

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function getAccessToken(){ //obtenemos el token para procesar el pedido
        try {
            $client=\Config\Services::curlrequest();
            $response=$client->request("POST", $this->baseurl."/v1/oauth2/token",[
                "auth"=>[$this->clientId,$this->secret],
                "form_params"=>[
                    "grant_type" => "client_credentials"
                ]
            ]);
            $res=json_decode($response->getBody());
            return $res->access_token;

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}


?>