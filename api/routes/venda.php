<?php 
/* PRODUTO */
$app->post('/venda', $authenticate($app),'addVenda');
$app->get('/venda', $authenticate($app), 'getVendas');
$app->get('/venda/:id',  $authenticate($app), 'getVenda');
$app->get('/venda/search/:query', $authenticate($app), 'findVendaByName');
$app->put('/venda/:id', $authenticate($app), 'updateVenda');
$app->delete('/venda/:id', $authenticate($app), 'deleteVenda');

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
?>