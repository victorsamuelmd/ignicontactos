(function(){
    'use strict';

    angular.module('ignicontactos', ['ngCookies', 'ngMap', 'ngFileUpload'])

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
                $scope.selectedContacto = {};
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
            $scope.editarContacto = function(){
                $rootScope.$broadcast('editarContacto', $scope.selectedContacto);
            }
        })
        .controller('ContactoCrearController', function ContactoCrearController($http, $scope, $rootScope, $cookies, Upload){
            $scope.contacto = {};
            $scope.crearContacto = function(){
                $http.post('/' + $cookies.get('username') + '/contacto/nuevo', $scope.contacto)
                    .then(function(response){
                        console.log(response.data);
                        $rootScope.$broadcast('contactoCreado', $scope.contacto);
                        $scope.contacto = {};
                    });
            }
            $scope.guardarCambios = function(){
                $http({method: 'PUT', url: '/' + $cookies.get('username') + '/contacto/' + $scope.contacto.id, data: $scope.contacto})
                    .then(function(response){
                        $('#contacto-form').closeModal();
                        $scope.contacto = {};
                    });
            }
            $scope.$on('editarContacto', function(event, data) {
                $('#contacto-form').openModal();
                $scope.contacto = data;
                console.log(data);
            });
            $scope.upload = function (file) {
                Upload.upload({
                    url: '/' + $cookies.get('username') + '/images',
                    data: {file: file, 'username': $scope.username}
                }).then(function (resp) {
                    console.log('Success ' + resp.config.data.file.name + 'uploaded. Response: ' + resp.data);
                }, function (resp) {
                    console.log('Error status: ' + resp.status);
                }, function (evt) {
                    var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                    console.log('progress: ' + progressPercentage + '% ' + evt.config.data.file.name);
                });
            };
        });
})();
$(document).ready(function(){
    // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
    $('.modal-trigger').leanModal();
});
