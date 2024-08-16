export async function request(route, method, body, auth=false, emit=null) {
    try {
        const response = await fetch(`http://localhost:5174/${route}`, {
            method: method,
            headers: { 'Content-Type' : 'application/json' },
            body: JSON.stringify(body)
        });

        const data = await response.json();

        if (!data) { 
            alert(`Error while ${route}! Empty response`);
            return false;
        }

        if (data.code !== 200) { 
            alert(`Error while ${route}, message: ${data.message}`);
            return false;
        }
        
        console.log(`Response while ${route} status: ${data}`);
        
        if (auth && data.jwt && emit !== null) {
            localStorage.setItem('JWT', data.jwt);
            emit('setAuthenticated', true);
        }

        return true; // For Task.vue when set complete for task
    } catch (err) {
        console.error(err);
    }
}