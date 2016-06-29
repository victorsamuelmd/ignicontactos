(function(){
    'use strict';

    angular.module('ignicontactos', ['ngMaterial', 'ngMessages', 'ngCookies', 'ngMap', 'ngFileUpload'])
    /*
     * Servicio para manejar contactos, este es el encargado de realizar
     * las peticiones al servidor mediante protocolo http usando un patrón REST
     */
        .service('contactos', function Contactos($http, $cookies){
            var contactos = this,
                username = $cookies.get('username');

            contactos.model = {contactoSeleccionado: {}, lista: []};

            contactos.obtenerContactos = function obtenerContactos() {
                $http.get('/' + username + '/contacto/todos')
                    .then(function(response){
                        contactos.model.lista = response.data;
                    }, function(reason){
                        console.log(reason.data);
                    });
            };

            contactos.crearContacto = function crearContacto(data) {
                $http.post('/' + username + '/contacto/nuevo', data)
                    .then(function(response){
                        data.id = response.data.id;
                        contactos.model.lista.push(data);
                    });
            };

            contactos.actualizarContacto = function(data){
                $http.put('/' + username + '/contacto/' + data.id, data)
                    .then(function(response){
                        // TODO
                    });
            };

            /*
             * Envia un peticion con metodo DELETE al servidor y actualiza la
             * vista si la petición es exitosa.
             */
            contactos.borrarContacto = function borrarContacto(id) {
                $http.delete('/' + username + '/contacto/' + id)
                    .then(function(){
                        contactos.model.lista = contactos.model.lista.filter(function(element){
                            return element.id !== id;
                        });
                        contactos.model.contactoSeleccionado = {};
                    });
            };

            contactos.seleccionarContacto = function seleccionarContacto(data) {
                contactos.model.contactoSeleccionado = data;
            };

            // TODO: Esta funcion no esta bien estructurada
            contactos.upload = function (file) {
                Upload.upload({
                    url: '/' + username + '/images',
                    data: {file: file, 'username': username}
                }).then(function (resp) {
                    $scope.contacto.imagen = resp.data.img;
                }, function (resp) {
                    console.log('Error status: ' + resp.status);
                }, function (evt) {
                    var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                    console.log('progress: ' + progressPercentage + '% ' + evt.config.data.file.name);
                });
            };
        })
        .controller('AppController', function AppController() {
            var app = this;
            app.borrarContacto = function(contacto) {
                console.log(contacto); 
            };
            app.editarContacto = function(contacto) {
                console.log(contacto); 
            };
        })

    /*
     * Controlador que muestra la lista de contactos obtenidos del servidor,
     * esto se actualiza automáticamente usando el método descrito en
     * http://stackoverflow.com/questions/19744462/update-scope-value-when-service-data-is-changed
     * a la vez que tiene mejor eficiencia.
     */
        .component('contactosLista', {
            templateUrl: 'templates/contactos-lista.component.html',
            controller: ContactosListaController,
            controllerAs: 'lista'
        })
        .component('contactoDetalle', {
            templateUrl: 'templates/contacto-detalle.component.html',
            controller: ContactoDetalleController,
            bindings: {
                contacto: '<',
                imagen: '=',
                onDelete: '&',
                onUpdate: '&'
            }
        })

        .component('contactoFormulario', {
            templateUrl: 'templates/contacto.formulario.html',
            controller: function (){
                var formulario = this;
            },
            controllerAs: 'formulario',
            bindings: {
                contacto: '=',
            }
        });

    function ContactosListaController(contactos, $mdDialog, $mdMedia, $scope){
        var listaContactos = this;

        listaContactos.model = contactos.model;
        listaContactos.crear = false;
        listaContactos.seleccionado = null;

        listaContactos.verDetalle = function(data){
            listaContactos.seleccionado = data;
            console.log(data);
        };

        listaContactos.borrarContacto = function(contacto) {
            contactos.borrarContacto(contacto.id);
        };

        contactos.obtenerContactos();

        $scope.status = '  ';
        $scope.customFullscreen = $mdMedia('xs') || $mdMedia('sm');
        listaContactos.showAlert = function(ev) {
            // Appending dialog to document.body to cover sidenav in docs app
            // Modal dialogs should fully cover application
            // to prevent interaction outside of dialog
            $mdDialog.show(
                $mdDialog.alert()
                .parent(angular.element(document.querySelector('#popupContainer')))
                .clickOutsideToClose(true)
                .title('This is an alert title')
                .textContent('You can specify some description text in here.')
                .ariaLabel('Alert Dialog Demo')
                .ok('Got it!')
                .targetEvent(ev)
            );
        };

        listaContactos.editarContacto = function(ev, contacto) {
            var useFullScreen = ($mdMedia('sm') || $mdMedia('xs'))  && $scope.customFullscreen;
            $mdDialog.show({
                controller: DialogController,
                controllerAs: 'formulario',
                templateUrl: 'templates/contacto-formulario.component.html',
                parent: angular.element(document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                fullscreen: true
            })
            .then(function(answer) {
                console.log("Dio una respuesta");
            }, function() {
                console.log("No dio respuesta");
            });

        $scope.$watch(function() {
            return $mdMedia('xs') || $mdMedia('sm');
        }, function(wantsFullScreen) {
            $scope.customFullscreen = (wantsFullScreen === true);
        });
        };
    }

    function ContactoDetalleController(){
        var contacto = this;
    }

    function DialogController($scope, $mdDialog, contactos) {
        var dialog = this;
        $scope.hide = function() {
            $mdDialog.hide();
        };
        dialog.cancel = function() {
            $mdDialog.cancel();
        };
        $scope.answer = function(answer) {
            $mdDialog.hide(answer);
        };
        dialog.crearContacto = function(contacto) {
            contactos.crearContacto(contacto); 
            $mdDialog.hide();
        };
    }

})();
