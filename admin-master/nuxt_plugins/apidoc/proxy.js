import { map, mapValues, keyBy, groupBy } from 'lodash';
const groupHeaders = {}; // ex. { sendBird: 'API_KEY: kek'} will inject headers to every method before sending request

class ApiMethod {
    constructor({ name, description, type, url, parameter, success, group }, axios) {
        this.group = group;
        this.name = name;
        this.request = {
            method: type,
            url
        };
        this.description = description;
        this.parameter = parameter ? parameter.fields.Parameter : [];
        this.success = success && success.fields ? success.fields['Success 200'] : [];
        this.axios = axios;
    }

    prepareUrl(params, isMock) {
        if (isMock) {
            return this.request.url;
        }
        const regexp = /\/:[a-zA-Z0-9_-]+[^/]/g;
        const matches = this.request.url.match(regexp);
        let resultUrl = this.request.url;
        if (matches !== null) {
            matches.forEach((match) => {
                const paramName = match.substring(2);
                if (!(paramName in params)) {
                    throw new Error(`No param found ${paramName}`);
                }
                resultUrl = resultUrl.replace(match, `/${params[paramName]}`);
                delete params[paramName];
            });
        }
        return { resultUrl, params };
    }

    // if is_mock = true, params object is mock, and will be returned back.
    sendRequest(params = {}, isMock = false) {
        if (isMock) {
            return new Promise(resolve => resolve(params));
        }

        const urlData = this.prepareUrl(params, isMock);
        const url = urlData.resultUrl;
        params = urlData.params;
        const sendingObj = { url, method: this.request.method };
        if (this.request.method.toLowerCase().trim() === 'get') {
            sendingObj.params = params;
        } else {
            sendingObj.data = params;
        }
        if (groupHeaders[this.group])
            sendingObj.headers = groupHeaders[this.group];


        if (process.client) {
            console.log('Sending request:', sendingObj, this.request, this);
        }
        return this.axios(sendingObj).then(
            (response) => {
                // if (!response.data) {
                //     if (process.client) {
                //         console.log('Response:', response);
                //     }
                //     return response;
                // }
                if (response.data.status === 'bad') {
                    console.error('Error with status BAD:', response.data);
                    return Promise.reject(response.data);
                }
                if (process.client) {
                    console.log('Response:', response.data);
                }
                return response.data;
            },
            (err) => {
                if (err.response && err.response.status === 422) {
                    console.error('Error with status 422:', err.response.data);
                    return Promise.reject(err.response.data);
                }
                console.log('err', err.response.data);
                return Promise.reject(err);
            }
        );
    }
}


const RequestObjectDeepHandler = {
    get(target, key) {
        if (typeof target[key] === 'object' && target[key] !== null) {
            return new Proxy(target[key], RequestObjectDeepHandler);
        }
        return target[key];
    }
};

const handler = {
    get(target, key) {
        if (key === 'meta') {
            return new Proxy(target, RequestObjectDeepHandler);
        }
        if (!(key in target)) {
            return undefined;
        }
        const targetObject = target[key];
        return targetObject.sendRequest.bind(targetObject);
    }
};
const groupsProxyHandler = {
    get(target, key) {
        if (key === 'meta') {
            return new Proxy(target, RequestObjectDeepHandler);
        }
        if (key === '_groupHeaders')
            return groupHeaders;
        if (key === '_setGroupHeaders') {
            return (group, headers) => {
                if (typeof group !== 'string')
                    throw new Error('group param must be string in setHeaders method');
                else
                    groupHeaders[group] = headers;
            };

        }

        if (key in target) {
            return target[key];
        } // every target[key] is already proxy

        const groups = Object.keys(target);
        let found = false;
        groups.forEach((group) => {
            const probableApiMethod = target[group][key];
            if (typeof probableApiMethod !== 'undefined') {
                found = probableApiMethod;
            }
        });
        if (found) {
            return found;
        }
        throw new Error(`api method not found ${key}`);
    }

};

function proxify(api, axios) { // TODO? One proxy for all groups
    const groupsApi = mapValues(groupBy(api, 'group'), (group) => {
        const groupMethods = keyBy(map(group, method => new ApiMethod(method, axios)), 'name');
        return new Proxy(groupMethods, handler);
    });
    return { proxy: new Proxy(groupsApi, groupsProxyHandler), groups: Object.keys(groupsApi) };
}

export { proxify, ApiMethod };
