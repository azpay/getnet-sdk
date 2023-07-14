### GETNET SDK PHP - API v1
E-commerce

Todos os passos e processos referentes à integração com o sistema de captura e autorização de transações financeiras da Getnet via as funcionalidades da API.

 Documentação oficial
* https://api.getnet.com.br/v1/doc/api

#### Composer
```
$ composer require "brunopazz/getnet-sdk @dev"
```
#### Exemplo Autorização com cartão de crédito MasterCard R$10,00 em 2x 

```php
// Autenticação da API (client_id, client_secret, env)
$getnet = new Getnet("c076e924-a3fe-492d-a41f-1f8de48fb4b1", "bc097a2f-28e0-43ce-be92-d846253ba748", "SANDBOX");

// Inicia uma transação
$transaction = new Transaction();

// Dados do pedido - Transação
$transaction->setSellerId("1955a180-2fa5-4b65-8790-2ba4182a94cb");
$transaction->setCurrency("BRL");
$transaction->setAmount("1000");

// Gera token do cartão - Obrigatório
$card = new Token("5155901222280001", "customer_21081826", $getnet);

// Dados do método de pagamento do comprador
$transaction->Credit("")
    ->setAuthenticated(false)
    ->setDynamicMcc("1799")
    ->setSoftDescriptor("LOJA*TESTE*COMPRA-123")
    ->setDelayed(false)
    ->setPreAuthorization(true)
    ->setNumberInstallments("2")
    ->setSaveCardData(false)
    ->setTransactionType("FULL")
    ->Card($card) 
        ->setBrand("MasterCard")
        ->setExpirationMonth("12")
        ->setExpirationYear("20")
        ->setCardholderName("Bruno Paz")
        ->setSecurityCode("123");
// Dados pessoais do comprador
$transaction->Customer("customer_21081826")
    ->setDocumentType("CPF")
    ->setEmail("customer@email.com.br")
    ->setFirstName("Bruno")
    ->setLastName("Paz")
    ->setName("Bruno Paz")
    ->setPhoneNumber("5551999887766")
    ->setDocumentNumber("12345678912")
    ->BillingAddress("90230060")
        ->setCity("São Paulo")
        ->setComplement("Sala 1")
        ->setCountry("Brasil")
        ->setDistrict("Centro")
        ->setNumber("1000")
        ->setPostalCode("90230060")
        ->setState("SP")
        ->setStreet("Av. Brasil");
// Dados de entrega do pedido
$transaction->Shippings("")
    ->setEmail("customer@email.com.br")
    ->setFirstName("João")
    ->setName("João da Silva")
    ->setPhoneNumber("5551999887766")
    ->ShippingAddress("90230060")
        ->setCity("Porto Alegre")
        ->setComplement("Sala 1")
        ->setCountry("Brasil")
        ->setDistrict("São Geraldo")
        ->setNumber("1000")
        ->setPostalCode("90230060")
        ->setState("RS")
        ->setStreet("Av. Brasil");
// Detalhes do Pedido
$transaction->Order("123456")
    ->setProductType("service")
    ->setSalesTax("0");
$transaction->setSellerId("1955a180-2fa5-4b65-8790-2ba4182a94cb");
$transaction->setCurrency("BRL");
$transaction->setAmount("1000");

// FingerPrint - Antifraude
$transaction->Device("hash-device-id")->setIpAddress("127.0.0.1");

// Processa a Transação
$response = $getnet->Authorize($transaction);

// Resultado da transação - Consultar tabela abaixo
$response->getStatus();
```

#### CONFIRMA PAGAMENTO (CAPTURA)
```php
// Autenticação da API (client_id, client_secret, env)
$getnet = new Getnet("c076e924-a3fe-492d-a41f-1f8de48fb4b1", "bc097a2f-28e0-43ce-be92-d846253ba748", "SANDBOX");

// Processa a confirmação da autorização
$capture = $getnet->AuthorizeConfirm("PAYMENT_ID");

// Resultado da transação - Consultar tabela abaixo
$capture->getStatus();
```

#### CANCELA PAGAMENTO (CRÉDITO e DÉBITO)
```php
// Autenticação da API (client_id, client_secret, env)
$getnet = new Getnet("c076e924-a3fe-492d-a41f-1f8de48fb4b1", "bc097a2f-28e0-43ce-be92-d846253ba748", "SANDBOX");

$cancel = $getnet->AuthorizeCancel("[PAYMENT_ID]", [AMOUNT]);

// Resultado da transação - Consultar tabela abaixo
$cancel->getStatus();
```

#### BOLETO BANCÁRIO (SANTANDER)

```php
$getnet = new Getnet("c076e924-a3fe-492d-a41f-1f8de48fb4b1", "bc097a2f-28e0-43ce-be92-d846253ba748", "SANDBOX");
$transaction = new Transaction();
$transaction->setSellerId("1955a180-2fa5-4b65-8790-2ba4182a94cb");
$transaction->setCurrency("BRL");
$transaction->setAmount("1000");

$transaction->Boleto("000001946598")
    ->setDocumentNumber("170500000019763")
    ->setExpirationDate("21/11/2018")
    ->setProvider("santander")
    ->setInstructions("Não receber após o vencimento");

$transaction->Customer()
    ->setDocumentType("CPF")
    ->setFirstName("Bruno")
    ->setName("Bruno Paz")
    ->setDocumentNumber("12345678912")
    ->BillingAddress("90230060")
    ->setCity("São Paulo")
    ->setComplement("Sala 1")
    ->setCountry("Brasil")
    ->setDistrict("Centro")
    ->setNumber("1000")
    ->setPostalCode("90230060")
    ->setState("SP")
    ->setStreet("Av. Brasil");

$transaction->Order("123456")
    ->setProductType("service")
    ->setSalesTax("0");

$response = $getnet->Boleto($transaction);

// Resultado da transação - Consultar tabela abaixo
$response->getStatus();
```

### Possíveis status de resposta de uma transação
|Status|Descrição|
| ------- | --------- |
|PENDING|Registrada ou Aguardando ação|
|CANCELED|Desfeita ou Cancelada|
|APPROVED|Aprovada|
|DENIED|Negada|
|AUTHORIZED|Autorizada pelo emissor|
|CONFIRMED|Confirmada ou Capturada|

### Cartões para testes

|  N. Cartão |  Resultado esperado |
| ------------ | ------------ |
|  5155901222280001 (Master)	  | Transação Autorizada  |
| 5155901222270002   (Master)|  Transação Não Autorizada |
|  5155901222260003 (Master) |  Transação Não Autorizada |
| 5155901222250004 (Master) |Transação Não Autorizada|
| 4012001037141112 (Visa) |Transação Autorizada|


### Ambientes disponíveis
|Paramentro|Detalhe|
| ------- | --------- |
|SANDBOX|Sandbox - para desenvolvedores |
|HOMOLOG|Homologação - para lojistas e devs |
|PRODUCTION|Produção - somente lojistas |

### Meios de Pagamento
|Modalidade|Descrição|
| ------- | --------- |
|CREDIT|Pagamento com cartão de crédito|
|DEBIT|Pagamento com cartão de débito|
|BOLETO|Gera boleto|


### Métodos de Pagamento
|Método|Descrição|
| ------- | --------- |
|Authorize|Autoriza uma transação com Pre-Auth ou não|
|AuthorizeConfirm|Confirma uma autorização de crédito|
|AuthorizeConfirmDebit|Confirma uma autorização de débito|
|AuthorizeCancel|Cancela a transação|
|Boleto|Gera boleto|


