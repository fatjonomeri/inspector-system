/* generated using openapi-typescript-codegen -- do not edit */
/* istanbul ignore file */
/* tslint:disable */
/* eslint-disable */
import type { HydraItemBaseSchema } from './HydraItemBaseSchema';
export type Job_jsonld = (HydraItemBaseSchema & {
    readonly id?: number;
    title: string;
    description?: string | null;
    status?: Job_jsonld.status;
    assignedTo?: string | null;
    scheduledDate?: string | null;
    completedAt?: string | null;
    assessment?: string | null;
    location: string;
    createdAt?: string;
    updatedAt?: string | null;
    readonly available?: boolean;
    readonly assigned?: boolean;
    readonly completed?: boolean;
});
export namespace Job_jsonld {
    export enum status {
        AVAILABLE = 'available',
        ASSIGNED = 'assigned',
        COMPLETED = 'completed',
    }
}

