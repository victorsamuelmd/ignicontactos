(function(){
    'use strict';

    angular.module('ignicontactos', ['ngCookies', 'ngMap', 'ngFileUpload'])
    /*
     * Servicio para manejar contactos, este es el encargado de realizar
     * las peticiones al servidor mediante protocolo http usando un patrón REST
     */
        .service('contactos', function Contactos($http, $cookies){
            var contactos = this;

            contactos.model = {contactoSeleccionado: {}, lista: []};

            contactos.obtenerContactos = function obtenerContactos() {
                $http.get('/' + $cookies.get('username') + '/contacto/todos')
                    .then(function(response){
                        contactos.model.lista = response.data;
                    }, function(reason){
                        console.log(reason.data);
                    });
            };

            contactos.crearContacto = function crearContacto(data) {
                $http.post('/' + $cookies.get('username') + '/contacto/nuevo', data)
                    .then(function(response){
                        data.id = response.data.id;
                        contactos.model.lista.push(data);
                    });
            };

            contactos.actualizarContacto = function(data){
                $http.put('/' + $cookies.get('username') + '/contacto/' + data.id, data)
                    .then(function(response){
                        $('#contacto-form').closeModal();
                    });
            };

            /*
             * Envia un peticion con metodo DELETE al servidor y actualiza la
             * vista si la petición es exitosa.
             */
            contactos.borrarContacto = function borrarContacto(id) {
                $http.delete('/' + $cookies.get('username') + '/contacto/' + id)
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

            contactos.upload = function (file) {
                Upload.upload({
                    url: '/' + $cookies.get('username') + '/images',
                    data: {file: file, 'username': $scope.username}
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

            contactos.obtenerContactos();

            listaContactos.verDetalle = function(data){
                contactos.seleccionarContacto(data);
            };
        })


        .controller('ContactoDetalleController', function ContactoDetalleController(contactos){
            var detalle = this;

            detalle.model = contactos.model;

            detalle.borrarContacto = function borrarContacto(id) {
                contactos.borrarContacto(id);
            };

        })


        .controller('ContactoCrearController', function ContactoCrearController(contactos){
            var formulario = this;

            formulario.editar = false;
            formulario.model = contactos.model;
            formulario.contacto = {};

            formulario.crearContacto = function crearContacto(contacto) {
                contactos.crearContacto(contacto);
                formulario.contacto = {};
            };
        });
})();
$(document).ready(function(){
    // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
    $('.modal-trigger').leanModal();
});
