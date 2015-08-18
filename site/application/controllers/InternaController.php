<?php
class InternaController extends BaseController
{
    private $config = array ();
    private $modeloCustomer;    
    private $modeloVenda;    
    private $modeloProduto;    
    private $modeloVendaProduto;    
    private $baseUrl;
    
    function preDispatch () {
        //configuração 
        $confUrl = Zend_Registry::get ( 'config' );
        $this->config = $confUrl->app->config->toArray ();
        
        //recuper a Base da URL
        $front = Zend_Controller_Front::getInstance ();
        $this->baseUrl = $front->getBaseUrl ();
        
        //autenticação
        $auth = Zend_Auth::getInstance();
        if (! $auth->hasIdentity()) {
            $this->_redirect("/index/index/");
        }
  }
  
    public function init() {
        
        //inicia o modelo
        $this->modeloCustomer = new ModeloCustomer ( );
        $this->modeloVenda = new ModeloVenda ( );
        $this->modeloProduto = new ModeloProduto ( );
        $this->modeloVendaProduto = new ModeloVendaProduto ( );
         
    }

    public function indexAction()
    {
        $this->_helper->layout()->setLayout('principal');

        $session = Zend_Registry::get('session');
        $usuario_logado = $session->usuarioLogado;
        //print_r($session); exit;

        $this->view->id_customer = $usuario_logado->id;

        $vendas = $this->modeloVenda->getByIdCustomer($usuario_logado->id);
        $tabela = '';
        
        if(count($vendas) > 0){
            foreach ($vendas as $venda) {
                switch ($venda['negociacao_status']) {
                    case 1:
                        $situacao = 'Aguardando Pagamento';
                        break;
                    case 2:
                        $situacao = 'Em Análise';
                        break;
                    case 3:
                        $situacao = 'Paga';
                        break;
                    case 4:
                        $situacao = 'Disponível';
                        break;
                    case 5:
                        $situacao = 'Em Disputa';
                        break;
                    case 6:
                        $situacao = 'Devolvida';
                        break;
                    case 7:
                        $situacao = 'Cancelada';
                        break;
                    default:
                        $situacao = 'Pendente';
                        break;
                }
                $tabela .= '<tr>';
                    $tabela .= '<td style="border-bottom:solid 1px; padding:5px">'.$venda['data_cadastro'].'</td>';
                    $tabela .= '<td style="border-bottom:solid 1px; padding:5px">'.$venda['produto'].'</td>';
                    $tabela .= '<td style="border-bottom:solid 1px; padding:5px; text-align:center;">'.number_format($venda['qtde'],'0',',','.').'</td>';
                    $tabela .= '<td style="border-bottom:solid 1px; padding:5px; text-align:right;">'.$venda['valor'].'</td>';
                    $tabela .= '<td style="border-bottom:solid 1px; padding:5px; text-align:center;">'.$situacao.'</td>';
                $tabela .= '</tr>';
            }
        }else{
            $tabela .= '<tr>
                            <td colspan="4">Você não possui nenhum crédito.</td>
                        </tr>';
        }

        $this->view->lista_vendas = $tabela;


        $produtos = $this->modeloProduto->getAll();
        $tabela = '';
        
        if(count($produtos) > 0){
            foreach ($produtos as $produto) {
                $tabela .= '<tr>';
                    $tabela .= '<td style="border-bottom:solid 1px; padding:5px">'.$produto['descricao'].'</td>';
                    $tabela .= '<td style="border-bottom:solid 1px; padding:5px; text-align:right;">'.number_format($produto['valor'],'2',',','.').'</td>';
                    $tabela .= '<td style="border-bottom:solid 1px; padding:5px;">
                                    <input type="hidden" value="'.$produto['id'].'" name="produto_id[]">
                                    <input style="font-size:12px;" type="text" size="5" maxlenght="3" id="qtde_'.$produto['id'].'" name="qtde[]" value="0">
                                </td>';
                $tabela .= '</tr>';
            }
        }else{
            $tabela .= '<tr>
                            <td colspan="4">Neste momento nenhum produto está disponível para compra.</td>
                        </tr>';
        }

        $this->view->lista_produtos = $tabela;

    }

