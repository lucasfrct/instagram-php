/*
 * Autor: Lucas Costa
 * Data: Abril de 2020
 */

class Observer 
{
    source = null;

    constructor(url) {
        this.source = new EventSource(url)
    }
    
    Event(event, callback) {
        
        this.source.addEventListener(event, (ev)=> {
            callback(JSON.parse(ev.data))
        });
    }
}

/*
var obs = new Observer('src/Observer.php?id=1036220550'); 
obs.Event("keyObserver", function(data) {
    console.log("keyObserver", data)
})
*/
