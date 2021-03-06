(()=> {
    "use strict"

    angular
        .module("streamvideo")
        .service("clock.service",[ClockService])

    function ClockService() {
        var that = this

        that.formatSecondsToHours = (seconds)=> {
            let hour = String("0" + (Math.floor(seconds / (60 * 60)) % 60))
            let minutes = String("0" + ( Math.floor(seconds / 60) % 60))
            let sec = String("0" + Math.floor(seconds % 60))
            
            return String(hour.substr(-2) +":"+ minutes.substr(-2) +":"+sec.substr(-2))
        }
    }
})()