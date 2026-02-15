# JobApi

All URIs are relative to *http://localhost*

|Method | HTTP request | Description|
|------------- | ------------- | -------------|
|[**apiJobsGetCollection**](#apijobsgetcollection) | **GET** /api/jobs | Retrieves the collection of Job resources.|
|[**apiJobsIdGet**](#apijobsidget) | **GET** /api/jobs/{id} | Retrieves a Job resource.|
|[**apiJobsPost**](#apijobspost) | **POST** /api/jobs | Creates a Job resource.|

# **apiJobsGetCollection**
> Array<Job> apiJobsGetCollection()

Retrieves the collection of Job resources.

### Example

```typescript
import {
    JobApi,
    Configuration
} from './api';

const configuration = new Configuration();
const apiInstance = new JobApi(configuration);

let page: number; //The collection page number (optional) (default to 1)
let status: string; // (optional) (default to undefined)
let status2: Array<string>; // (optional) (default to undefined)
let locationId: number; // (optional) (default to undefined)
let locationId2: Array<number>; // (optional) (default to undefined)

const { status, data } = await apiInstance.apiJobsGetCollection(
    page,
    status,
    status2,
    locationId,
    locationId2
);
```

### Parameters

|Name | Type | Description  | Notes|
|------------- | ------------- | ------------- | -------------|
| **page** | [**number**] | The collection page number | (optional) defaults to 1|
| **status** | [**string**] |  | (optional) defaults to undefined|
| **status2** | **Array&lt;string&gt;** |  | (optional) defaults to undefined|
| **locationId** | [**number**] |  | (optional) defaults to undefined|
| **locationId2** | **Array&lt;number&gt;** |  | (optional) defaults to undefined|


### Return type

**Array<Job>**

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json, application/ld+json, application/problem+json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
|**200** | Job collection |  -  |
|**403** | Forbidden |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **apiJobsIdGet**
> Job apiJobsIdGet()

Retrieves a Job resource.

### Example

```typescript
import {
    JobApi,
    Configuration
} from './api';

const configuration = new Configuration();
const apiInstance = new JobApi(configuration);

let id: string; //Job identifier (default to undefined)

const { status, data } = await apiInstance.apiJobsIdGet(
    id
);
```

### Parameters

|Name | Type | Description  | Notes|
|------------- | ------------- | ------------- | -------------|
| **id** | [**string**] | Job identifier | defaults to undefined|


### Return type

**Job**

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json, application/ld+json, application/problem+json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
|**200** | Job resource |  -  |
|**403** | Forbidden |  -  |
|**404** | Not found |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **apiJobsPost**
> Job apiJobsPost()

Creates a Job resource.

### Example

```typescript
import {
    JobApi,
    Configuration,
    ApiJobsPostRequest
} from './api';

const configuration = new Configuration();
const apiInstance = new JobApi(configuration);

let apiJobsPostRequest: ApiJobsPostRequest; //The new Job resource (optional)

const { status, data } = await apiInstance.apiJobsPost(
    apiJobsPostRequest
);
```

### Parameters

|Name | Type | Description  | Notes|
|------------- | ------------- | ------------- | -------------|
| **apiJobsPostRequest** | **ApiJobsPostRequest**| The new Job resource | |


### Return type

**Job**

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json, application/ld+json, application/problem+json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
|**201** | Job resource created |  -  |
|**400** | Invalid input |  -  |
|**422** | An error occurred |  -  |
|**403** | Forbidden |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

