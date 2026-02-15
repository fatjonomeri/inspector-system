# ErrorJsonld

A representation of common errors.

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**context** | [**HydraItemBaseSchemaContext**](HydraItemBaseSchemaContext.md) |  | [optional] [default to undefined]
**id** | **string** |  | [default to undefined]
**type** | **string** |  | [default to undefined]
**title** | **string** | A short, human-readable summary of the problem. | [optional] [readonly] [default to undefined]
**detail** | **string** | A human-readable explanation specific to this occurrence of the problem. | [optional] [readonly] [default to undefined]
**status** | **number** |  | [optional] [default to 400]
**instance** | **string** | A URI reference that identifies the specific occurrence of the problem. It may or may not yield further information if dereferenced. | [optional] [readonly] [default to undefined]
**type** | **string** | A URI reference that identifies the problem type | [optional] [readonly] [default to undefined]
**description** | **string** |  | [optional] [readonly] [default to undefined]

## Example

```typescript
import { ErrorJsonld } from './api';

const instance: ErrorJsonld = {
    context,
    id,
    type,
    title,
    detail,
    status,
    instance,
    type,
    description,
};
```

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)
