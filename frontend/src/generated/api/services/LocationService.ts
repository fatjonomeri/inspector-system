/* generated using openapi-typescript-codegen -- do not edit */
/* istanbul ignore file */
/* tslint:disable */
/* eslint-disable */
import type { Location } from '../models/Location';
import type { CancelablePromise } from '../core/CancelablePromise';
import { OpenAPI } from '../core/OpenAPI';
import { request as __request } from '../core/request';
export class LocationService {
    /**
     * Retrieves the collection of Location resources.
     * Retrieves the collection of Location resources.
     * @param page The collection page number
     * @returns Location Location collection
     * @throws ApiError
     */
    public static apiLocationsGetCollection(
        page: number = 1,
    ): CancelablePromise<Array<Location>> {
        return __request(OpenAPI, {
            method: 'GET',
            url: '/api/locations',
            query: {
                'page': page,
            },
        });
    }
    /**
     * Retrieves a Location resource.
     * Retrieves a Location resource.
     * @param id Location identifier
     * @returns Location Location resource
     * @throws ApiError
     */
    public static apiLocationsIdGet(
        id: string,
    ): CancelablePromise<Location> {
        return __request(OpenAPI, {
            method: 'GET',
            url: '/api/locations/{id}',
            path: {
                'id': id,
            },
            errors: {
                404: `Not found`,
            },
        });
    }
}
