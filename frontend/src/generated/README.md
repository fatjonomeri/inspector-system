# Generated API Client

This folder contains auto-generated TypeScript API clients from the OpenAPI specification.

## Regenerating the API

When the backend API changes, regenerate the client:

```bash
npm run generate:api
```

## Usage

Import the configured API instances from `client.ts`:

```typescript
import { jobApi, locationApi, userApi } from '@/generated/client'

// Example: Fetch all jobs
const response = await jobApi.apiJobsGetCollection()
const jobs = response.data
```

## Custom Hooks

Use the custom React Query hooks for easier integration:

```typescript
import { useJobs, useAvailableJobs, useMyJobs } from '@/hooks/useJobs'
import { useLocations } from '@/hooks/useLocations'
import { useUsers } from '@/hooks/useUsers'
```

## Structure

- `api/` - Auto-generated API client code (do not edit manually)
- `client.ts` - Configured API instances with axios interceptors

## Authentication

Authentication is handled via session cookies. The axios instance in `client.ts` is configured with:

- `withCredentials: true` for cookie support
- Automatic redirect to `/login` on 401 errors
