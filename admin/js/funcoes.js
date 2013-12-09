function AdminCtrl($scope, $http, $window){
	
	var $ = jQuery;

	$scope.ativarMenu = function(menu){
		$scope.menu_ativo = menu;
	}

	$scope.login = function(){

		$http.post('../api/login', $scope.user).success(function(data){
			if(data.login.auth){

				$('#login').fadeOut(function(){
					$('#menu').removeClass('hide').fadeIn();
					//carregando os dados
					listarCustomer();
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

	$scope.viewUser = function() {
		$scope.menu_ativo = 3;
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

	var init = function(){

		$scope.menu_ativo = 1;

		$scope.ativo_user = 0;
		$scope.user_view = {id:0, nome:'', email:'', senha:''};
		$scope.user = {id:0, nome:'', email:'', senha:''};
		$scope.users = [];

		$scope.ativo_banheiro = 0;
		$scope.banheiro_view = {id:0, descricao:'', logradouto:'', numero:'', bairro:'', cep:'', cidade:'', uf:'', ativo:0};
		$scope.banheiro = {id:0, descricao:'', logradouto:'', numero:'', bairro:'', cep:'', cidade:'', uf:'', ativo:0};
		$scope.banheiros = [];

		$scope.ativo_customer = 0;
		$scope.customer = {id:0, nome:'', sobrenome:'', email:'', ativo:0};
		$scope.customer_view = {id:0, nome:'', sobrenome:'', email:'', ativo:0};
		$scope.customers = [];

		//checando login
		$http.get('../api/checkauth').success(function(data){
			if(data.auth != false){
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