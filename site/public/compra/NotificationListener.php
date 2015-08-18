<?php
/*
 ************************************************************************
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
 ************************************************************************
 */

require_once "../class/PagSeguroLibrary/PagSeguroLibrary.php";
require_once "conexao.php";

/* Informando as credenciais  */    
$credentials = new PagSeguroAccountCredentials(      
    'sandro@unitybrasil.com.br',       
    '657F4FAD9A1543F38DB9B7900539A9E6'      
);  
  
/* Tipo de notificação recebida */  
$type = $_POST['notificationType'];  
  
/* Código da notificação recebida */  
$code = $_POST['notificationCode'];  
  
  
/* Verificando tipo de notificação recebida */  
if ($type === 'transaction') {  
      
    /* Obtendo o objeto PagSeguroTransaction a partir do código de notificação */  
    $transaction = PagSeguroNotificationService::checkTransaction(  
        $credentials,  
        $code // código de notificação  
    );  

    /* objeto PagSeguroTransactionStatus */    
    $status = $transaction->getStatus(); 

    $code = $transaction->getCode();
    $id = intval($transaction->getReference());
    $code_status = $status->getValue();
    $msg = $status->getTypeFromValue();
    
    //atualizando a venda
    mysql_query("UPDATE tb_venda SET negociacao_code = '".$code."', negociacao_status = '".$code_status."', 
                negociacao_msg = '".$msg."', data_atualizacao = NOW() WHERE id = ".$id)or die(mysql_error());
      
} 
