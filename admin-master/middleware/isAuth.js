export default function ({ store, route, redirect }) {

    if (!store.getters.isAuthenticated && route.path !== '/login') {
        redirect('/login');
    }
}
