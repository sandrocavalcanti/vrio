<?php 
/* BANHEIRO */
$app->post('/ponto', $authenticate($app),'addPonto');
$app->get('/ponto', 'getPontos');
$app->get('/ponto/:id',  $authenticate($app), 'getPonto');
$app->get('/ponto/search/:query', $authenticate($app), 'findPontoByName');
$app->put('/ponto/:id', $authenticate($app), 'updatePonto');
$app->delete('/ponto/:id', $authenticate($app), 'deletePonto');

function addPonto()
{
    $request = \Slim\Slim::getInstance()->request();
    $ponto = json_decode($request->getBody());
    $sql = "INSERT INTO tb_ponto_resgate (descricao, logradouro, numero, bairro, cep, cidade, uf, data_cadastro) 
            VALUES (:descricao, :logradouro, :numero, :bairro, :cep, :cidade, :uf, NOW())";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("descricao", $ponto->descricao);
        $stmt->bindParam("logradouro", $ponto->logradouro);
        $stmt->bindParam("numero", $ponto->numero);
        $stmt->bindParam("bairro", $ponto->bairro);
        $stmt->bindParam("cep", $ponto->cep);
        $stmt->bindParam("cidade", $ponto->cidade);
        $stmt->bindParam("uf", $ponto->uf);
        
        $stmt->execute();
        $ponto->id = $db->lastInsertId();
        $db = null;

        echo json_encode($ponto);

    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getPontos() 
{
    $sql = "SELECT *, DATE_FORMAT(data_cadastro,'%d/%m/%Y %H:%i') AS data_cadastro FROM tb_ponto_resgate";
    try {

        $db = getConnection();
        $stmt = $db->query($sql);
        $pontos = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($pontos);
        
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function getPonto($id) 
{
    $sql = "SELECT * FROM tb_ponto_resgate WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $ponto = $stmt->fetchObject();
        $db = null;
        echo json_encode($ponto);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function findPontoByName($query) 
{
    $sql = "SELECT * FROM tb_ponto_resgate WHERE UPPER(descricao) LIKE :query ORDER BY descricao";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $query = "%".$query."%";
        $stmt->bindParam("query", $query);
        $stmt->execute();
        $pontos = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"pontos": ' . json_encode($pontos) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function updatePonto($id) {
    $request = \Slim\Slim::getInstance()->request();
    $body = $request->getBody();
    $ponto = json_decode($body);
    $sql = "UPDATE tb_ponto_resgate SET descricao=:descricao, logradouro=:logradouro, numero=:numero,
    		bairro=:bairro, cep=:cep, cidade=:cidade, uf=:uf,  ativo=:ativo
    		WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("descricao", $ponto->descricao);
        $stmt->bindParam("logradouro", $ponto->logradouro);
        $stmt->bindParam("numero", $ponto->numero);
        $stmt->bindParam("bairro", $ponto->bairro);
        $stmt->bindParam("cep", $ponto->cep);
        $stmt->bindParam("cidade", $ponto->cidade);
        $stmt->bindParam("uf", $ponto->uf);
        $stmt->bindParam("ativo", $ponto->ativo);
        
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
        echo json_encode($ponto);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function deletePonto($id) {
    $sql = "DELETE FROM tb_ponto_resgate WHERE id=:id";
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