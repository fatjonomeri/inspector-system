# User


## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **number** |  | [optional] [readonly] [default to undefined]
**email** | **string** |  | [default to undefined]
**roles** | **Array&lt;string&gt;** | The user roles | [optional] [default to undefined]
**password** | **string** |  | [optional] [default to undefined]
**location** | **string** |  | [default to undefined]
**firstName** | **string** |  | [optional] [default to undefined]
**lastName** | **string** |  | [optional] [default to undefined]
**createdAt** | **string** |  | [optional] [default to undefined]
**lastLoginAt** | **string** |  | [optional] [default to undefined]
**userIdentifier** | **string** | A visual identifier that represents this user. | [optional] [readonly] [default to undefined]
**timezone** | **string** |  | [optional] [readonly] [default to undefined]

## Example

```typescript
import { User } from './api';

const instance: User = {
    id,
    email,
    roles,
    password,
    location,
    firstName,
    lastName,
    createdAt,
    lastLoginAt,
    userIdentifier,
    timezone,
};
```

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)
