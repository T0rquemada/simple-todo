export async function request(route, method, body, auth=false, emit=null) {
    if (!route) throw new Error('Route for reqeust not provided!');
    if (!method) throw new Error('Method for reqeust not provided!');
    if (!body) throw new Error('Body for reqeust not provided!');

    try {
        const response = await fetch(`http://localhost:5174/${route}`, {
            method: method,
            headers: { 'Content-Type' : 'application/json' },
            body: JSON.stringify(body)
        });

        if (!response.ok) { alert(`Error while ${route}! Response not ok!`); return null; }

        const data = await response.json();

        if (!data) { 
            alert(`Error while ${route}! Empty response`);
        }
        
        if (auth && data.jwt && emit !== null) {
            localStorage.setItem('JWT', data.jwt);
            emit('setAuthenticated', true);
        }

        return data;
    } catch (err) {
        console.error(err);
    }
}