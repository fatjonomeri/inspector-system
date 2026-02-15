/* generated using openapi-typescript-codegen -- do not edit */
/* istanbul ignore file */
/* tslint:disable */
/* eslint-disable */
import type { HydraItemBaseSchema } from './HydraItemBaseSchema';
export type User_jsonld = (HydraItemBaseSchema & {
    readonly id?: number;
    email: string;
    /**
     * The user roles
     */
    roles?: Array<string>;
    password?: string;
    location: string;
    firstName?: string | null;
    lastName?: string | null;
    createdAt?: string;
    lastLoginAt?: string | null;
    /**
     * A visual identifier that represents this user.
     */
    readonly userIdentifier?: string;
    readonly timezone?: string;
});

