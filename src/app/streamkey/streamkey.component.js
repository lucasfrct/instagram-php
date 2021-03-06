(()=> {

    angular
        .module("instagramlivekey")
        .component("streamkey", {
            templateUrl: "src/app/streamkey/streamkey.html",
            controller: [ "$scope", "$timeout", "streamkey.service", StreamKeyController ],    
        })

    function StreamKeyController($scope, $timeput, service) {
    
        $scope.loaded = ()=> {
            $timeput(()=> { M.updateTextFields() },10)
        }

        $scope.control = {
            bar: false,
            code: false,
            form: true,
            invalid: false,
        }
        
        $scope.code = ""

        $scope.stream = {
            status: [],
            required: false,
            id: "",
            user: "",
            code: "",
            password: "",
            server: "",
            key: "",
            server: "",
            key: "",
            broadcast: "",
        }
        
        $scope.copy = (id)=> {
            
            el = angular.element(document.querySelector("#"+id));
            el[0].select()
            el[0].setSelectionRange(0, 99999);
            document.execCommand("copy");  
            
            M.toast({ html: 'Copiado com sucesso' })
        }

        $scope.getstream = (stream)=> {


            $scope.control.bar = true
            $scope.control.invalid = false
            
            if (!$scope.control.code) {
                
                stream.id = IdCreate(stream.user);
                
                eventCode(stream.id)
                
                service.stream(stream).then((data)=> {
                    console.log("STREAM RESPONSE =======================================", data.data)
                    $scope.control.bar = false
                    $scope.loaded()
                })

            }

            if ($scope.control.code) { sendCode(stream) }
            
        }

        function eventCode(id) {
            EventCode = new EventSource("src/observer.code.php?id="+id)

            EventCode.addEventListener("code", function(event) {

                var stream = JSON.parse(event.data)
                
                console.log("EVENT CODE TRIGGER: ", stream )

                if (stream.require == true) {
                    $scope.stream.require = true
                    $scope.control.code = true
                    $scope.control.bar = false
                }
    
                stream.status.map((item)=> {
                    if (item.LoginError && item.LoginError.length > 0 && !$scope.control.invalid) {
                        $scope.control.invalid = true
                        M.toast({ html: 'Favor inserir usuário e senha válidos' })
                    }
                })
                    
            
                if (stream.server && stream.key) {
                    stream.password = $scope.stream.password
                    $scope.stream = stream
                    $scope.control.form = false
                    $scope.control.bar = false

                    $scope.loaded()
                    EventCode.close()

                }
                
                $scope.$apply()
    
            
            })
        }

        function compareLogin(str) {
            return (String(JSON.stringify(angular.copy($scope.stream))) != str)
        } 

        function sendCode(login) {
            login.code = $scope.code
            service.code(login).then((code)=> {
                console.log("RESPONSE SEND CODE  :", code.data)
                $scope.loaded()
            })
        }

        function IdCreate ( user ) {
            return String(String(Math.floor(Date.now() / 1000))+"-"+user);
        }

    }

}) ()