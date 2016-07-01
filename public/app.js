(function(){
    'use strict';

    angular.module('ignicontactos', ['ngCookies', 'ngMap', 'ngFileUpload', 'angular.filter', 'ngRoute'])
    /*
     * Servicio para manejar contactos, este es el encargado de realizar
     * las peticiones al servidor mediante protocolo http usando un patrón REST
     */
        .service('contactos', function Contactos($http, $cookies, Upload){
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
                return $http.post('/' + username + '/contacto/nuevo', data)
                    .then(function(response){
                        return response.data;
                    }, function(error) {
                        return error;
                    });
            };

            contactos.actualizarContacto = function(data){
                return $http.put('/' + username + '/contacto/' + data.id, data)
                    .then(function(response){
                        return response.data;
                    }, function(error) {
                        return error;
                    });
            };

            /*
             * Envia un peticion con metodo DELETE al servidor y actualiza la
             * vista si la petición es exitosa.
             */
            contactos.borrarContacto = function borrarContacto(id) {
                $http.delete('/' + username + '/contacto/' + id)
                    .then(function(){
                    });
            };

            contactos.obtenerImagen = function(name) {
                return $http.get('/imagen', {params: {name: name}})
                    .then(function(response) {
                        return response.data;
                    }, function(error) {
                        return error;
                    });
            }

            // TODO: Esta funcion no esta bien estructurada
            contactos.upload = function (file) {
                return new Promise(function(resolve, reject) {
                    Upload.upload({
                        url: '/' + username + '/images',
                        data: {file: file, 'username': username}
                    }).then(function(response){resolve(response.data)},
                        function(error){reject(error)})
                });
            }
        })


    /*
     * La función de este componente es mantener la vista de la aplicacion
     * sincronizada con el estado de los datos en el servidor.
     */
        .component('appRoot', {
            templateUrl: 'templates/app.component.html',
            controllerAs: 'app',
            controller: function(contactos, $cookies) {
                var app = this;

                app.username = $cookies.get('username');
                app.listaDeContactos = [];
                app.contactoSeleccionado = {};
                app.imagen = null;
                app.contactoEnEdicion = {};
                app.enEdicion = false;

                app.seleccionarContacto = function(contacto) {
                    app.imagen = null;
                    app.contactoSeleccionado = contacto;
                    if (contacto.imagen) {
                    contactos.obtenerImagen(contacto.imagen).then(
                            function(data) {
                                app.imagen = data.imagen;
                            }, function(error) {
                                console.log(error);
                                app.imagen = null;
                            });
                    }
                };

                app.borrarContacto = function borrarContacto(id) {
                    contactos.borrarContacto(id);
                    app.$onInit();
                    app.contactoSeleccionado = null;
                };

                app.iniciarEdicion = function(contacto) {
                    $('#contacto-form').openModal();
                    app.contactoEnEdicion = angular.copy(contacto);
                    app.enEdicion = true;
                };

                app.crear = function() {
                    $('#contacto-form').openModal();
                    app.contactoEnEdicion = {};
                    app.enEdicion = false;
                };

                app.crearContacto = function(contacto) {
                    $('#contacto-form').closeModal();
                    contactos.crearContacto(contacto).then(function(id) {
                        contacto.id = id;
                        app.listaDeContactos.push(contacto);
                        Materialize.toast('Contacto creado exitosamente', 4000);
                    }, function(error) {
                        Materialize.toast(error, 4000);
                    });
                }

                app.editarContacto = function(contacto) {
                    $('#contacto-form').closeModal();
                    if (angular.equals(app.contactoSeleccionado, app.contactoEnEdicion)) {
                        console.log("Son iguales");
                    } else {
                        contactos.actualizarContacto(contacto).then(function(data) {
                            app.contactoSeleccionado = data;
                            Materialize.toast('Actualizacion correcta', 4000);
                        }, function(error) {
                            Materialize.toast('Error al actualizar, si persiste comuniquese con el administrador del sistema', 4000);
                        });
                    }
                };

                app.subirImagen = function(file) {
                    var promise = contactos.upload(file);
                    var img;
                    promise.then(function(data) {
                        img = data.img;
                        app.contactoEnEdicion.imagen = img;
                        Materialize.toast('Imagen subida apropiadamente', 4000)
                    });
                }

                app.borrarContactos = function(listaParaBorrar) {
                    listaParaBorrar.forEach(function(id, index, lista) {
                        console.log('borrando contacto con id:' + id);
                        contactos.borrarContacto(id);
                    });
                    app.$onInit();
                };

                app.$onInit = function() {
                    Materialize.updateTextFields();
                     $('ul.tabs').tabs();
                    contactos.obtenerContactos()
                        .then(function(data) {
                            app.listaDeContactos = data;
                        });
                };
            }
        })

        /*
         * Este componente muestra los contactos en forma de lista y permite
         * ver los detalles de cada uno
         */
        .component('contactoLista', {
            templateUrl: 'templates/contacto-lista.component.html',
            controllerAs: 'lista',
            controller: function ContactosListaController() {
                var listaContactos = this;

                listaContactos.filtro = '';

                listaContactos.$onInit = function() {
                    console.log("Inicializado el componente de lista de Contactos");
                };

                listaContactos.seleccionar = function(contacto) {
                    listaContactos.alSeleccionar({contacto: contacto});
                };

            },
            bindings: {
                lista: '<',
                alSeleccionar: '&'
            }
        })


        /*
         * Recibe lo datos del contacto del cual se quieren ver los detalles
         */
        .component('contactoDetalle', {
            controllerAs: 'cnt',
            templateUrl: 'templates/contacto-detalle.component.html',
            controller: function ContactoDetalleController(){
                var detalle = this;


                detalle.borrar = function(contacto) {
                    detalle.alBorrar({id: contacto.id});
                };

                detalle.editar = function editarContacto(contacto) {
                    detalle.alEditar({contacto: contacto});
                };

                detalle.$onChanges = function (changesObj) {
                    detalle.contacto = changesObj.contacto.currentValue;
                };
            },
            bindings: {
                contacto: '<',
                imagen: '<',
                alEditar: '&',
                alBorrar: '&'
            }
        })


        /*
         * Es usado tanto como para crear nuevos contactos como para editar
         * los que ya están creados.
         */
        .component('contactoFormulario', {
            templateUrl: 'templates/contacto.formulario.html',
            controllerAs: 'formulario',
            controller: function (){
                var formulario = this;
                formulario.crear = function(contacto) {
                    if (!formulario.contacto.nombres) {
                        formulario.error = 'El nombre no puede estar vacío';
                    } else {
                        formulario.alCrear({contacto: contacto});
                        console.log("Funcion llamada con: ", contacto);
                    }
                };

                formulario.editar = function(contacto) {
                    if (!formulario.contacto.nombres) {
                        Materialize.toast('El nombre de contacto no puede ser vacío');
                    } else {
                        formulario.alActualizar({contacto: contacto});
                        formulario.contacto = {};
                    }
                };

                formulario.$onInit = function() {
                };

                formulario.seleccionarImg = function(file) {
                    formulario.alSeleccionarImg({file: file});
                };
            },
            bindings: {
                contacto: '<',
                modoEditar: '<',
                alCrear: '&',
                alActualizar: '&',
                alSeleccionarImg: '&'
            }
        })
        .component('contactoTabla', {
            templateUrl: 'templates/contactos-tabla.component.html',
            controllerAs: 'tabla',
            controller: function() {
                var tabla = this;
               tabla.filtro = ''; 
               tabla.borrar = function() {
                   var listaParaBorrar = tabla.contactos.filter(function(contacto) {
                       return contacto.paraBorrar;
                   }).map(function(contacto) {
                       return contacto.id;
                   });
                   tabla.borrarContactos({lista: listaParaBorrar});
               }
               tabla.$onInit = function() {
                    Materialize.updateTextFields();
               };
            },
            bindings: {
                contactos: '<',
                borrarContactos: '&'
            }
        });
})();
