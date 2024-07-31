const petite = {

    url: '',
    type: 'store',
    folder: '',
    response: '',
    sandbox: false,

    mounted: function() {
        const container = document.getElementById('container')
        container.classList.remove("load");
    },

    __end: function(){
        this.sandbox = false;
    },

    __post: function(url, __obj, success){

        const data = new FormData();
        for (var property in __obj)
        data.append(property, __obj[property])

        this.sandbox = true;
        this.response = ''

        fetch(url, { method: 'POST', body: data })
        .then(function(responce){
            return responce.json();
        }).then((function(responce) {
            if ( responce.success )
            success.call(this, responce)
            this.__end();
        }).bind(this)).catch(() => this.__end)
    },

    save: function() {

        this.__post('/apply', {
            url:  this.url,
            type:  this.type,
            folder:  this.folder
        }, function(responce){
            this.response = responce.file
        })
    }
}