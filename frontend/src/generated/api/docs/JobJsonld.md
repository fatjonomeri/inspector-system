# JobJsonld


## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**context** | [**HydraItemBaseSchemaContext**](HydraItemBaseSchemaContext.md) |  | [optional] [default to undefined]
**id** | **string** |  | [default to undefined]
**type** | **string** |  | [default to undefined]
**id** | **number** |  | [optional] [readonly] [default to undefined]
**title** | **string** |  | [default to undefined]
**description** | **string** |  | [optional] [default to undefined]
**status** | **string** |  | [optional] [default to StatusEnum_Available]
**assignedTo** | **string** |  | [optional] [default to undefined]
**scheduledDate** | **string** |  | [optional] [default to undefined]
**completedAt** | **string** |  | [optional] [default to undefined]
**assessment** | **string** |  | [optional] [default to undefined]
**location** | **string** |  | [default to undefined]
**createdAt** | **string** |  | [optional] [default to undefined]
**updatedAt** | **string** |  | [optional] [default to undefined]
**available** | **boolean** |  | [optional] [readonly] [default to undefined]
**assigned** | **boolean** |  | [optional] [readonly] [default to undefined]
**completed** | **boolean** |  | [optional] [readonly] [default to undefined]

## Example

```typescript
import { JobJsonld } from './api';

const instance: JobJsonld = {
    context,
    id,
    type,
    id,
    title,
    description,
    status,
    assignedTo,
    scheduledDate,
    completedAt,
    assessment,
    location,
    createdAt,
    updatedAt,
    available,
    assigned,
    completed,
};
```

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)
