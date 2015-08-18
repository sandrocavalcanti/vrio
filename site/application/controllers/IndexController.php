
<?php
class IndexController extends BaseController
{
    private $modeloCustomer;
    private $modeloBanheiro;

    public function init ()
    {
        $this->modeloCustomer = new ModeloCustomer ( );
        $this->modeloBanheiro = new ModeloBanheiro ( );
    }
    public function indexAction ()
    {
		$this->_helper->layout()->setLayout('principal');
    }
	public function quemSomosAction ()
    {
		$this->_helper->layout()->setLayout('principal');
    }
    public function banheirosAction ()
    {
		$this->_helper->layout()->setLayout('principal');
    }
    public function creditosAction ()
    {
		$this->_helper->layout()->setLayout('principal');
    }
    public function localizarBanheirosAction ()
    {
        $this->_helper->layout()->setLayout('principal');
        $this->view->banheiros = $this->modeloBanheiro->getAll();
    }
    public function contatoAction ()
    {
        $this->_helper->layout()->setLayout('principal');
        
        if ($this->getRequest()->isPost()) {
            $post = Zend_Registry::get('post');

            //$date = Zend_Registry::get('date');
            $mens = "Contato Site Banheiros Vrio:\n";
            $mens .= "-------------------------------------------------------------------\n";
            $mens .= "Nome: " . html_entity_decode(utf8_decode($post->nome)) . "\n";
            $mens .= "E-mail: " . $post->email . "\n";
            $mens .= "Telefone: ".$post->fone . "\n";
            $mens .= "Mensagem: " . html_entity_decode(utf8_decode($post->msg)) . "\n";
            $mens .= "-------------------------------------------------------------------\n";
            $mens .= "Data/Hora: $date\n";
            $mail = new Zend_Mail('utf-8');
            $mail->setBodyText($mens);

            // Configuração para autenticação do e-mail
            $smtpServer = 'smtp.banheirosvrio.com.br';
            $username = 'contato@banheirosvrio.com.br';
            $password = 'vrio1202';
            $config = array('ssl' => 'tls','auth' => 'login','username' => $username,'password' => $password);

            $transport = new Zend_Mail_Transport_Smtp($smtpServer, $config);

            $mail->setFrom('contato@banheirosvrio.com.br', 'Banheiros Vrio');
            $mail->addTo('contato@banheirosvrio.com.br');
            $mail->addCc('ricardomota@riscozeroautos.com.br');
            $mail->setSubject('Contato Site - Banheiros Vrio');
            $enviadoSucesso = '';
            try {
                $mail->send($transport);
                $enviadoSucesso = "<div id='msgEmail' style='color:#ffffff;background-color: #029b28;'><p style='color:#ffffff;'>E-mail enviado com sucesso! Em breve entraremos contato.</p></div>";
            } catch (Exception $e) {
                $enviadoSucesso = "<div id='msgEmail'>Ocorreu um erro ao enviar o e-mail! Tente novamente.</div>";
            }
            
            $this->view->msgenviado = $enviadoSucesso;
        }
    }

    public function cadastroAction ()
    {
        $this->_helper->layout()->setLayout('principal');

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();

            unset($post['senha_confirme']);
            unset($post['enviar']);

            $id = $this->modeloCustomer->add($post);

            if($id > 0){
                $date = date('d/m/Y H:i:s');
                $mens = "Olá " . html_entity_decode(utf8_decode($post['nome'])) . ", seu cadastro foi realizado com sucesso.\n";
                $mens .= "Segue abaixo os seus dados de acesso:\n";
                $mens .= "-------------------------------------------------------------------\n";
                $mens .= "Celular: ".$post['celular'] . "\n";
                $mens .= "Senha: ".$post['senha'] . "\n";
                $mens .= "-------------------------------------------------------------------\n";
                $mens .= "Data/Hora: $date\n\n";
                $mens .= "Equipe Banheiros Vrio\n";
                $mail = new Zend_Mail('utf-8');
                $mail->setBodyText($mens);

                // Configuração para autenticação do e-mail
                $smtpServer = 'smtp.banheirosvrio.com.br';
                $username = 'contato@banheirosvrio.com.br';
                $password = 'vrio1202';
                $config = array('ssl' => 'tls','auth' => 'login','username' => $username,'password' => $password);

                $transport = new Zend_Mail_Transport_Smtp($smtpServer, $config);

                $mail->setFrom('contato@banheirosvrio.com.br', 'Banheiros Vrio');
                $mail->addTo($post['email']);
                $mail->setSubject('Cadastro - Banheiros Vrio');
                $enviadoSucesso = '';
                try {
                    $mail->send($transport);
                    $enviadoSucesso = "<div id='msgEmail' style='color:#ffffff;background-color: #029b28;'><p style='color:#ffffff;'>Cadastro realizado com sucesso! Você receberá em seu e-mail seus dados de acesso.</p></div>";
                } catch (Exception $e) {
                    print_r($e->getMessage()); exit;
                    $enviadoSucesso = "<div id='msgEmail' style='color:#ffffff;background-color: #red;'><p style='color:#ffffff;'>Ocorreu um erro ao enviar o e-mail! Tente novamente.</p></div>";
                }
            }else{
                $enviadoSucesso = "<div id='msgEmail' style='color:#ffffff;background-color: #red;'><p style='color:#ffffff;'>Erro: Já existe um cadastro com esse Celular e/ou CPF! Tente novamente.</p></div>";
            }
            
            $this->view->msgenviado = $enviadoSucesso;
        }
        
    }
    
}
