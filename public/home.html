<?php
session_start();

if(!isset($_SESSION['username'])){
    header('Location: login.php');
}
?><!doctype html>
<html lang="es">
    <head>
        <title>Ignicontactos</title>
        <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular.min.js"></script>
        <script src="https://code.angularjs.org/1.5.7/angular-cookies.min.js"></script>
        <link rel="stylesheet" href="styles.css">
    </head>
    <div class="pure-g">
        <div class="pure-u-1">
            <nav class="pure-menu pure-menu-horizontal">
                <a href="home.php" class="pure-menu-heading pure-menu-link">IngiContactos</a>
                <ul class="pure-menu-list">
                    <li class="pure-menu-item">Bienvenido <?php echo $_SESSION['username']; ?></li>
                    <li class="pure-menu-item"><a href="logout.php" class="pure-menu-link">Salir</a>
                </ul>
            </nav>
        </div>
    </div>

    <body ng-app="ignicontactos" class="pure-g">
        <div class="pure-u-1">
        </div>

        <div ng-controller="ContactosListController" class="pure-u-1">
            <input type="text" placeholder="Filtrar" ng-bind="filtro">
            <div class="pure-g">
            <div class="pure-u-1-6">
                <div class="pure-menu pure-menu-scrollable custom-restricted">
                <ul class="pure-menu-list">
                    <li ng-repeat="contacto in contactos | filter: filtro" class="pure-menu-item">
                        <a class="pure-menu-link" href="#" ng-click="verDetalle(contacto)">
                            {{contacto.nombres}} {{contacto.apellidos}}
                        </a>
                    </li>
                </ul>
                </div>
            </div>
            <div class="pure-u-5-6">
                <div ng-controller="ContactoDetalleController">
                    <div ng-if="selectedContacto.nombres">
                        <p>Nombres: {{selectedContacto.nombres}}</p>
                        <p>Apellidos: {{selectedContacto.apellidos}}</p>
                        <p>Telefono: {{selectedContacto.telefono}}</p>
                        <p>Email: {{selectedContacto.email}}</p>
                        <button ng-click="borrarContacto()" class="pure-button pure-button-secondary">
                            Borrar
                        </button>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <form ng-controller="ContactoCrearController" class="pure-form pure-form-stacked">
            <input ng-model="contacto.nombres" type="text" placeholder="Nombres">
            <input ng-model="contacto.apellidos" type="text" placeholder="Apellidos">
            <input ng-model="contacto.telefono" type="text" placeholder="telefono"> 
            <input ng-model="contacto.email" type="text" placeholder="email"> 
            <input ng-model="contacto.categoria" type="text" placeholder="categoria"> 
            <input ng-model="contacto.fecha_nacimiento" type="text" placeholder="fecha_nacimiento"> 
            <input ng-model="contacto.pais" type="text" placeholder="pais"> 
            <input ng-model="contacto.departamento" type="text" placeholder="departamento"> 
            <input ng-model="contacto.ciudad" type="text" placeholder="ciudad"> 
            <input ng-model="contacto.direccion" type="text" placeholder="direccion"> 
            <input ng-model="contacto.coordenadas" type="text" placeholder="coordenadas"> 
            <input ng-model="contacto.notas" type="text" placeholder="notas"> 
            <button ng-click="crearContacto()" class="pure-button pure-button-primary">Crear</button>
        </form>

        <script src="app.js"></script>
    </body>
</html>
