<?php 
/* PRODUTO */
$app->post('/produto', $authenticate($app),'addProduto');
$app->get('/produto', $authenticate($app), 'getProdutos');
$app->get('/produto/:id',  $authenticate($app), 'getProduto');
$app->get('/produto/search/:query', $authenticate($app), 'findProdutoByName');
$app->put('/produto/:id', $authenticate($app), 'updateProduto');
$app->delete('/produto/:id', $authenticate($app), 'deleteProduto');

function addProduto()
{
    $request = \Slim\Slim::getInstance()->request();
    $produto = json_decode($request->getBody());
    $sql = "INSERT INTO tb_produto (descricao, valor) 
            VALUES (:descricao, :valor)";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("descricao", $produto->descricao);
        $stmt->bindParam("valor", $produto->valor);
        
        $stmt->execute();
        $produto->id = $db->lastInsertId();
        $db = null;

        echo json_encode($produto);

    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getProdutos() 
{
    $sql = "SELECT * FROM tb_produto";
    try {

        $db = getConnection();
        $stmt = $db->query($sql);
        $produtos = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($produtos);
        
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function getProduto($id) 
{
    $sql = "SELECT * FROM tb_produto WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $produto = $stmt->fetchObject();
        $db = null;
        echo json_encode($produto);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function findProdutoByName($query) 
{
    $sql = "SELECT * FROM tb_produto WHERE UPPER(descricao) LIKE :query ORDER BY descricao";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $query = "%".$query."%";
        $stmt->bindParam("query", $query);
        $stmt->execute();
        $produtos = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"produtos": ' . json_encode($produtos) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function updateProduto($id) {
    $request = \Slim\Slim::getInstance()->request();
    $body = $request->getBody();
    $produto = json_decode($body);
    $sql = "UPDATE tb_produto SET descricao=:descricao, valor=:valor, ativo=:ativo
    		WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("descricao", $produto->descricao);
        $stmt->bindParam("valor", $produto->valor);
        $stmt->bindParam("ativo", $produto->ativo);
        
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
        echo json_encode($produto);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function deleteProduto($id) {
    $sql = "DELETE FROM tb_produto WHERE id=:id";
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