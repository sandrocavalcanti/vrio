<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
//

/*
 * ***********************************************************************
 Copyright [2011] [PagSeguro Internet Ltda.]

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
 * ***********************************************************************
 */

require_once "../class/PagSeguroLibrary/PagSeguroLibrary.php";
require_once "conexao.php";

/**
 * Class with a main method to illustrate the usage of the domain class PagSeguroPaymentRequest
 */
class CreatePaymentRequest
{

    public static function main()
    {
        
        print json_encode('0'); exit;
        // Instantiate a new payment request
        $paymentRequest = new PagSeguroPaymentRequest();
        
        //salvando a venda
        mysql_query("INSERT INTO tb_venda VALUES (NULL, 0, NOW(), ".$_POST['id_customer'].", NULL, 1, NULL, NOW())")or die(mysql_error());
        $id_venda = mysql_insert_id();

        // Sets the currency
        $paymentRequest->setCurrency("BRL");

        $valor_total = 0;

        //salvando os produtos
        if(count($_POST['produto_id']) > 0){
            for($i = 0; $i < count($_POST['produto_id']); $i++){

                if($_POST['qtde'][$i] > 0){
                    //recuperando info do produto
                    $query = mysql_query("SELECT * FROM tb_produto WHERE id = ".$_POST['produto_id'][$i])or die(mysql_error());
                    $rows = mysql_fetch_assoc($query);

                    //salvando no banco os itens da venda
                    mysql_query("INSERT INTO tb_venda_produto VALUES (NULL, ".$_POST['qtde'][$i].", ".$rows['valor'].", 
                                ".$rows['id'].", ".$id_venda.", 0, NULL)")or die(mysql_error());
                    //$id_venda = mysql_insert_id();

                    // Add an item for this payment request
                    $paymentRequest->addItem($rows['id'], $rows['descricao'], $_POST['qtde'][$i], $rows['valor']);  

                    $valor_total +=  $_POST['qtde'][$i] * $rows['valor'];
                }

            }
        }
        
        mysql_query("UPDATE tb_venda SET valor_total = ".$valor_total." WHERE id = ".$id_venda)or die(mysql_error());

        // Sets a reference code for this payment request, it is useful to identify this payment
        // in future notifications.
        $paymentRequest->setReference($id_venda);

        // Sets shipping information for this payment request
        // $sedexCode = PagSeguroShippingType::getCodeByType('SEDEX');
        // $paymentRequest->setShippingType($sedexCode);
        // $paymentRequest->setShippingAddress(
        //     '01452002',
        //     'Av. Brig. Faria Lima',
        //     '1384',
        //     'apto. 114',
        //     'Jardim Paulistano',
        //     'São Paulo',
        //     'SP',
        //     'BRA'
        // );

        //recuperando info do cliente
        $query = mysql_query("SELECT * FROM tb_customer WHERE id = ".$_POST['id_customer']);
        $row = mysql_fetch_assoc($query);

        $nbr_cpf = $row['cpf'];

        $parte_um     = substr($nbr_cpf, 0, 3);
        $parte_dois   = substr($nbr_cpf, 3, 3);
        $parte_tres   = substr($nbr_cpf, 6, 3);
        $parte_quatro = substr($nbr_cpf, 9, 2);

        $monta_cpf = "$parte_um.$parte_dois.$parte_tres-$parte_quatro";


        // Sets your customer information.
        $paymentRequest->setSender(
            $row['nome'].' '.$row['sobrenome'],
            $row['email'],
            '81',
            substr($row['celular'],2),
            'CPF',
            $monta_cpf
        );

        mysql_close();

        // Sets the url used by PagSeguro for redirect user after ends checkout process
        //$paymentRequest->setRedirectUrl("http://www.banheirosvrio.com.br/site/public/compra/sucesso.php");

        // Add checkout metadata information
        // $paymentRequest->addMetadata('PASSENGER_CPF', '15600944276', 1);
        // $paymentRequest->addMetadata('GAME_NAME', 'DOTA');
        // $paymentRequest->addMetadata('PASSENGER_PASSPORT', '23456', 1);

        // Another way to set checkout parameters
        $paymentRequest->addParameter('notificationURL', 'http://www.banheirosvrio.com.br/site/public/compra/NotificationListener.php');
        // $paymentRequest->addParameter('senderBornDate', '07/05/1981');
        // $paymentRequest->addIndexedParameter('itemId', '0003', 3);
        // $paymentRequest->addIndexedParameter('itemDescription', 'Notebook Preto', 3);
        // $paymentRequest->addIndexedParameter('itemQuantity', '1', 3);
        // $paymentRequest->addIndexedParameter('itemAmount', '200.00', 3);

        try {

            /*
             * #### Credentials #####
             * Substitute the parameters below with your credentials (e-mail and token)
             * You can also get your credentials from a config file. See an example:
             * $credentials = PagSeguroConfig::getAccountCredentials();
             */
            $credentials = new PagSeguroAccountCredentials("sandro@unitybrasil.com.br",
                "657F4FAD9A1543F38DB9B7900539A9E6");
            $credentials = PagSeguroConfig::getAccountCredentials();
            // Register this payment request in PagSeguro, to obtain the payment URL for redirect your customer.
            $url = $paymentRequest->register($credentials);
            
            self::printPaymentUrl($url);
        } catch (PagSeguroServiceException $e) {
            print_r($e->getHttpStatus()); // imprime o código HTTP  
      
            foreach ($e->getErrors() as $key => $error) {  
                echo $error->getCode(); // imprime o código do erro  
                echo $error->getMessage(); // imprime a mensagem do erro  
            }  
        }
    }

    public static function printPaymentUrl($url)
    {
        if ($url) {
            /*echo "<h2>Criando requisi&ccedil;&atilde;o de pagamento</h2>";
            echo "<p>URL do pagamento: <strong>$url</strong></p>";
            echo "<p><a title=\"URL do pagamento\" href=\"$url\">Ir para URL do pagamento.</a></p>";*/
            print json_encode($url);
        }else{
            print 'erro';
        }
    }
}

CreatePaymentRequest::main();
