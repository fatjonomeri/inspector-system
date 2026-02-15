# ConstraintViolationJsonld

Unprocessable entity

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**context** | [**HydraItemBaseSchemaContext**](HydraItemBaseSchemaContext.md) |  | [optional] [default to undefined]
**id** | **string** |  | [default to undefined]
**type** | **string** |  | [default to undefined]
**status** | **number** |  | [optional] [default to 422]
**violations** | [**Array&lt;ConstraintViolationViolationsInner&gt;**](ConstraintViolationViolationsInner.md) |  | [optional] [default to undefined]
**detail** | **string** |  | [optional] [readonly] [default to undefined]
**description** | **string** |  | [optional] [readonly] [default to undefined]
**type** | **string** |  | [optional] [readonly] [default to undefined]
**title** | **string** |  | [optional] [readonly] [default to undefined]
**instance** | **string** |  | [optional] [readonly] [default to undefined]

## Example

```typescript
import { ConstraintViolationJsonld } from './api';

const instance: ConstraintViolationJsonld = {
    context,
    id,
    type,
    status,
    violations,
    detail,
    description,
    type,
    title,
    instance,
};
```

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)
