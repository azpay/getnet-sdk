<?php
namespace Getnet\API;
/**
 * Class Debit
 * @package Getnet\API
 */
class Debit implements \JsonSerializable
{
    /**
     * @var
     */
    private $dynamic_mcc;
    /**
     * @var
     */
    private $soft_descriptor;
    /**
     * @var
     */
    private $card;

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * @param mixed $card
     */
    public function setCard($card)
    {
        $this->card = $card;
    }

    /**
     * @param $card
     * @return Card
     */
    public function Card($card)
    {
        $card = new Card($card);
        $this->card = $card;

        return $card;
    }

    /**
     * @return mixed
     */
    public function getDynamicMcc()
    {
        return $this->dynamic_mcc;
    }

    /**
     * @param mixed $dynamic_mcc
     * @return Debit
     */
    public function setDynamicMcc($dynamic_mcc)
    {
        $this->dynamic_mcc = $dynamic_mcc;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSoftDescriptor()
    {
        return $this->soft_descriptor;
    }

    /**
     * @param mixed $soft_descriptor
     * @return Debit
     */
    public function setSoftDescriptor($soft_descriptor)
    {
        $this->soft_descriptor = $soft_descriptor;

        return $this;
    }


}