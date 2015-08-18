function AdminCtrl($scope, $http, $window){
	
	var $ = jQuery;

	$scope.ativarMenu = function(menu){
		$scope.menu_ativo = menu;
	}

	$scope.login = function(){

		$http.post('../api/login', $scope.user).success(function(data){
			if(data.id > 0){

				if(data.tipo == 2){
					$scope.permissao_cliente = false;
					$scope.permissao_locais = false;
					$scope.permissao_produtos = false;
					$scope.permissao_vendas = true;
					$scope.permissao_usuarios = false;
					
					
				}else{
					$scope.ativo_venda = 1;
				}

				$scope.ativo_venda = 1;
				$('#login').fadeOut(function(){
						$('#menu').removeClass('hide').fadeIn();
						//carregando os dados
						listarVenda();
					});

			}else{
				$(".alert").addClass('alert-warning');
				$("#msg_alert").html('Usuário inválido!');
				$(".alert").alert();
				$(".alert").css({display: ''}).fadeOut(5000);
			}
			
		});
	}

	$scope.logout = function(){

		$http.get('../api/logout').success(function(data){
			if(data.logout){
				$('.tela, #menu').addClass('hide');
				$('#login').removeClass('hide').fadeIn();
			}
		});
	}

	$scope.viewCustomer = function() {
		$scope.menu_ativo = 1;
		listarCustomer();
	}

	$scope.viewBanheiro = function() {
		$scope.menu_ativo = 2;
		listarBanheiro();
	}

	$scope.viewProduto = function() {
		$scope.menu_ativo = 3;
		listarProduto();
	}

	$scope.viewVenda = function() {
		$scope.menu_ativo = 4;
		listarVenda();
	}

	/*$scope.viewPonto = function() {
		$scope.menu_ativo = 6;
		listarPonto();
	}*/

	$scope.viewUser = function() {
		$scope.menu_ativo = 9;
		listarUser();
	}

	var listarCustomer = function () {
		$http.get('../api/customer').success(function(data){
			//console.log(data.error);
			$scope.customers = data;

			$('.tela').addClass('hide');
			$('#customer').removeClass('hide').fadeIn();
		});
	}

	var listarBanheiro = function () {
		$http.get('../api/banheiro').success(function(data){
			//console.log(data.error);
			$scope.banheiros = data;

			$('.tela').addClass('hide');
			$('#banheiro').removeClass('hide').fadeIn();
		});
	}

	var listarProduto = function () {
		$http.get('../api/produto').success(function(data){
			//console.log(data.error);
			$scope.produtos = data;

			$('.tela').addClass('hide');
			$('#produto').removeClass('hide').fadeIn();
		});
	}

	var listarVenda = function () {
		$http.get('../api/venda').success(function(data){
			//console.log(data.error);
			$scope.vendas = data;

			$('.tela').addClass('hide');
			$('#venda').removeClass('hide').fadeIn();
		});
	}

	var listarVendaProduto = function (id_venda) {
		$http.get('../api/vendaprodutos/'+id_venda).success(function(data){
			//console.log(data.error);
			$scope.vendaprodutos = data;
		});
	}

	/*var listarPonto = function () {
		$http.get('../api/ponto').success(function(data){
			//console.log(data.error);
			$scope.pontos = data;

			$('.tela').addClass('hide');
			$('#ponto').removeClass('hide').fadeIn();
		});
	}*/

	var listarUser = function () {
		$http.get('../api/user').success(function(data){
			//console.log(data.error);
			$scope.users = data;

			$('.tela').addClass('hide');
			$('#user').removeClass('hide').fadeIn();
		});
	}

	$scope.visualizarCustomer = function($index, customer){
		$scope.customer_view = customer;
		$scope.ativo_customer = $index;
	}

	$scope.visualizarBanheiro = function($index, banheiro){
		$scope.banheiro_view = banheiro;
		$scope.ativo_banheiro = $index;
	}

	$scope.visualizarProduto = function($index, produto){
		$scope.produto_view = produto;
		$scope.ativo_produto = $index;
	}

	$scope.visualizarVenda = function($index, venda){
		$scope.venda_view = venda;
		$scope.ativo_venda = $index;
		listarVendaProduto(venda.id);
	}

	/*$scope.visualizarPonto = function($index, ponto){
		$scope.ponto_view = ponto;
		$scope.ativo_ponto = $index;
	}*/

	$scope.visualizarUser = function($index, user){
		$scope.user_view = user;
		$scope.ativo_user = $index;
	}


	$scope.salvarCustomer = function(){

		if($scope.customer_view.id > 0){

			$http.put('../api/customer/'+$scope.customer_view.id, $scope.customer_view).success(function(data){
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-warning');
				$("#msg_alert").html('Alterado com sucesso!');
			
			});

		}else{

			$http.post('../api/customer', $scope.customer_view).success(function(data){
				$scope.customers.unshift(data);
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-success');
				$("#msg_alert").html('Cadastrado com sucesso!');
				
			});

		}
		$('#modalCadastro').modal('hide');
		$(".alert").css({display: ''}).fadeOut(5000);
	}

	$scope.salvarBanheiro = function(){

		if($scope.banheiro_view.id > 0){

			$http.put('../api/banheiro/'+$scope.banheiro_view.id, $scope.banheiro_view).success(function(data){
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-warning');
				$("#msg_alert").html('Alterado com sucesso!');
			
			});

		}else{

			$http.post('../api/banheiro', $scope.banheiro_view).success(function(data){
				$scope.banheiros.unshift(data);
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-success');
				$("#msg_alert").html('Cadastrado com sucesso!');
				
			});

		}
		$('#modalCadastro').modal('hide');
		$(".alert").css({display: ''}).fadeOut(5000);
	}

	$scope.salvarProduto = function(){

		if($scope.produto_view.id > 0){

			$http.put('../api/produto/'+$scope.produto_view.id, $scope.produto_view).success(function(data){
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-warning');
				$("#msg_alert").html('Alterado com sucesso!');
			
			});

		}else{

			$http.post('../api/produto', $scope.produto_view).success(function(data){
				$scope.produtos.unshift(data);
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-success');
				$("#msg_alert").html('Cadastrado com sucesso!');
				
			});

		}
		$scope.produto_view = {id:0, descricao:'', valor:0, ativo:0};
		$('#modalCadastro').modal('hide');
		$(".alert").css({display: ''}).fadeOut(5000);
	}

	$scope.resgatar = function(id_venda){

		if(confirm('Deseja realmente resgatar este Crédito?') && id_venda > 0){

			$http.put('../api/vendaresgate/'+id_venda).success(function(data){
				if(data == 'true'){
					$(".alert").css({display: 'block'});
					$(".alert").addClass('alert-warning');
					$("#msg_alert").html('Venda resgatada com sucesso!');

					listarVenda();
				}else{
					alert('Aviso: Resgate não realizado. Tente novamente.');
				}
			
			});

		}else{

			if(id_venda <= 0){
				alert('Aviso: Selecione uma vendas antes de resgatá-la.');
			}

		}
	}

	/*$scope.salvarPonto = function(){

		if($scope.ponto_view.id > 0){

			$http.put('../api/ponto/'+$scope.ponto_view.id, $scope.ponto_view).success(function(data){
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-warning');
				$("#msg_alert").html('Alterado com sucesso!');
			
			});

		}else{

			$http.post('../api/ponto', $scope.ponto_view).success(function(data){
				$scope.pontos.unshift(data);
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-success');
				$("#msg_alert").html('Cadastrado com sucesso!');
				
			});

		}
		$scope.ponto_view = {id:0, descricao:'', logradouto:'', numero:'', bairro:'', cep:'', cidade:'', uf:'', ativo:0};
		$('#modalCadastro').modal('hide');
		$(".alert").css({display: ''}).fadeOut(5000);
	}*/

	$scope.salvarUser = function(){

		if($scope.user_view.id > 0){

			$http.put('../api/user/'+$scope.user_view.id, $scope.user_view).success(function(data){
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-warning');
				$("#msg_alert").html('Alterado com sucesso!');
			
			});

		}else{

			$http.post('../api/user', $scope.user_view).success(function(data){
				$scope.users.unshift(data);
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-success');
				$("#msg_alert").html('Cadastrado com sucesso!');
				
			});

		}
		$('#modalCadastro').modal('hide');
		$(".alert").css({display: ''}).fadeOut(5000);
	}

	$scope.deletarCustomer = function(){

		if(confirm('Deseja realmente excluir este registro?') && $scope.customer_view.id > 0){

			$http.delete('../api/customer/'+$scope.customer_view.id, $scope.customer_view).success(function(data){
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-warning');
				$("#msg_alert").html('Removido com sucesso!');
				$(".alert").css({display: ''}).fadeOut(5000);
			});

		}
		
	}

	$scope.deletarBanheiro = function(){

		if(confirm('Deseja realmente excluir este registro?') && $scope.banheiro_view.id > 0){

			$http.delete('../api/banheiro/'+$scope.banheiro_view.id, $scope.banheiro_view).success(function(data){
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-warning');
				$("#msg_alert").html('Removido com sucesso!');
				$(".alert").css({display: ''}).fadeOut(5000);
			});

		}
		
	}

	$scope.deletarProduto = function(){

		if(confirm('Deseja realmente excluir este registro?') && $scope.produto_view.id > 0){

			$http.delete('../api/produto/'+$scope.produto_view.id, $scope.produto_view).success(function(data){
				$scope.produto_view = {id:0, descricao:'', valor:0, ativo:0};
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-warning');
				$("#msg_alert").html('Removido com sucesso!');
				$(".alert").css({display: ''}).fadeOut(5000);
			});

		}
		
	}

	/*$scope.deletarPonto = function(){

		if(confirm('Deseja realmente excluir este registro?') && $scope.ponto_view.id > 0){

			$http.delete('../api/ponto/'+$scope.ponto_view.id, $scope.ponto_view).success(function(data){
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-warning');
				$("#msg_alert").html('Removido com sucesso!');
				$(".alert").css({display: ''}).fadeOut(5000);
				$scope.ponto_view = {id:0, descricao:'', logradouto:'', numero:'', bairro:'', cep:'', cidade:'', uf:'', ativo:0};
			});

		}
		
	}*/

	$scope.deletarUser = function(){

		if(confirm('Deseja realmente excluir este registro?') && $scope.user_view.id > 0){

			$http.delete('../api/user/'+$scope.user_view.id, $scope.user_view).success(function(data){
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-warning');
				$("#msg_alert").html('Removido com sucesso!');
				$(".alert").css({display: ''}).fadeOut(5000);
			});

		}
		
	}

	$scope.formatarStatus = function(status){
		var retorno = '';
		switch(parseInt(status))
		{
		case 1:
            retorno = 'Aguardando Pagamento';
            break;
        case 2:
            retorno = 'Em Análise';
            break;
        case 3:
            retorno = 'Paga';
            break;
        case 4:
            retorno = 'Disponível';
            break;
        case 5:
            retorno = 'Em Disputa';
            break;
        case 6:
            retorno = 'Devolvida';
            break;
        case 7:
            retorno = 'Cancelada';
            break;
		default:
		  retorno = 'Aguardando Pagamento';
		}

		return retorno;
	}

	var init = function(){

		$scope.permissao_cliente = true;
		$scope.permissao_locais = true;
		$scope.permissao_produtos = true;
		$scope.permissao_vendas = true;
		$scope.permissao_usuarios = true;

		$scope.menu_ativo = 1;

		$scope.ativo_user = 0;
		$scope.user_view = {id:0, nome:'', email:'', senha:'', tipo:1, ativo:0};
		$scope.user = {id:0, nome:'', email:'', senha:'', tipo:1, ativo:0};
		$scope.users = [];

		$scope.ativo_banheiro = 0;
		$scope.banheiro_view = {id:0, descricao:'', logradouto:'', numero:'', bairro:'', cep:'', cidade:'', uf:'', latitude:'', longitude:'', tipo:'', ativo:0};
		$scope.banheiro = {id:0, descricao:'', logradouto:'', numero:'', bairro:'', cep:'', cidade:'', uf:'', latitude:'', longitude:'', tipo:'', ativo:0};
		$scope.banheiros = [];

		$scope.ativo_customer = 0;
		$scope.customer = {id:0, nome:'', sobrenome:'', email:'', sexo:'', celular:'', cpf:'', ativo:0};
		$scope.customer_view = {id:0, nome:'', sobrenome:'', email:'', sexo:'', celular:'', cpf:'', ativo:0};
		$scope.customers = [];

		$scope.ativo_produto = 0;
		$scope.produto_view = {id:0, descricao:'', valor:0, ativo:0};
		$scope.produto = {id:0, descricao:'', valor:0, ativo:0};
		$scope.produtos = [];

		$scope.ativo_venda = 0;
		$scope.venda_view = {id:0, valor_total:0, data_cadastro:'', cliente:'', id_customer:0, negociacao_status:1};
		$scope.venda = {id:0, valor_total:0, data_cadastro:'', cliente:'', id_customer:0, negociacao_status:1};
		$scope.vendas = [];

		$scope.vendaprodutos = [];

		/*$scope.ativo_ponto = 0;
		$scope.ponto_view = {id:0, descricao:'', logradouto:'', numero:'', bairro:'', cep:'', cidade:'', uf:'', latitude:'', longitude:'', ativo:0};
		$scope.ponto = {id:0, descricao:'', logradouto:'', numero:'', bairro:'', cep:'', cidade:'', uf:'', latitude:'', longitude:'', ativo:0};
		$scope.pontos = [];*/

		//checando login
		$http.get('../api/checkauth').success(function(data){
			if(data.id > 0){
				//carregando os dados
				listarCustomer();

				$('#login').fadeOut(function(){
					$('#menu, #customer').removeClass('hide').fadeIn();
				});
			}else{
				$('.tela').addClass('hide');
				$('#login').removeClass('hide').fadeIn();
				
			}
		});
	};

	init();

}