// route = 'tasks/delete_task'
export async function request(route, method, body) {
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
        return true;
    } catch (err) {
        console.error(err);
    }
}