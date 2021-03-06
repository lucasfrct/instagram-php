(()=> {

    angular
        .module("streamvideo")
        .component("streamvideo", {
            templateUrl: "app/streamvideo/stream.video.html",
            controller: [
                "$rootScope", 
                "$scope", 
                "$timeout",
                "stream.video.service", 
                "clock.service", 
                StreamVideoController
            ]
        })

    function StreamVideoController($rootScope,$scope, $timeout, service, clock) {

        $scope.control = {
            landscape: true,
            portrait: false,
            time: "00:00:00",
            looped: false,
            counter: "",
            required: false,
        }

        $scope.stream = {
            connection: false,
            play: false,
            loop: false,
            required: false,
            response: [],
            status: [],
            comments: [],
            likes: [],
            time: 0,
            path: "",
            disk: "C:/",
            position: "landscape",
            user: "lucascosta4590",
            password: "",
            code:"",
            server: "",
            key: "",
        }

        $scope.playStream = (stream)=> {

            console.log("PLAY >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>: ", stream)

            if (stream.code.length > 0) {
                $scope.control.required = false
                $scope.streram.required = true
            }

            service.play(stream).then((data)=> {
                console.log("PLAY RESPONSE >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>: ", data.data)
            })

        }

        $scope.stopStream = (stream)=> {

            console.log("STOP >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>: ", stream) 

            service.stop(stream).then((data)=> {
                console.log("STOP RESPONSE >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>: ", data.data)
            })
        }
  
        $scope.loadStream = (stream)=> {

            $scope.toggleModal()

            $rootScope.disk = $scope.stream.disk
        }

        $rootScope.$watch('video', (video)=> {
            $scope.stream.path = video
        })

        $scope.loopStream = (stream)=> {
            $scope.stream.loop = !$scope.stream.loop
        }

        $scope.toggleModal = ()=> {
            $scope.control.modal = !$scope.control.modal
        }

        $scope.togglePosition = ()=> {
            $scope.control.landscape = !$scope.control.landscape
            $scope.control.portrait = !$scope.control.portrait

            if ( $scope.control.portrait ) {
                $scope.stream.position = "portrait"
            } else {
                $scope.stream.position = "landscape"
            }
        }

        var status = new Observer("app/observer/observer.status.php")
        status.Event('connection', (stream)=> {
            
            console.log("EVENT CONECTION: ", stream)

            $scope.stream.connection = stream.connection 

            if (!$scope.control.required && stream.required) {
                $scope.control.required = stream.required
                $scope.stream.required = stream.required
            }
            
            if ($scope.stream.connection) {
                $scope.control.time = clock.formatSecondsToHours($scope.stream.time++)
            } else {
                $scope.stream.time = 0
                $scope.control.time = clock.formatSecondsToHours($scope.stream.time)
            }

           
            $scope.$apply()

        });
    }

}) ()