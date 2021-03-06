var state = {
    modeLogin: true,
    modeKey: false,
    id: "",
    user: "",
    password: "",
    code: "",
    server: "",
    key: "",
    error: "Erro!!!",
}

mode()
setState({})
toggleError()

function modeLogin() {
    $(".form").css( "display", "block")
    $(".duser").css( "display", "block")
    $(".dpass").css( "display", "block")
    $(".dcode").css( "display", "none")
}

function modeCode() {
    $(".form").css( "display", "block")
    $(".duser").css( "display", "none")
    $(".dpass").css( "display", "none")
    $(".dcode").css( "display", "block")
}

function modeKey() {
    $(".form").css( "display", "none")
}

function mode() {
    (state.modeLogin) ? modeLogin() : modeCode();
    (state.modeKey) ? modeKey() : ''
}

function liveCreate(url, state) {

    let live = $.ajax({ url: url, data: state, type: "POST", cache: false, timeout: 60000, }) 

    live.always((al)=> {
        console.log("RESPONSE PHP LIVE ALWAYS (COMPLETE): ", al.responseText)
    })

    return live
}

function setState(obj) {
    state = Object.assign(state, obj)

    $(".user").val(state.user)
    $(".password").val(state.password)
    $(".code").val(state.code)
    
    errorMsg(state.error)
    $(".server").html(state.server)
    $(".key").html(state.key)
    
    return state
}

function getState() {
    state.user = $(".user").val()
    state.password = $(".password").val()
    state.code = $(".code").val()
    state.id = generateID(state.user)
}

function generateID(name){
    var id = ""
    for (var i = 0; i < name.length; i++) {
        id  += String(name.charCodeAt(i))
    }
    return String(id)
}

function toggleError() {
    $(".error").click(()=> {
        $(".error").toggle()
    })
}

function errorMsg(msg) {
    $(".error > p").html(msg)
}

function validate(data) {
    return (data.id.length >= 5 && data.user.length >= 3 && data.password.length >= 6)
}

$(".mdl-button").click(()=> {

    $(".form-bar").show()
    getState()
    
    console.log(validate(state))

    if (validate(state)) {

        liveCreate('src/go.php', state)

        var obs = new Observer("src/Observer.php?id="+state.id)

        obs.add("keyObserver", (data)=> {
            console.log("keyObserver: ", data)
            $(".bar").hide()
        
            setState(data)
        
            if(state.error.length > 0) {
                $(".error").toggle()
                errorMsg("ERRO de Servidor")
            }
        
            if(state.requiredCode) {
                state.modeLogin = false
                mode()
            }
        
            if(state.key) {
                state.modeKey = true
                mode()
            }
        })

    } else {
        $(".error").toggle() 
        errorMsg("favor preencher o campos")
    }    

})

