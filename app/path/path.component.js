/*
 * Autor: Lucas Costa
 * Data: Maio de 2020
 */

( ()=> {
    "use stric"

    angular
        .module("streamvideo")
        .component("path", {
            templateUrl: "app/path/path.html",
            controller: [ "$rootScope", "$scope", "$document", "path.service", PathController ]
        })

    function PathController($rootScope, $scope, $document, service) {

        $scope.window = {
            base: "",
            paths: [],
            history: [],
            path: [],
        }

        $rootScope.$watch('disk', (disk)=> {
            
            $scope.window.base = disk
            
            access($scope.window.base)
        })

        $scope.selectDirectory = (directory)=> {
            access(directory+"/")
        }

        $scope.selectVideo = (video)=> {
            $rootScope.video = video
            console.log("VIDEO: ", $rootScope.video)
        }

        $scope.back = ()=> {
            $scope.window.history.splice(-1,1)
            var temp = $scope.window.history.slice(-1)[0]
            $scope.window.history.splice(-1,1)
            
            if (!temp) {
                temp = $scope.window.base
            }
             
            access(temp)
        }

        $scope.toggle = ($event)=> {
            $el = $document[0].querySelectorAll(".thumbnails li");
            $el.forEach((item)=> {
                angular.element(item).removeClass("active")
            })

            angular.element($event.target).addClass("active")
        }

        $scope.extract = (path)=> {
            
            var file = path.substring(digestPath(path).lastIndexOf('/') + 1)

            var extension = file.substring(file.lastIndexOf('.') + 1)

            return {file: file, extension: extension}
        }

        function digestPath(path = "C:/") {
            return path.replace(/\\/g, "\/")
        }

        function access(path) {
            service.access(path).then((response)=> {

                $scope.window.history.push(digestPath(path))
                $scope.window.path = digestPath(path)
                $scope.window.paths = response.data

            })
        }
        
    }


}) ()