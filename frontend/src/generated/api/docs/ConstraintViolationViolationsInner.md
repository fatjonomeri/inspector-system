# ConstraintViolationViolationsInner


## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**propertyPath** | **string** | The property path of the violation | [default to undefined]
**message** | **string** | The message associated with the violation | [default to undefined]
**code** | **string** | The code of the violation | [optional] [default to undefined]
**hint** | **string** | An extra hint to understand the violation | [optional] [default to undefined]
**payload** | **{ [key: string]: any; }** | The serialized payload of the violation | [optional] [default to undefined]

## Example

```typescript
import { ConstraintViolationViolationsInner } from './api';

const instance: ConstraintViolationViolationsInner = {
    propertyPath,
    message,
    code,
    hint,
    payload,
};
```

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)
