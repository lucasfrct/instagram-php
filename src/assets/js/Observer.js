// cria a conexão persistente

class Observer 
{
    source = null;

    constructor(url) {
        this.source = new EventSource(url)
    }

    add(event, callback) {
        this.source.addEventListener(event, (ev)=> {
            callback(JSON.parse(ev.data))
        });
    }
}

/*
var obs = new Observer('src/Observer.php?id=1036220550'); 
obs.add("keyObserver", function(data) {
    console.log("keyObserver", data)
})
*/
