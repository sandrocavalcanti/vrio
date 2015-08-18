<?php 
require_once "lib/PagSeguroLibrary/PagSeguroLibrary.php";

/* PRODUTO */
$app->post('/venda', $authenticate($app),'addVenda');
$app->get('/venda', $authenticate($app), 'getVendas');
$app->get('/venda/:id',  $authenticate($app), 'getVenda');
$app->get('/vendaprodutos/:id',  $authenticate($app), 'getProdutosVenda');
$app->put('/vendaresgate/:id',  $authenticate($app), 'resgatarVenda');
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
    $sql = "SELECT v.*, DATE_FORMAT(v.data_cadastro, '%d/%m/%Y') AS data_cadastro, 
            CONCAT(c.nome,' ',c.sobrenome) AS cliente 
            FROM tb_venda v INNER JOIN tb_customer c ON c.id = v.id_customer";
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

function getProdutosVenda($id) {
    $request = \Slim\Slim::getInstance()->request();
    $body = $request->getBody();

    $sql = "SELECT p.descricao AS produto, DATE_FORMAT(vp.data_resgate,'%d/%m/%Y') AS data_resgate, 
            REPLACE(REPLACE(REPLACE(FORMAT((vp.valor_unit * vp.qtde), 2), '.', '@'), ',', '.'), '@', ',') AS valor, 
            FORMAT(vp.qtde, 0) AS qtde
            FROM tb_venda_produto vp INNER JOIN tb_produto p ON p.id = vp.id_produto
            WHERE vp.id_venda=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $produtos = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        echo json_encode($produtos);
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
                WHERE v.negociacao_status IN (3,4) AND vp.resgatado = 0 AND v.id_customer=:id
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

    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Headers: X-Requested-With');   

    $retorno = 0;
    echo json_encode($retorno); exit;

    $array_post['id_customer'] = $post['id_customer'];
    foreach($post as $key=>$value){
        if(strpos($key, 'prod_') !== false){
            $array_post['produto_id'][] = str_replace('prod_', '', $key);
            $array_post['qtde'][] = $value;
        }
    }

    $fields_string = http_build_query($array_post);

    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    $url = 'http://www.banheirosvrio.com.br/site/public/compra/index.php';
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_HEADER, 0);
    curl_setopt($ch,CURLOPT_POST, count($array_post));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

    //execute post
    $retorno = curl_exec($ch);

    //close connection
    curl_close($ch);

    //$retorno = 0;
    echo json_encode($retorno);
}

function resgatarVenda($id) {
    $request = \Slim\Slim::getInstance()->request();
    $body = $request->getBody();
    
    $sql = "UPDATE tb_venda_produto SET data_resgate=NOW(), resgatado=1 WHERE id_venda=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
        echo json_encode(true);
    } catch(PDOException $e) {
        //echo '{"error":{"text":'. $e->getMessage() .'}}';
        echo json_encode(false);
    }
}
?>