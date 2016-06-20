(function(){
    'use strict';

    angular.module('ignicontactos', [])

        .controller('ContactosListController', function ContactosListController($http, $scope){
            $scope.contactos = [];
            $scope.selectedContacto = {};
            $http.get('/victorsamuelmd/contacto/todos').then(function(response){
                $scope.contactos = response.data;
            });
            $scope.verDetalle = function(data){
                $scope.selectedContacto = data;
            }
            $scope.$on('contactoCreado', function(event, data){
                $scope.contactos.push(data);
            });
        })
        .controller('ContactoDetalleController', function ContactoDetalleController($http, $scope){
            $scope.borrarContacto = function(){
                $http({method: 'DELETE', url: '/victorsamuelmd/contacto/' + $scope.selectedContacto.id})
                    .then(function(data){console.log(data);});
            }
        })
        .controller('ContactoCrearController', function ContactoCrearController($http, $scope, $rootScope){
            $scope.contacto = {};
            $scope.crearContacto = function(){
                $http.post('/victorsamuelmd/contacto/nuevo', $scope.contacto)
                    .then(function(response){
                        console.log(response.data);
                        $rootScope.$broadcast('contactoCreado', $scope.contacto);
                    });
            }
        });
})();
