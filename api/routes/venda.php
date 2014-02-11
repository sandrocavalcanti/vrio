<?php 
require_once "lib/PagSeguroLibrary/PagSeguroLibrary.php";

/* PRODUTO */
$app->post('/venda', $authenticate($app),'addVenda');
$app->get('/venda', $authenticate($app), 'getVendas');
$app->get('/venda/:id',  $authenticate($app), 'getVenda');
$app->get('/venda/search/:query', $authenticate($app), 'findVendaByName');
$app->put('/venda/:id', $authenticate($app), 'updateVenda');
$app->delete('/venda/:id', $authenticate($app), 'deleteVenda');
$app->get('/vendasapp', 'getVendasAtivas');
$app->post('/vendasaddapp', 'addAppVenda');

function addVenda()
{
    $request = \Slim\Slim::getInstance()->request();
    $venda = json_decode($request->getBody());
    $sql = "INSERT INTO tb_venda (descricao, valor) VALUES (:descricao, :valor)";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("descricao", $venda->descricao);
        $stmt->bindParam("valor", $venda->valor);
        
        $stmt->execute();
        $venda->id = $db->lastInsertId();
        $db = null;

        echo json_encode($venda);

    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getVendas() 
{
    $sql = "SELECT * FROM tb_venda";
    try {

        $db = getConnection();
        $stmt = $db->query($sql);
        $vendas = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($vendas);
        
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function getVenda($id) 
{
    $sql = "SELECT * FROM tb_venda WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $venda = $stmt->fetchObject();
        $db = null;
        echo json_encode($venda);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function findVendaByName($query) 
{
    $sql = "SELECT * FROM tb_venda WHERE UPPER(descricao) LIKE :query ORDER BY descricao";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $query = "%".$query."%";
        $stmt->bindParam("query", $query);
        $stmt->execute();
        $vendas = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"vendas": ' . json_encode($vendas) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function updateVenda($id) {
    $request = \Slim\Slim::getInstance()->request();
    $body = $request->getBody();
    $venda = json_decode($body);
    $sql = "UPDATE tb_venda SET descricao=:descricao, valor=:valor, ativo=:ativo
    		WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("descricao", $venda->descricao);
        $stmt->bindParam("valor", $venda->valor);
        $stmt->bindParam("ativo", $venda->ativo);
        
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
        echo json_encode($venda);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function deleteVenda($id) {
    $sql = "DELETE FROM tb_venda WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getVendasAtivas() {
    $request = \Slim\Slim::getInstance()->request();
    
    $get = $request->get();

    try {  

        $sql = "SELECT v.id, DATE_FORMAT(v.data_cadastro,'%d/%m/%Y') AS data_cadastro, 
                REPLACE(REPLACE(REPLACE(FORMAT(v.valor_total, 2), '.', '@'), ',', '.'), '@', ',') AS valor, 
                FORMAT(vp.qtde, 0) AS qtde
                FROM tb_venda_produto vp INNER JOIN tb_venda v ON v.id = vp.id_venda
                WHERE vp.resgatado = 0 AND v.id_customer=:id
                ORDER BY v.data_cadastro ASC";

        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $get['id']);
        $stmt->execute();
        $vendas = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        
        $retorno['vendas'] = $vendas;

        header('Content-Type: text/javascript; charset=utf8');

        $callback = $get['callback'];
        echo $callback.'('.json_encode($retorno ).');';

    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function addAppVenda()
{
    $request = \Slim\Slim::getInstance()->request();
    $post = $request->post();

    // Instantiate a new payment request
    $paymentRequest = new PagSeguroPaymentRequest();

    // Sets the currency
    $paymentRequest->setCurrency("BRL");

    // Add an item for this payment request
    $paymentRequest->addItem('0001', 'Notebook prata', 2, 430.00);

    // Add another item for this payment request
    $paymentRequest->addItem('0002', 'Notebook rosa', 2, 560.00);

    // Sets a reference code for this payment request, it is useful to identify this payment
    // in future notifications.
    $paymentRequest->setReference("REF123");

    // Sets shipping information for this payment request
    $sedexCode = PagSeguroShippingType::getCodeByType('SEDEX');
    $paymentRequest->setShippingType($sedexCode);
    $paymentRequest->setShippingAddress(
        '01452002',
        'Av. Brig. Faria Lima',
        '1384',
        'apto. 114',
        'Jardim Paulistano',
        'São Paulo',
        'SP',
        'BRA'
    );

    // Sets your customer information.
    $paymentRequest->setSender(
        'João Comprador',
        'comprador@s2it.com.br',
        '11',
        '56273440',
        'CPF',
        '156.009.442-76'
    );

    // Sets the url used by PagSeguro for redirect user after ends checkout process
    $paymentRequest->setRedirectUrl("http://www.banheirosvrio.com.br/compra/sucesso.php");

    // Add checkout metadata information
    $paymentRequest->addMetadata('PASSENGER_CPF', '15600944276', 1);
    $paymentRequest->addMetadata('GAME_NAME', 'DOTA');
    $paymentRequest->addMetadata('PASSENGER_PASSPORT', '23456', 1);

    // Another way to set checkout parameters
    $paymentRequest->addParameter('notificationURL', 'http://www.banheirosvrio.com.br/compra/notificacoes.php');
    $paymentRequest->addParameter('senderBornDate', '07/05/1981');
    $paymentRequest->addIndexedParameter('itemId', '0003', 3);
    $paymentRequest->addIndexedParameter('itemDescription', 'Notebook Preto', 3);
    $paymentRequest->addIndexedParameter('itemQuantity', '1', 3);
    $paymentRequest->addIndexedParameter('itemAmount', '200.00', 3);

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

        //self::printPaymentUrl($url);
        $retorno = $url;
    } catch (PagSeguroServiceException $e) {
        die($e->getMessage());
    }

    /*if(!existeCustomer($post)){
        $sql = "INSERT INTO tb_customer (id, nome, sobrenome, email, sexo, celular, cpf, senha, data_cadastro) 
                VALUES (NULL, :nome, :sobrenome, :email, :sexo, :celular, :cpf, :senha, NOW())";
        try {
            $senha = sha1($post['senha']);

            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("nome", $post['nome']);
            $stmt->bindParam("sobrenome", $post['sobrenome']);
            $stmt->bindParam("email", $post['email']);
            $stmt->bindParam("senha", $senha);
            $stmt->bindParam("sexo", $post['sexo']);
            $stmt->bindParam("cpf", $post['cpf']);
            $stmt->bindParam("celular", $post['celular']);
            
            $stmt->execute();
            $id = $db->lastInsertId();
            $db = null;
            
            $retorno = $id;

        } catch(PDOException $e) {
            $retorno = 0;
        }

    }else{
        $retorno = 0;
    }*/

    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Headers: X-Requested-With');
    
    //print_r($post);
    echo json_encode($retorno);

}
?>