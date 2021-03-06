(()=> {
    
    angular
        .module("instagramlivekey")
        .service("streamkey.service", [ "$http", streamController])

    function streamController($http) {
        var that = this

        that.stream = (login)=> {
            var data = angular.copy(login)
            data.action    = "streamkey"
            data.password  = btoa(data.password)
            return $http({url: "src/streamkey.service.php", method: "POST", data: data})
        }

        that.code = (login)=> {
            var data = angular.copy(login)
            data.action    = "verificartion"
            data.code      = btoa(data.code)
            return $http({url: "src/code.service.php", method: "POST", data: data})
        }
    }

}) ()