<?php 
require_once('./lib/Common.php');

/* CUSTOMER */
$app->post('/customer', 'addCustomer');
$app->get('/customerlogin', 'loginCustomer');
$app->get('/customer', $authenticate($app), 'getCustomers');
$app->get('/customer/:id', $authenticate($app), 'getCustomer');
$app->get('/customer/search/:query', $authenticate($app), 'findCustomerByName');
$app->put('/customer/:id', $authenticate($app), 'updateCustomer');
$app->delete('/customer/:id', $authenticate($app), 'deleteCustomer');

function addCustomer()
{
    $request = \Slim\Slim::getInstance()->request();
    $customer = json_decode($request->getBody());
    $sql = "INSERT INTO tb_customer (nome, sobrenome, email, sexo, celular, cpf, senha) 
            VALUES (:nome, :sobrenome, :email, :sexo, :celular, :senha)";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("nome", $customer->nome);
        $stmt->bindParam("sobrenome", $customer->sobrenome);
        $stmt->bindParam("email", $customer->email);
        $stmt->bindParam("senha", $customer->senha);
        $stmt->bindParam("sexo", $customer->sexo);
        $stmt->bindParam("cpf", $customer->sexo);
        $stmt->bindParam("celular", $customer->celular);
        
        $stmt->execute();
        $customer->id = $db->lastInsertId();
        $db = null;

        echo json_encode($customer);

    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getCustomers() 
{
    $sql = "SELECT *, DATE_FORMAT(data_cadastro,'%d/%m/%Y %H:%i') AS data_cadastro 
            FROM tb_customer ORDER BY nome ASC";
    try {

        $db = getConnection();
        $stmt = $db->query($sql);
        $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($customers);
        
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function getCustomer($id) 
{
    $sql = "SELECT * FROM tb_customer WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $customer = $stmt->fetchObject();
        $db = null;
        echo json_encode($customer);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function findCustomerByName($query) 
{
    $sql = "SELECT * FROM tb_customer WHERE UPPER(nome) LIKE :query ORDER BY nome";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $query = "%".$query."%";
        $stmt->bindParam("query", $query);
        $stmt->execute();
        $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"customers": ' . json_encode($customers) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function updateCustomer($id) {
    $request = \Slim\Slim::getInstance()->request();
    $body = $request->getBody();
    $customer = json_decode($body);
    $sql = "UPDATE tb_customer 
            SET nome=:nome, sobrenome=:sobrenome, email=:email, sexo=:sexo, celular=:celular, cpf=:cpf, ativo=:ativo 
            WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("nome", $customer->nome);
        $stmt->bindParam("sobrenome", $customer->sobrenome);
        $stmt->bindParam("email", $customer->email);
        $stmt->bindParam("ativo", $customer->ativo);
        $stmt->bindParam("sexo", $customer->sexo);
        $stmt->bindParam("cpf", $customer->cpf);
        $stmt->bindParam("celular", $customer->celular);
        
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
        echo json_encode($customer);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function deleteCustomer($id) {
    $sql = "DELETE FROM tb_customer WHERE id=:id";
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

function loginCustomer() {
    $request = \Slim\Slim::getInstance()->request();
    $get = $request->get();

    $sql = "SELECT id, nome, sobrenome, email, celular, sexo, cpf, data_nascimento, uf
            FROM tb_customer WHERE celular=:celular AND senha=:senha AND ativo=1";

    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $senha = sha1($get['senha']);
        $stmt->bindParam("senha", $senha);
        $stmt->bindParam("celular", $get['celular']);
        $stmt->execute();
        $customer = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        if(isset($customer[0]->id)){
            //$customer[0]->data_nascimento = Common::mysqlToBr($customer[0]->data_nascimento);
            $retorno['customer'] = $customer;
        }else{
            $retorno = 0;
        }
        
        header('Content-Type: text/javascript; charset=utf8');
        // header('Access-Control-Allow-Origin: http://www.example.com/');
        // header('Access-Control-Max-Age: 3628800');
        // header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

        $callback = $get['callback'];
        echo $callback.'('.json_encode($customer).');';

    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
?>