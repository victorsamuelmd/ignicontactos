(function(){
    'use strict';

    angular.module('ignicontactos', ['ngCookies', 'ngMap', 'ngFileUpload'])
    /*
     * Servicio para manejar contactos, este es el encargado de realizar
     * las peticiones al servidor mediante protocolo http usando un patrón REST
     */
        .service('contactos', function Contactos($http, $cookies){
            var contactos = this,
                username = $cookies.get('username');

            contactos.obtenerContactos = function obtenerContactos() {
                return $http.get('/' + username + '/contacto/todos')
                    .then(function(response){
                        return response.data;
                    }, function(reason){
                        console.log(reason.data);
                    });
            };

            contactos.crearContacto = function crearContacto(data) {
                $http.post('/' + username + '/contacto/nuevo', data)
                    .then(function(response){
                        data.id = response.data.id;
                        contactos.model.lista.push(data);
                        contactos.model.nuevo = {};
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
        .component('appRoot', {
            templateUrl: 'templates/app.component.html',
            controllerAs: 'app',
            controller: function(contactos) {
                var app = this;

                app.$onInit = function() {
                    console.log('Inicializado el componente de la aplicación');
                }
            }
        })

        .component('contactoLista', {
            templateUrl: 'templates/contacto-lista.component.html',
            controllerAs: 'lista',
            controller: function ContactosListaController(contactos){
                var listaContactos = this;

                listaContactos.lista = [];
                listaContactos.filtro = '';
                listaContactos.contactoSeleccionado = {};

                listaContactos.verDetalle = function(data){
                    listaContactos.contactoSeleccionado = data;
                };

                listaContactos.crearContacto = function(contacto) {
                    contactos.crearContacto(contacto);
                }

                listaContactos.$onInit = function() {
                    console.log("Inicializado el componente de lista de Contactos");
                    contactos.obtenerContactos().then(function(data) {
                        listaContactos.lista = data;
                    });
                }

            }
        })


        .component('contactoDetalle', {
            controllerAs: 'cnt',
            templateUrl: 'templates/contacto-detalle.component.html',
            controller: function ContactoDetalleController(contactos){
                var detalle = this;

                detalle.editar = true;
                detalle.contacto = {};

                detalle.borrarContacto = function borrarContacto(id) {
                    contactos.borrarContacto(id);
                };
                detalle.editarContacto = function editarContacto() {
                    detalle.editar = false; 
                };

                detalle.$onChanges = function (changesObj) {
                    detalle.contacto = changesObj.contacto.currentValue;
                }
            },
            bindings: {
                contacto: '<',
                cntEditar: '&',
                cntBorrar: '&'
            }
        })


        .component('contactoFormulario', {
            templateUrl: 'templates/contacto.formulario.html',
            controllerAs: 'formulario',
            controller: function (){
                var formulario = this;
                formulario.crear = function(contacto) {
                    formulario.onCreate(contacto);
                    console.log("Funcion llamada con: ", contacto);
                }
            },
            bindings: {
                contacto: '<',
                onCreate: '&',
                onUpdate: '&'
            }
        })

        .component('contactoTabla', {
            templateUrl: 'templates/contactos-tabla.component.html',
            controllerAs: 'tabla',
            controller: function() {
                
            },
            bindings: {
                contactos: '<'
            }
        });
})();
