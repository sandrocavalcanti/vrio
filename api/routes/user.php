<?php 
/* USER */
$app->post('/user', $authenticate($app), 'addUser');
$app->get('/user', $authenticate($app), 'getUsers');
$app->get('/user/:id',  $authenticate($app), 'getUser');
$app->get('/user/search/:query', $authenticate($app), 'findUserByName');
$app->put('/user/:id', $authenticate($app), 'updateUser');
$app->delete('/user/:id', $authenticate($app), 'deleteUser');

function addUser()
{
    $request = \Slim\Slim::getInstance()->request();
    $user = json_decode($request->getBody());

    $sql = "INSERT INTO tb_user (nome, email, senha, data_cadastro) 
            VALUES (:nome, :email, :senha, NOW())";
    try {
        //cripted
        $pass = sha1($user->senha);

        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("nome", $user->nome);
        $stmt->bindParam("email", $user->email);
        $stmt->bindParam("senha", $pass);
        
        $stmt->execute();
        $user->id = $db->lastInsertId();
        $db = null;

        echo json_encode($user);

    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getUsers() 
{
    $sql = "SELECT *, DATE_FORMAT(data_cadastro,'%d/%m/%Y %H:%i') AS data_cadastro 
            FROM tb_user ORDER BY nome ASC";
    try {

        $db = getConnection();
        $stmt = $db->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($users);
        
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function getUser($id) 
{
    $sql = "SELECT * FROM tb_user WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $user = $stmt->fetchObject();
        $db = null;
        echo json_encode($user);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function findUserByName($query) 
{
    $sql = "SELECT * FROM tb_user WHERE UPPER(nome) LIKE :query ORDER BY nome";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $query = "%".$query."%";
        $stmt->bindParam("query", $query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"users": ' . json_encode($users) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function updateUser($id) {
    $request = \Slim\Slim::getInstance()->request();
    $body = $request->getBody();
    $user = json_decode($body);
    $sql = "UPDATE tb_user SET nome=:nome, email=:email, senha=:senha WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("nome", $user->nome);
        $stmt->bindParam("email", $user->email);
        $stmt->bindParam("senha", $user->senha);
        
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
        echo json_encode($user);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function deleteUser($id) {
    $sql = "DELETE FROM tb_user WHERE id=:id";
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
?>