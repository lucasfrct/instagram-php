(()=> {


    angular
        .module("instagramlivekey")
        .directive('filepath', ['$timeout', inputFileDirective])

    function inputFileDirective($timeout) {
        return {
            link: function (scope, element, attrs) {

                console.log("FILE Exe")

                element.on('change', function  (evt) {

                    var fileInput = document.getElementById('video_p');
                    readVideo(fileInput);

                    //var fileUrl = window.URL.createObjectURL(fileInput.files[0]);
                    //console.log ( "file", fileUrl )              

                });
            }
        }
    }

    function readVideo(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();
        
            reader.onload = function(e) {
        
                console.log("file", e.target.result)
            };
        
            reader.readAsDataURL(input.files[0]);
        }
    }
        

})()