    public function comprarAction () {
        $this->_helper->viewRenderer->setNoRender ( true );
        $this->_helper->layout ()->disableLayout ();
        
        if ($this->_request->isPost()) {
            include_once 'PagSeguroLibrary/PagSeguroLibrary.php';
            
            // Instantiate a new payment request
            $paymentRequest = new PagSeguroPaymentRequest();

            // Sets the currency
            $paymentRequest->setCurrency("BRL");

            $session = Zend_Registry::get('session');
            $usuario_logado = $session->usuarioLogado;

            $dados_venda = array(
                'id'=>null,
                'id_customer'=>$usuario_logado->id,
                'data_cadastro'=>date('Y-m-d H:i:s'),
                'valor_total'=>'0'
            );
            //inserindo a venda no bd
            $id_venda = $this->modeloVenda->insert($dados_venda);
            $valor_total = 0;

            for ($i = 0; $i < count($_POST['produto_id']); $i++) {
                $produto = $this->modeloProduto->getById($_POST['produto_id'][$i]);
                
                $paymentRequest->addItem($_POST['produto_id'][$i], $produto['descricao'], $_POST['qtde'][$i], $produto['valor']);
                
                $valor_total += $_POST['qtde'][$i] * $produto['valor'];
                
                $dados = array('id'=>null,'qtde'=>$_POST['qtde'][$i],'valor_unit'=>$produto['valor'],'id_produto'=>$_POST['produto_id'][$i], 'id_venda'=>$id_venda);
                
                $this->modeloVendaProduto->add($dados);
            }

            $this->modeloVenda->update(array('valor_total'=>$valor_total), 'id = '.$id_venda);
            // Add an item for this payment request
            //$paymentRequest->addItem('0001', 'Notebook prata', 2, 430.00);

            // Sets a reference code for this payment request, it is useful to identify this payment
            // in future notifications.
            $paymentRequest->setReference($id_venda);

            // Sets shipping information for this payment request
            // $sedexCode = PagSeguroShippingType::getCodeByType('SEDEX');
            // $paymentRequest->setShippingType($sedexCode);
            // $paymentRequest->setShippingAddress(
            //     '01452002',
            //     'Av. Brig. Faria Lima',
            //     '1384',
            //     'apto. 114',
            //     'Jardim Paulistano',
            //     'São Paulo',
            //     'SP',
            //     'BRA'
            // );



            // Sets your customer information.
            $paymentRequest->setSender(
                $usuario_logado->nome.' '.$usuario_logado->sobrenome,
                $usuario_logado->email,
                '11',
                '56273440',
                'CPF',
                $usuario_logado->cpf
            );

            // Sets the url used by PagSeguro for redirect user after ends checkout process
            $paymentRequest->setRedirectUrl("http://www.banheirosvrio.com.br/compra/sucesso.php");

            // Add checkout metadata information
            // $paymentRequest->addMetadata('PASSENGER_CPF', '15600944276', 1);
            // $paymentRequest->addMetadata('GAME_NAME', 'DOTA');
            // $paymentRequest->addMetadata('PASSENGER_PASSPORT', '23456', 1);

            // Another way to set checkout parameters
            $paymentRequest->addParameter('notificationURL', 'http://www.banheirosvrio.com.br/compra/notificacoes.php');
            // $paymentRequest->addParameter('senderBornDate', '07/05/1981');
            // $paymentRequest->addIndexedParameter('itemId', '0003', 3);
            // $paymentRequest->addIndexedParameter('itemDescription', 'Notebook Preto', 3);
            // $paymentRequest->addIndexedParameter('itemQuantity', '1', 3);
            // $paymentRequest->addIndexedParameter('itemAmount', '200.00', 3);

            try {

                /*
                 * #### Credentials #####
                 * Substitute the parameters below with your credentials (e-mail and token)
                 * You can also get your credentials from a config file. See an example:
                 * $credentials = PagSeguroConfig::getAccountCredentials();
                 */
                $credentials = new PagSeguroAccountCredentials("sandro@unitybrasil.com.br",
                    "657F4FAD9A1543F38DB9B7900539A9E6");
                $credentials = PagSeguroConfig::getAccountCredentials();
                // Register this payment request in PagSeguro, to obtain the payment URL for redirect your customer.
                $url = $paymentRequest->register($credentials);

                //self::printPaymentUrl($url);
                $retorno = $url;
            } catch (PagSeguroServiceException $e) {
                die($e->getMessage());
            }

            /*if(!existeCustomer($post)){
                $sql = "INSERT INTO tb_customer (id, nome, sobrenome, email, sexo, celular, cpf, senha, data_cadastro) 
                        VALUES (NULL, :nome, :sobrenome, :email, :sexo, :celular, :cpf, :senha, NOW())";
                try {
                    $senha = sha1($post['senha']);

                    $db = getConnection();
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam("nome", $post['nome']);
                    $stmt->bindParam("sobrenome", $post['sobrenome']);
                    $stmt->bindParam("email", $post['email']);
                    $stmt->bindParam("senha", $senha);
                    $stmt->bindParam("sexo", $post['sexo']);
                    $stmt->bindParam("cpf", $post['cpf']);
                    $stmt->bindParam("celular", $post['celular']);
                    
                    $stmt->execute();
                    $id = $db->lastInsertId();
                    $db = null;
                    
                    $retorno = $id;

                } catch(PDOException $e) {
                    $retorno = 0;
                }

            }else{
                $retorno = 0;
            }*/

            print Zend_Json_Encoder::encode($retorno);
        }
    }
    
}