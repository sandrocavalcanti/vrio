<?php
ini_set('display_errors',0);

class AuthController extends Zend_Controller_Action {
    /**
     * Este método será executado sempre que a classe for instanciada,
     * depois do construtor.
     * Faz o carregamento das classes que serão usadas pelo controlador.
     *
     * @return void
     */
    public function init () {

    }
    /**
     * Método que mostra a página inicial
     *
     * @return void
     */
    public function indexAction () {
        $this->_redirect('/index/');
    }
    
    private function showDivErro ($valor) {
        return "<div class='msg'>" . $valor . "</div>";
    }
    
    public function loginAction () {
        $this->_helper->viewRenderer->setNoRender ( true );
        $this->_helper->layout ()->disableLayout ();
        
        if ($this->_request->isPost()) {
            //pega as informações do usuário
            $filtro = new Zend_Filter_StripTags();
            $caracters = array("(",")",".");
            $user = $this->getRequest()->getParam('celular');
            $user = str_replace($caracters, "", $user);
            $senha = $filtro->filter($this->getRequest()->getParam('senha'));
            if (empty($user)) {
                $retorno = 0;
            } else {
                $db = Zend_Registry::get('db');
                $authAdapter = new Zend_Auth_Adapter_DbTable($db);
                $authAdapter->setTableName('tb_customer');
                $authAdapter->setIdentityColumn('celular');
                $authAdapter->setCredentialColumn('senha');
                $authAdapter->setCredentialTreatment("SHA1(?)");
                //seta as credencias para a autenticação
                $authAdapter->setIdentity($user);
                $authAdapter->setCredential($senha);
                //faço a autenticação
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                if ($result->isValid()) {
                    //sucesso
                    $data = $authAdapter->getResultRowObject(null, 'senha');
                    $auth->getStorage()->write($data);
                    //recupero a sessão
                    $session = Zend_Registry::get('session');
                    $userLogado = Zend_Auth::getInstance()->getIdentity();
                    $session->usuarioLogado = $userLogado;
                    //print_r($session); exit;
                    Zend_Registry::set($usuarioLogado, $session);
                    //redirecionando para a pagina inicial
                    $retorno = 1;
                } else {
                    //erro
                    $retorno = 0;
                }
            }

            print Zend_Json_Encoder::encode($retorno);
        }
    }
    public function logoutAction () {
        session_destroy();
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect("/index/index/");
    }
}