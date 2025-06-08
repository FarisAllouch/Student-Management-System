var Constants = {
    get_api_base_url: function () {
        if(location.hostname == 'localhost') {
            return "http://localhost/FarisAllouch/Student-Management-System/backend/"
        } else {
            return "https://squid-app-8obkj.ondigitalocean.app/backend/"
        }
    }
}