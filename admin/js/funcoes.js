function AdminCtrl($scope, $http, $window){
	
	var $ = jQuery;

	$scope.visualizar = function($index, contato){
		$scope.contato_view = contato;
		$scope.ativo = $index;
	}

	$scope.login = function(){

		$http.post('../api/login', $scope.user).success(function(data){
			if(data.login.auth){

				$('#login').fadeOut(function(){
					$('#menu').removeClass('hide').fadeIn();
				});
				//carregando os dados
				listarCustomer();

			}else{
				$(".alert").addClass('alert-warning');
				$("#msg_alert").html('Usuário inválido!');
				$(".alert").alert();
			}
			
		});
	}

	$scope.logout = function(){

		$http.get('../api/logout').success(function(data){
			if(data.logout){
				$('#login').fadeIn(function(){
					$('#menu, #customer, #revista').addClass('hide');
				});
			}
		});
	}

	$scope.viewCustomer = function() {
		listarCustomer();
	}

	$scope.viewBanheiro = function() {
		listarBanheiro();
	}

	$scope.viewUser = function() {
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

	$scope.salvarRevista = function(){

		$scope.revista_view.imagem_capa = $('#imagem_capa').val();

		if($scope.revista_view.id_revista > 0){

			$http.put('../api/revista/'+$scope.revista_view.id_revista, $scope.revista_view).success(function(data){
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-warning');
				$("#msg_alert").html('Alterado com sucesso!');
			
			});

		}else{

			$http.post('../api/revista', $scope.revista_view).success(function(data){
				$scope.revistas.unshift(data);
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-success');
				$("#msg_alert").html('Cadastrado com sucesso!');
				
			});

		}
		$('#modalCadastro').modal('hide');
		$(".alert").css({display: ''}).fadeOut(5000);
	}

	$scope.deletarRevista = function(){

		if(confirm('Deseja realmente excluir este registro?') && $scope.revista_view.id_revista > 0){

			$http.delete('../api/revista/'+$scope.revista_view.id_revista, $scope.revista_view).success(function(data){
				$(".alert").css({display: 'block'});
				$(".alert").addClass('alert-warning');
				$("#msg_alert").html('Removido com sucesso!');
				$(".alert").css({display: ''}).fadeOut(5000);
			});

		}
		
	}

	$scope.visualizarRevista = function($index, revista){
		$scope.revista_view = revista;
		$scope.ativo_revista = $index;
	}

	var init = function(){

		$scope.ativo = 0;
		$scope.user_view = {id:0, nome:'', email:'', senha:''};
		$scope.user = {id:0, nome:'', email:'', senha:''};
		$scope.users = [];

		$scope.banheiro_view = {id:0, descricao:'', logradouto:'', numero:'', bairro:'', cep:'', cidade:'', uf:'', ativo:0};
		$scope.banheiro = {id:0, descricao:'', logradouto:'', numero:'', bairro:'', cep:'', cidade:'', uf:'', ativo:0};
		$scope.banheiros = [];

		$scope.customer = {id:0, nome:'', sobrenome:'', link:'', email:'', ativo:0};
		$scope.customer_view = {id:0, nome:'', sobrenome:'', link:'', email:'', ativo:0};
		$scope.customers = [];
		$scope.ativo_customer = 0;

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