/* generated using openapi-typescript-codegen -- do not edit */
/* istanbul ignore file */
/* tslint:disable */
/* eslint-disable */
import type { Job } from '../models/Job';
import type { CancelablePromise } from '../core/CancelablePromise';
import { OpenAPI } from '../core/OpenAPI';
import { request as __request } from '../core/request';
export class JobService {
    /**
     * Retrieves the collection of Job resources.
     * Retrieves the collection of Job resources.
     * @param page The collection page number
     * @param status
     * @param statusArray
     * @param locationId
     * @param locationIdArray
     * @returns Job Job collection
     * @throws ApiError
     */
    public static apiJobsGetCollection(
        page: number = 1,
        status?: string,
        statusArray?: Array<string>,
        locationId?: number,
        locationIdArray?: Array<number>,
    ): CancelablePromise<Array<Job>> {
        return __request(OpenAPI, {
            method: 'GET',
            url: '/api/jobs',
            query: {
                'page': page,
                'status': status,
                'status[]': statusArray,
                'location.id': locationId,
                'location.id[]': locationIdArray,
            },
            errors: {
                403: `Forbidden`,
            },
        });
    }
    /**
     * Creates a Job resource.
     * Creates a Job resource.
     * @param requestBody The new Job resource
     * @returns Job Job resource created
     * @throws ApiError
     */
    public static apiJobsPost(
        requestBody?: {
            title: string;
            description?: string;
            /**
             * IRI of the location
             */
            location: string;
        },
    ): CancelablePromise<Job> {
        return __request(OpenAPI, {
            method: 'POST',
            url: '/api/jobs',
            body: requestBody,
            mediaType: 'application/json',
            errors: {
                400: `Invalid input`,
                403: `Forbidden`,
                422: `An error occurred`,
            },
        });
    }
    /**
     * Retrieves a Job resource.
     * Retrieves a Job resource.
     * @param id Job identifier
     * @returns Job Job resource
     * @throws ApiError
     */
    public static apiJobsIdGet(
        id: string,
    ): CancelablePromise<Job> {
        return __request(OpenAPI, {
            method: 'GET',
            url: '/api/jobs/{id}',
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
