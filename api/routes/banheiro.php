<?php 
/* BANHEIRO */
$app->post('/banheiro', $authenticate($app),'addBanheiro');
$app->get('/banheiro', $authenticate($app), 'getBanheiros');
$app->get('/banheiro/:id',  $authenticate($app), 'getBanheiro');
$app->get('/banheiro/search/:query', $authenticate($app), 'findBanheiroByName');
$app->put('/banheiro/:id', $authenticate($app), 'updateBanheiro');
$app->delete('/banheiro/:id', $authenticate($app), 'deleteBanheiro');
$app->get('/banheirosapp', 'getBanheirosApp');

function addBanheiro()
{
    $request = \Slim\Slim::getInstance()->request();
    $banheiro = json_decode($request->getBody());
    $sql = "INSERT INTO tb_banheiro (descricao, logradouro, numero, bairro, cep, cidade, uf, latitude, longitude, tipo, data_cadastro) 
            VALUES (:descricao, :logradouro, :numero, :bairro, :cep, :cidade, :uf, :latitude, :longitude, :tipo, NOW())";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("descricao", $banheiro->descricao);
        $stmt->bindParam("logradouro", $banheiro->logradouro);
        $stmt->bindParam("numero", $banheiro->numero);
        $stmt->bindParam("bairro", $banheiro->bairro);
        $stmt->bindParam("cep", $banheiro->cep);
        $stmt->bindParam("cidade", $banheiro->cidade);
        $stmt->bindParam("uf", $banheiro->uf);
        $stmt->bindParam("latitude", $banheiro->latitude);
        $stmt->bindParam("longitude", $banheiro->longitude);
        $stmt->bindParam("tipo", $banheiro->tipo);
        
        $stmt->execute();
        $banheiro->id = $db->lastInsertId();
        $db = null;

        echo json_encode($banheiro);

    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getBanheiros() 
{
    $sql = "SELECT *, DATE_FORMAT(data_cadastro,'%d/%m/%Y %H:%i') AS data_cadastro FROM tb_banheiro";
    try {

        $db = getConnection();
        $stmt = $db->query($sql);
        $banheiros = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($banheiros);
        
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function getBanheiro($id) 
{
    $sql = "SELECT * FROM tb_banheiro WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $banheiro = $stmt->fetchObject();
        $db = null;
        echo json_encode($banheiro);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function findBanheiroByName($query) 
{
    $sql = "SELECT * FROM tb_banheiro WHERE UPPER(descricao) LIKE :query ORDER BY descricao";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $query = "%".$query."%";
        $stmt->bindParam("query", $query);
        $stmt->execute();
        $banheiros = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"banheiros": ' . json_encode($banheiros) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function updateBanheiro($id) {
    $request = \Slim\Slim::getInstance()->request();
    $body = $request->getBody();
    $banheiro = json_decode($body);
    $sql = "UPDATE tb_banheiro SET descricao=:descricao, logradouro=:logradouro, numero=:numero,
    		bairro=:bairro, cep=:cep, cidade=:cidade, uf=:uf, latitude=:latitude, longitude=:longitude, tipo=:tipo, 
            ativo=:ativo
    		WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("descricao", $banheiro->descricao);
        $stmt->bindParam("logradouro", $banheiro->logradouro);
        $stmt->bindParam("numero", $banheiro->numero);
        $stmt->bindParam("bairro", $banheiro->bairro);
        $stmt->bindParam("cep", $banheiro->cep);
        $stmt->bindParam("cidade", $banheiro->cidade);
        $stmt->bindParam("uf", $banheiro->uf);
        $stmt->bindParam("latitude", $banheiro->latitude);
        $stmt->bindParam("longitude", $banheiro->longitude);
        $stmt->bindParam("tipo", $banheiro->tipo);
        $stmt->bindParam("ativo", $banheiro->ativo);
        
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
        echo json_encode($banheiro);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function deleteBanheiro($id) {
    $sql = "DELETE FROM tb_banheiro WHERE id=:id";
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

function getBanheirosApp() {
    $request = \Slim\Slim::getInstance()->request();
    
    $get = $request->get();

    try {  

        $sql = "SELECT * FROM tb_banheiro WHERE ativo = 1";

        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $banheiros = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        
        $retorno['banheiros'] = $banheiros;

        header('Content-Type: text/javascript; charset=utf8');

        $callback = $get['callback'];
        echo $callback.'('.json_encode($retorno ).');';

    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
?>