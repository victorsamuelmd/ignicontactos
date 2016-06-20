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
            });
        })
        .controller('ContactoDetalleController', function ContactoDetalleController($http, $scope, $cookies){
            $scope.borrarContacto = function(){
                $http({method: 'DELETE', url: '/' + $cookies.get('username') + '/contacto/' + $scope.selectedContacto.id})
                    .then(function(data){console.log(data);});
            }
        })
        .controller('ContactoCrearController', function ContactoCrearController($http, $scope, $rootScope, $cookies){
            $scope.contacto = {};
            $scope.crearContacto = function(){
                $http.post('/' + $cookies.get('username') + '/contacto/nuevo', $scope.contacto)
                    .then(function(response){
                        console.log(response.data);
                        $rootScope.$broadcast('contactoCreado', $scope.contacto);
                    });
            }
        });
})();
