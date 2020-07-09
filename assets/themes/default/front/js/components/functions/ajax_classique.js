const axios = require('axios/dist/axios');

function loader(status){
    let loader = document.querySelector('#loader');
    if(status){
        axios.interceptors.request.use(function (config) {
            loader.style.display = "flex"; return config;
        }, function (error) { return Promise.reject(error); });
    }else{
        loader.style.display = "none";
    }
}

function sendAjax(self, url, data, suite=null, openLoader=true) {
    if(openLoader){
        loader(true);
    }

    axios({ method: 'post', url: url, data: data }).then(function (response) 
    {
        let data = response.data;
        let code = data.code;
        loader(false);
        
        if(code === 1){
            let state = { error: '', success: data.message }
            let newState = {...state, ...suite}
            self.setState(newState);
            if(data.url !== undefined){
                window.history.replaceState(null, null, data.url);
                setTimeout(function () {
                    location.reload()
                }, 3000);
            }
        }else{
            self.setState(data.errors);
        }
    });
}

module.exports = {
    sendAjax,
    loader
}