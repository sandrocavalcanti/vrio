<?php 
/* MAIN */
$app->post("/login", 'login');
$app->get("/logout", 'logout');
$app->get("/checkauth", $authenticate($app));

function logout()
{
    unset($_SESSION['vrio']['auth']);
}

function login()
{
    $request = \Slim\Slim::getInstance()->request();
    $user = json_decode($request->getBody());

    $sql = "SELECT id FROM tb_user WHERE email LIKE :email AND senha LIKE SHA1(:senha)";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("email", $user->email);
        $stmt->bindParam("senha", $user->senha);
        $stmt->execute();

        if($stmt->fetchColumn() > 0){
            //regustrando o login na sessao
            $_SESSION['vrio']['auth'] = true;
            echo '{"login":{"auth":true}}';
        }else{
            unset($_SESSION['vrio']['auth']);
            echo '{"login":{"auth":false}}';
        }
        
        $db = null;
        
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getConnection() 
{
    $serverList = array('localhost', '127.0.0.1');

    if(!in_array($_SERVER['HTTP_HOST'], $serverList)) {
        $dbhost="localhost";
        $dbuser="gestor_apps";
        $dbpass="apps1202";
        $dbname="gestor_apps";
    }else{
        $dbhost="localhost";
        $dbuser="root";
        $dbpass="root";
        $dbname="vrio";    
    }
    
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}
?>