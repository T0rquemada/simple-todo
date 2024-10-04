export async function request(route, method, body=null, jwt=null ) {
    if (!route) throw new Error('Route for request not provided!');
    if (!method) throw new Error('Method for reqeust not provided!');
    if (!body && method !== 'GET') throw new Error('Body for reqeust not provided!');

    try {
        let headers = { 'Content-Type' : 'application/json' };
        if (jwt) headers['Authorization'] = `Bearer ${jwt}`;

        let options = {
            method: method,
            headers: headers
        }

        if (body) options.body = JSON.stringify(body);

        const response = await fetch(`http://localhost:5174/${route}`, options);

        if (!response.ok) { 
            const error = await response.json();
            console.error(`Error while ${route}: ${error.message}`); 
            return null; 
        }

        const data = await response.json();

        return data;
    } catch (err) {
        console.error(err);
    }
}