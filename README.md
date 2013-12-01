Aplicativo vrio
====

Este projeto consiste em um aplicativo mobile Sencha Touch integrado com uma API em Slim PHP + MySQL, incluindo acesso administrativo atravẽs de uma interface WEB com Angular JS.

Modelo de Dados
====

Cliente = [
  {
          nome:'',
          sobrenome:'',
          email:'',
          senha:'',
          creditos:[
            {
              qtde:0,
              valor:0.00,
              data_compra:'',
              negociacao_id:0,
              status:'',
              forma_pgto:''
            }
          ],
          utilizado:[
            {
              data:'',
              banheiro_id:0
            }
          ],
          data_cadastro:''
  }
];

Banheiro = [
  {
    descricao:'',
    latitude:''
    longitude:'',
    status:0,
    fotos:[
      {
        descricao:'',
        local:''
      }
    ]
  }
];

Usuario = [
  {
    nome:'',
    email:'',
    senha:'',
    data_cadastro:''
  }
];

Eventos
====

Cliente:
  - CRUD;
  - Login;
  - Logout;
  - LembrarSenha;
  - Comprar;
  - Resgatar;

Banheiro:
  - CRUD;
  - BuscarPorLocalidade;

Usuario:
  - CRUD;
  - Login;
  - Logout;
  - LembrarSenha;
