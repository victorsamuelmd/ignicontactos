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

    /*
     * Controlador que muestra la lista de contactos obtenidos del servidor,
     * esto se actualiza automáticamente usando el método descrito en
     * http://stackoverflow.com/questions/19744462/update-scope-value-when-service-data-is-changed
     * a la vez que tiene mejor eficiencia.
     */
        .controller('ContactosListaController', function ContactosListaController(contactos){
            var listaContactos = this;

            listaContactos.model = contactos.model;
            listaContactos.crear = false;

            listaContactos.verDetalle = function(data){
                contactos.seleccionarContacto(data);
            };

            contactos.obtenerContactos();
        })


        .controller('ContactoDetalleController', function ContactoDetalleController(contactos){
            var detalle = this;

            detalle.model = contactos.model;
            detalle.editar = true;

            detalle.borrarContacto = function borrarContacto(id) {
                contactos.borrarContacto(id);
            };
            detalle.editarContacto = function editarContacto() {
               detalle.editar = false; 
            };

        })


        .component('formularioContacto', {
            templateUrl: 'templates/contacto.formulario.html',
            controller: function (){
                var formulario = this;
            },
            bindings: {
                contacto: '='
            }
        });
})();
