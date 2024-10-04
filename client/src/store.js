import { createStore } from 'vuex';
import { request } from './api/request.js';

const store = createStore({
    state: {
        tasks: []  
    },
    mutations: {
        setTasks(state, tasks) {
            state.tasks = tasks;
        }
    },
    actions: {
        async refreshTasks({ commit }) {  
            try {
                let jwt = localStorage.getItem('JWT');
                if (!jwt) throw new Error('JWT not exist!');

                let response = await request('tasks/get_tasks', 'GET', null, jwt);
                if (!response.tasks) commit('setTasks', []);
                else commit('setTasks', response.tasks);  
            } catch (error) {
                console.error('Error:', error);
            }
        }
    },
});

export default store;
