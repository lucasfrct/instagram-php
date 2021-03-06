/*
 * modal.directive.js
 * Autor: Lucas Costa
 * Data: Janeiro de 2020
 */
(()=> { 
	"use strict"

	angular
		.module("streamvideo")
		.directive("modal", ModalDirective)

	// funcao para caregar o modulo de modais na pagina
	function ModalDirective(){	
		return {
			restrict: "A",																// escopo somente para atributos
			scope: { modal: '=' }, 												// captura o objeto no atributo modal
			link: ModalInit,															// funcao auto executada
		}

		function ModalInit($scope, element) {
			element.addClass("modal")												// add classe css modal
			var modalInstance = M.Modal.init(element[0], { preventScrolling: true, dismissible: false })

			$scope.$watch('modal', function(status) {
				(status) ? modalInstance.open() : modalInstance.close();
			})
			
		}
	}
})()