(()=> {

    angular
        .module("streamvideo")
        .service("stream.video.service", [ "$http", StreamVideoService])

    function StreamVideoService($http) {
        var that = this

        that.play = (stream)=> {
            stream.action = "play"
            stream.play = true
            return $http({method: "POST", url: "app/streamvideo/stream.video.play.service.php", data: stream})
        }

        that.stop = (stream)=> {
            stream.action = "stop"
            stream.play = false
            return $http({method: "POST", url: "app/streamvideo/stream.video.stop.service.php", data: stream})
        }

    }

}) ()