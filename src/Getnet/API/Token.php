<?php
/**
 * Created by PhpStorm.
 * User: brunopaz
 * Date: 09/07/2018
 * Time: 14:03
 */

namespace Getnet\API;


/**
 * Class Token
 * @package Getnet\API
 */
class Token
{
    /**
     * @var
     */
    public $number_token;
    /**
     * @var
     */
    private $card_number;
    /**
     * @var
     */
    private $customer_id;


    /**
     * Token constructor.
     * @param $card_number
     * @param $customer_id
     * @param Getnet $credencial
     */
    public function __construct($card_number, $customer_id, Getnet $credencial)
    {
        $this->card_number = $card_number;
        $this->customer_id = $customer_id;
        $this->setNumberToken($credencial);

        return $this;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->number_token;
    }

    /**
     * @return mixed
     */
    public function getCardNumber()
    {
        return $this->card_number;
    }

    /**
     * @param mixed $card_number
     * @return Token
     */
    public function setCardNumber($card_number)
    {
        $this->card_number = (string)$card_number;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * @param mixed $customer_id
     * @return Token
     */
    public function setCustomerId($customer_id)
    {
        $this->customer_id = (string)$customer_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumberToken()
    {
        return $this->number_token;
    }


    public function setNumberToken(Getnet $credencial)
    {
        $data = array("card_number" => $this->card_number, "customer_id" => $this->customer_id);

        $request = new Request($credencial);
        try{
            $response = $request->post($credencial, "/v1/tokens/card", json_encode($data));
        }catch (\Exception $exception){
            return false;
        }

        $this->number_token = $response["number_token"];

        return $this;
    }
}
