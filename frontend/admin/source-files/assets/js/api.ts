// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const getMessage = async ({ url }: { url: string }): Promise<any> => {
    console.log('get message:', url)
    try {
        const response = await fetch(`${url}`);
        // https://sort.joinmerge.gr/api/v1/message

        // Check if the response is successful (status code 2xx)
        if (!response.ok || (response.status < 200 || response.status >= 400)) {
            throw new Error(`Request failed with status ${response.status}`);
        }
        console.log('response status:', response.status)

        // Parse the response (assuming it's JSON)
        const data = await response.json();

        return data;
    } catch (error) {
        console.log("Error occurred while fetching data:", (error as Error).message);
        // You can throw the error to be handled by the caller if needed
        // throw error;
    }
};

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const checkSubscribe = async ({ host }: { host: string }): Promise<any> => {
    try {
        const response = await fetch(`https://sort.joinmerge.gr/api/v1/subscriber?site-url=${host}`);

        // Check if the response is successful (status code 2xx)
        if (!response.ok || (response.status < 200 || response.status >= 400)) {
            throw new Error(`Request failed with status ${response.status}`);
        }
        console.log('response status:', response.status)

        // Parse the response (assuming it's JSON)
        const data = await response.json();

        return data;
    } catch (error) {
        console.log("Error occurred while fetching data:", (error as Error)?.message);
        // You can throw the error to be handled by the caller if needed
        // throw error;
    }
};


// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const postSubscribe = async ({ host, email }: { host: string, email: string }): Promise<any> => {
    try {
        const response = await fetch(`https://sort.joinmerge.gr/api/v1/subscriber?site-url=${host}&email=${email}`, {
            method: "POST",
            // headers: {
            //     'Content-Type': 'application/json',
            // }
        });

        // Check if the response is successful (status code 2xx)
        if (!response.ok || (response.status < 200 || response.status >= 400)) {
            throw new Error(`Request failed with status ${response.status}`);
        }
        console.log('response status:', response.status)

        // Parse the response (assuming it's JSON)
        const data = await response.json();

        return data;
    } catch (error) {
        console.log("Error occurred while fetching data:", (error as Error)?.message);
        // You can throw the error to be handled by the caller if needed
        // throw error;
    }
};

export const getMetaKeysProgress = async ({ url }: { url: string }): Promise<{
    nextPageToProcess: number;
} | undefined> => {
    console.log('get message:', url)
    try {
        const response = await fetch(`${url}`);
        // https://sort.joinmerge.gr/api/v1/message

        // Check if the response is successful (status code 2xx)
        if (!response.ok || (response.status < 200 || response.status >= 400)) {
            throw new Error(`Request failed with status ${response.status}`);
        }
        console.log('response status:', response.status)

        // Parse the response (assuming it's JSON)
        const data = await response.json();

        return data;
    } catch (error) {
        console.log("Error occurred while fetching data:", (error as Error).message);
        // You can throw the error to be handled by the caller if needed
        // throw error;
    }
};