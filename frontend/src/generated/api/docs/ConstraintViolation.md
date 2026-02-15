# ConstraintViolation

Unprocessable entity

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**status** | **number** |  | [optional] [default to 422]
**violations** | [**Array&lt;ConstraintViolationViolationsInner&gt;**](ConstraintViolationViolationsInner.md) |  | [optional] [default to undefined]
**detail** | **string** |  | [optional] [readonly] [default to undefined]
**type** | **string** |  | [optional] [readonly] [default to undefined]
**title** | **string** |  | [optional] [readonly] [default to undefined]
**instance** | **string** |  | [optional] [readonly] [default to undefined]

## Example

```typescript
import { ConstraintViolation } from './api';

const instance: ConstraintViolation = {
    status,
    violations,
    detail,
    type,
    title,
    instance,
};
```

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)
