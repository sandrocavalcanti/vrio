<?php 
/* PRODUTO */
$app->post('/venda', $authenticate($app),'addVenda');
$app->get('/venda', $authenticate($app), 'getVendas');
$app->get('/venda/:id',  $authenticate($app), 'getVenda');
$app->get('/venda/search/:query', $authenticate($app), 'findVendaByName');
$app->put('/venda/:id', $authenticate($app), 'updateVenda');
$app->delete('/venda/:id', $authenticate($app), 'deleteVenda');
$app->get('/vendasapp', 'getVendasAtivas');

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
?>