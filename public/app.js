(function(){
    'use strict';

    angular.module('ignicontactos', ['ngCookies'])

        .controller('ContactosListController', function ContactosListController($http, $scope, $cookies){
            $scope.contactos = [];
            $scope.selectedContacto = {};
            $http.get('/' + $cookies.get('username') + '/contacto/todos').then(function(response){
                $scope.contactos = response.data;
            });
            $scope.verDetalle = function(data){
                $scope.selectedContacto = data;
            }
            $scope.$on('contactoCreado', function(event, data){
                $scope.contactos.push(data);
                $('#contacto-form').closeModal();
                Materialize.toast('Contacto creado exitosamente', 5000);
            });
            $scope.$on('contactoBorrado', function(event, data){
                Materialize.toast('Contacto borrado exitosamente', 5000);
                $scope.contactos = $scope.contactos.filter(function(elem){
                    return elem.id !== data;
                });
            });
        })
        .controller('ContactoDetalleController', function ContactoDetalleController($http, $scope, $cookies, $rootScope){
            $scope.borrarContacto = function(){
                $http({method: 'DELETE', url: '/' + $cookies.get('username') + '/contacto/' + $scope.selectedContacto.id})
                    .then(function(data){
                        $rootScope.$broadcast('contactoBorrado', $scope.selectedContacto.id);
                    });
            }
        })
        .controller('ContactoCrearController', function ContactoCrearController($http, $scope, $rootScope, $cookies){
            $scope.contacto = {};
            $scope.crearContacto = function(){
                $http.post('/' + $cookies.get('username') + '/contacto/nuevo', $scope.contacto)
                    .then(function(response){
                        console.log(response.data);
                        $rootScope.$broadcast('contactoCreado', $scope.contacto);
                        $scope.contacto = {};
                    });
            }
        });
})();
$(document).ready(function(){
    // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
    $('.modal-trigger').leanModal();
});
