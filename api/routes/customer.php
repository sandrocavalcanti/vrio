<?php 
/* CUSTOMER */
$app->post('/customer', 'addCustomer');
$app->post('/customerlogin', 'loginCustomer');
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
    $post = $request->post();

    $sql = "SELECT id FROM tb_customer WHERE celular=:celular AND senha=:senha";

    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $senha = sha1($post['senha']);
        $stmt->bindParam("senha", $senha);
        $stmt->bindParam("celular", $post['celular']);
        
        $return = $stmt->execute();
        print $return; exit;
        $db = null;
        if($return > 0){
            $retorno = 1;
        }else{
            $retorno = 0;
        }
        echo json_encode($retorno);

    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
?>