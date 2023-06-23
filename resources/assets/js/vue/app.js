import Vue from 'vue';
import store from './store';
import router from './router';
import App from './components/App';
import moment from 'moment';
import  i18n  from './i18n';

import VueI18n from 'vue-i18n'

Vue.use(VueI18n)


Vue.filter('formatDate', function(value) {
    if (value) {
        return moment(String(value)).format('D MMM YYYY');
    }
});

Vue.filter('now', function(value) {
    if (value) {
        return moment(String(value)).format('MMMM DD, YYYY, hh:mm a');
    }
});


Vue.filter('prefix_year', function(value) {
    if (value) {
        return moment(String(value)).format('YY');
    }
});


Vue.prototype.$frontendUrl = 'https://new.luxique.com.bd';
Vue.prototype.$baseUrl = 'https://api.new.luxique.com.bd';
Vue.prototype.$thumbnailUrl = 'https:/api.new.luxique.com.bd/media/thumbnail';

// Vue.prototype.$frontendUrl = 'http://127.0.0.1:8003';
// Vue.prototype.$baseUrl = 'http://127.0.0.1:8000'; 
// Vue.prototype.$thumbnailUrl = 'http://127.0.0.1:8000/media/thumbnail'; 


export default new Vue({
    i18n,
    store,
    router,
    render: h => h(App),
});