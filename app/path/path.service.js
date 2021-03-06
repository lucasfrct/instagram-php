/*
 * Autor: Lucas Costa
 * Data: Maio de 2020
 */

(()=> {
    "user strict"

    angular
        .module("streamvideo")
        .service("path.service", ["$http", PathService])

    function PathService($http) {
        var that = this
        
        that.access = (path)=> {
            access = {path: path, action: "access"}
            return $http({method: "POST", url: "app/path/path.access.service.php", data: access})
        }
    }
}) ()