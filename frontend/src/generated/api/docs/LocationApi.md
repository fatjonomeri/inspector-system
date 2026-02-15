# LocationApi

All URIs are relative to *http://localhost*

|Method | HTTP request | Description|
|------------- | ------------- | -------------|
|[**apiLocationsGetCollection**](#apilocationsgetcollection) | **GET** /api/locations | Retrieves the collection of Location resources.|
|[**apiLocationsIdGet**](#apilocationsidget) | **GET** /api/locations/{id} | Retrieves a Location resource.|

# **apiLocationsGetCollection**
> Array<Location> apiLocationsGetCollection()

Retrieves the collection of Location resources.

### Example

```typescript
import {
    LocationApi,
    Configuration
} from './api';

const configuration = new Configuration();
const apiInstance = new LocationApi(configuration);

let page: number; //The collection page number (optional) (default to 1)

const { status, data } = await apiInstance.apiLocationsGetCollection(
    page
);
```

### Parameters

|Name | Type | Description  | Notes|
|------------- | ------------- | ------------- | -------------|
| **page** | [**number**] | The collection page number | (optional) defaults to 1|


### Return type

**Array<Location>**

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json, application/ld+json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
|**200** | Location collection |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **apiLocationsIdGet**
> Location apiLocationsIdGet()

Retrieves a Location resource.

### Example

```typescript
import {
    LocationApi,
    Configuration
} from './api';

const configuration = new Configuration();
const apiInstance = new LocationApi(configuration);

let id: string; //Location identifier (default to undefined)

const { status, data } = await apiInstance.apiLocationsIdGet(
    id
);
```

### Parameters

|Name | Type | Description  | Notes|
|------------- | ------------- | ------------- | -------------|
| **id** | [**string**] | Location identifier | defaults to undefined|


### Return type

**Location**

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json, application/ld+json, application/problem+json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
|**200** | Location resource |  -  |
|**404** | Not found |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

