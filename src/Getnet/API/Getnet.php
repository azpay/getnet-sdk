<?php
namespace Getnet\API;
    /**
     * Created by PhpStorm.
     * User: brunopaz
     * Date: 09/07/2018
     * Time: 01:26
     * Documentation https://api.getnet.com.br/v1/doc/api
     */

/**
 * Class Getnet
 * @package Getnet\API
 */
class Getnet
{
    /**
     * @var bool
     */
    public $debug = false;
    /**
     * @var Request
     */
    private $client_id;
    /**
     * @var
     */
    private $client_secret;
    /**
     * @var
     */
    private $env;
    /**
     * @var
     */
    private $authorizationToken;

    /**
     * Getnet constructor.
     * @param $client_id
     * @param $client_secret
     * @param $env
     */

    public function __construct($client_id, $client_secret, $env)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->env = $env;

        $request = new Request($this);

        try{
            return $request->auth($this);
        }catch (\Exception $exception){
            return false;
        }

    }

    /**
     * @param boolean $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @return $this
     */
    public function getAuthorizationToken()
    {
        return $this->authorizationToken;
    }

    /**
     * @param $this $authorizationToken
     */
    public function setAuthorizationToken($authorizationToken)
    {
        $this->authorizationToken = $authorizationToken;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * @param mixed $client_id
     */
    public function setClientId($client_id)
    {
        $this->client_id = $client_id;
    }

    /**
     * @return mixed
     */
    public function getClientSecret()
    {
        return $this->client_secret;
    }

    /**
     * @param mixed $client_secret
     */
    public function setClientSecret($client_secret)
    {
        $this->client_secret = $client_secret;
    }

    /**
     * @return mixed
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param mixed $env
     */
    public function setEnv($env)
    {
        $this->env = $env;
    }


    /**
     * @param Transaction $transaction
     * @return AuthorizeResponse
     */
    public function Authorize(Transaction $transaction)
    {
        try {

            $request = new Request($this);

            if (property_exists($transaction, "debit")) {
                $response = $request->post($this, "/v1/payments/debit", $transaction->toJSON());
            } elseif (property_exists($transaction, "credit")) {
                $response = $request->post($this, "/v1/payments/credit", $transaction->toJSON());
            }
            if ($this->debug)
                print $transaction->toJSON();
        } catch (\Exception $e) {

            $error = new BaseResponse();
            $error->mapperJson(json_decode($e->getMessage(), true));

            return $error;
        }
        $authresponse = new AuthorizeResponse();
        $authresponse->mapperJson($response);

        return $authresponse;
    }

    /**
     * @param $payment_id
     * @return AuthorizeResponse|BaseResponse
     */
    public function AuthorizeConfirm($payment_id)
    {
        try {
            $request = new Request($this);
            $response = $request->post($this, "/v1/payments/credit/" . $payment_id . "/confirm", "");
        } catch (\Exception $e) {

            $error = new BaseResponse();
            $error->mapperJson(json_decode($e->getMessage(), true));

            return $error;
        }
        $authresponse = new AuthorizeResponse();
        $authresponse->mapperJson($response);

        return $authresponse;
    }

    public function AuthorizeConfirmDebit($payment_id, $payer_authentication_response)
    {
        try {
            $payer_authentication_response = array("payer_authentication_response" => $payer_authentication_response);
            $request = new Request($this);
            $response = $request->post($this, "/v1/payments/debit/" . $payment_id . "/authenticated/finalize", json_encode($payer_authentication_response));
        } catch (\Exception $e) {

            $error = new BaseResponse();
            $error->mapperJson(json_decode($e->getMessage(), true));

            return $error;
        }
        $authresponse = new AuthorizeResponse();
        $authresponse->mapperJson($response);

        return $authresponse;
    }

    /**
     * @param $payment_id
     * @param $amount_val
     * @return AuthorizeResponse|BaseResponse
     */
    public function AuthorizeCancel($payment_id, $amount_val)
    {
        $amount = array("amount" => $amount_val);

        try {
            $request = new Request($this);
            $response = $request->post($this, "/v1/payments/credit/" . $payment_id . "/cancel", json_encode($amount));

        } catch (\Exception $e) {

            $error = new BaseResponse();
            $error->mapperJson(json_decode($e->getMessage(), true));

            return $error;
        }
        $authresponse = new AuthorizeResponse();
        $authresponse->mapperJson($response);

        return $authresponse;
    }

    /**
     * @param Transaction $transaction
     * @return BaseResponse|BoletoRespose
     */
    public function Boleto(Transaction $transaction)
    {
        try {
            $request = new Request($this);
            $response = $request->post($this, "/v1/payments/boleto", $transaction->toJSON());
            if ($this->debug)
                print $transaction->toJSON();
        } catch (\Exception $e) {

            $error = new BaseResponse();
            $error->mapperJson(json_decode($e->getMessage(), true));

            return $error;
        }
        $boletoresponse = new BoletoRespose();
        $boletoresponse->mapperJson($response);
        $boletoresponse->setBaseUrl($request->getBaseUrl());

        return $boletoresponse;
    }
    
     /**
     * @param $org_id
     * @param $session_id
     *
     * @return bool|string
     * Capturando Device Fingerprint em aplicação/serviço Backend:
     * Em arquiteturas que o transação é realizada atraves de uma aplicação backend, é recomendado carregar a URL de captura no servidor onde o pagamento será processado (antes de enviar a transação).
     *
     * Para isso, é recomendado através do comando "CURL" fazer uma chamada GET na URL de Captura conforme exemplo abaixo:
     */
    public function SessionId($org_id, $session_id)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://h.online-metrix.net/fp/tags.js?org_id=" . $org_id . "&session_id=" . $session_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.54.0');
        $output = curl_exec($ch);
        curl_close($ch);
        return $session_id;
    }
}

