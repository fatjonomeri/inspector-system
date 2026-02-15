/* generated using openapi-typescript-codegen -- do not edit */
/* istanbul ignore file */
/* tslint:disable */
/* eslint-disable */
import type { User } from '../models/User';
import type { CancelablePromise } from '../core/CancelablePromise';
import { OpenAPI } from '../core/OpenAPI';
import { request as __request } from '../core/request';
export class UserService {
    /**
     * Retrieves the collection of User resources.
     * Retrieves the collection of User resources.
     * @param page The collection page number
     * @returns User User collection
     * @throws ApiError
     */
    public static apiUsersGetCollection(
        page: number = 1,
    ): CancelablePromise<Array<User>> {
        return __request(OpenAPI, {
            method: 'GET',
            url: '/api/users',
            query: {
                'page': page,
            },
            errors: {
                403: `Forbidden`,
            },
        });
    }
    /**
     * Retrieves a User resource.
     * Retrieves a User resource.
     * @param id User identifier
     * @returns User User resource
     * @throws ApiError
     */
    public static apiUsersIdGet(
        id: string,
    ): CancelablePromise<User> {
        return __request(OpenAPI, {
            method: 'GET',
            url: '/api/users/{id}',
            path: {
                'id': id,
            },
            errors: {
                403: `Forbidden`,
                404: `Not found`,
            },
        });
    }
}
