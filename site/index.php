<?php
if($_SERVER['SERVER_NAME'] ==  'localhost'){
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', 'On');
}else{
	ini_set('display_errors', 'off');
}
set_include_path(
PATH_SEPARATOR . '../../library_zf/' .
PATH_SEPARATOR . './application/class' .
PATH_SEPARATOR . './application/controllers' .
PATH_SEPARATOR . './application/models' .
PATH_SEPARATOR . get_include_path());
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
$options = array(
'layout' => 'principal', 
'layoutPath' => './application/views/layouts', 
'contentKey' => 'conteudoSite');
Zend_Layout::startMvc($options);
/** O método set é responsável por armazenar variáveis que podem ser usadas
 * pelos aplicativos. Aqui, registrando os arrays post e get com dados vindos do usuário.
 * o Zend_Filter limpa os dados.
 */
Zend_Registry::set('post', new Zend_Filter_Input(NULL, NULL, $_POST));
Zend_Registry::set('get', new Zend_Filter_Input(NULL, NULL, $_GET));
/** Inicia a sessão global */
Zend_Session::start();
/** Cria o manipulador da dessão */
Zend_Registry::set('session', new Zend_Session_Namespace());
/** Configura o controlador do projeto.
 * O Controlador, por acaso, é o index.php
 */
$baseUrl = substr($_SERVER['PHP_SELF'], 0,
strpos($_SERVER['PHP_SELF'], '/index.php'));
/** Cria uma nova instância da classe controladora */
$frontController = Zend_Controller_Front::getInstance();
/** Configura o endereço do controlador do projeto */
$frontController->setbaseUrl($baseUrl);
/** Indica o diretório onde estão os outros controladores da aplicação */
$controllers = array('default' => './application/controllers');
/**set os controllers*/
$frontController->setControllerDirectory($controllers);
//** O controlador deve tratar as exceçoes */
$frontController->throwExceptions(true);
/** Configurações da base de dados.
 * Indica onde estão as Configuraçõesdo projeto.
 * Estão no arquivo config.ini na seção database.
 */
if($_SERVER['SERVER_NAME'] ==  'localhost'){
    $config = new Zend_Config_Ini('./application/configs/config.ini', 'dev');
}else{
		$config = new Zend_Config_Ini('./application/configs/config.ini', 'producao');
}
/** Registra na memória a variável config */
Zend_Registry::set('config', $config);
/** Configura a conexão com a base de dados, pegando as variáveis do arquivo
 * de configurão.
 */
$db = Zend_Db::factory($config->db->adapter, $config->db->config->toArray());
Zend_Db_Table_Abstract::setDefaultAdapter($db);
$db->query("SET NAMES utf8");
$db->query("SET CHARACTER SET utf8");

/** Registra a variável db */
Zend_Registry::set('db', $db);
/** Configura o formato para moeda */
setlocale(LC_MONETARY, 'ptb');
$date = new Zend_Date('pt_BR');
$date->setTimezone('America/Recife');
Zend_Registry::set('date', $date);
/** Executa o controlador do projeto.
 * Ele irá receber todas as requisições e invocar os arquivos correspondentes.
 */
$frontController->dispatch();
