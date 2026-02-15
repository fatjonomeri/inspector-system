# Inspector Management System

A demo project for managing inspector work schedules and assessments across multiple locations (UK, Mexico, and India).

## Features

- **Session-based Authentication** with Redis
- **Role-based Access Control** (Admin and Inspector roles)
- **Timezone-aware Operations** - Dates stored in UTC, automatically converted to user's timezone based on location
- **Location Management** - Multi-country support with timezone handling
- **Job Assignment & Completion** - Inspectors can assign themselves to jobs and submit assessments
- **RESTful API** with comprehensive OpenAPI/Swagger documentation
- **Modern Web Interface** - React-based frontend with responsive design

## Tech Stack

**Backend:**

- PHP 8.4 (Symfony 7.4 + API Platform 4.2)
- FrankenPHP (built on Caddy)
- MySQL 8.4
- Redis 7
- NelmioApiDocBundle with Swagger UI

**Frontend:**

- React 18 with TypeScript
- Vite
- TanStack Query (React Query)
- Axios
- Tailwind CSS + shadcn/ui
- React Router

**Containerization:** Docker Compose

## Quick Start

### Prerequisites

- Docker and Docker Compose installed
- Ports 443, 5173, 3306, and 6379 available

### Installation

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd inspector-system
   ```

2. **Start the Docker containers**

   ```bash
   docker compose up -d --build
   ```

3. **Run database migrations**

   ```bash
   docker exec inspector-system-backend-1 php bin/console doctrine:migrations:migrate --no-interaction
   ```

4. **Seed the database**

   ```bash
   docker exec inspector-system-backend-1 php bin/console app:seed-database
   ```

5. **Access the application**
   - **Frontend Web App:** `http://localhost:5173`
   - **API Base URL:** `https://localhost`
   - **API Documentation:** `https://localhost/api/docs`

   > **Note:** You'll see a browser warning about the self-signed SSL certificate when accessing the API directly. This is expected in development - click "Advanced" and proceed. The frontend at port 5173 doesn't require this.

## Test Credentials

The seed command creates the following users:

| Email                        | Password    | Role           | Location |
| ---------------------------- | ----------- | -------------- | -------- |
| admin@example.com            | admin123    | ROLE_ADMIN     | UK       |
| inspector.uk@example.com     | password123 | ROLE_INSPECTOR | UK       |
| inspector.mexico@example.com | password123 | ROLE_INSPECTOR | Mexico   |
| inspector.india@example.com  | password123 | ROLE_INSPECTOR | India    |

## API Overview

### Authentication Endpoints

- `POST /api/auth/register` - Register a new user
- `POST /api/auth/login` - Login (creates session)
- `POST /api/auth/logout` - Logout (destroys session)

### User Endpoints

- `GET /api/me` - Get current user information
- `GET /api/me/jobs` - Get jobs assigned to current user

### Job Endpoints

**Inspector Access:**

- `GET /api/jobs/available` - List all available jobs
- `GET /api/jobs/{id}` - View job details (available jobs or own assigned jobs)
- `POST /api/jobs/{id}/assign` - Assign an available job to yourself
- `POST /api/jobs/{id}/complete` - Submit job completion with assessment

**Admin Access:**

- `GET /api/jobs` - List all jobs (with filters for status and location)
- `GET /api/jobs/{id}` - View any job details
- `POST /api/jobs` - Create new jobs
- All inspector endpoints

### Admin-Only Endpoints

- `GET /api/users` - List all users
- `GET /api/users/{id}` - View user details
- `POST /api/users` - Create new users
- `GET /api/locations` - List all locations

## Key Concepts

### Session-Based Authentication

The API uses session-based authentication with cookies:

- Sessions are stored in Redis for scalability
- "Remember me" functionality keeps users logged in for 1 week
- Session cookies are `httpOnly` and `secure` for security

### Role-Based Access

**ROLE_ADMIN:**

- Full access to all endpoints
- Can create and manage users and jobs
- Can view all jobs and users across all locations

**ROLE_INSPECTOR:**

- Can view available jobs only for his location
- Can assign available jobs to themselves
- Can only complete jobs assigned to them
- Can view their own assigned/completed jobs

### Timezone Handling

- All dates are stored in **UTC** in the database
- API responses automatically convert dates to the **user's timezone** based on their location
- Supported timezones:
  - UK: `Europe/London`
  - Mexico: `America/Mexico_City`
  - India: `Asia/Kolkata`

### Job Workflow

1. **Available** - Job is created by admin, ready for assignment
2. **Assigned** - Inspector assigns the job to themselves with a scheduled date
3. **Completed** - Inspector submits completion with assessment and optional completion date

## Development

### Useful Commands

```bash
# Backend commands
# Clear Symfony cache
docker exec inspector-system-backend-1 php bin/console cache:clear

# Run migrations
docker exec inspector-system-backend-1 php bin/console doctrine:migrations:migrate

# Create a new migration
docker exec inspector-system-backend-1 php bin/console make:migration

# View all routes
docker exec inspector-system-backend-1 php bin/console debug:router

# Frontend commands
# Install dependencies (if needed)
docker exec inspector-system-frontend-1 npm install

# View frontend logs
docker compose logs -f frontend

# Database & Cache
# Access MySQL CLI
docker exec -it inspector-system-db-1 mysql -u user -ppass inspection_db

# Access Redis CLI
docker exec -it inspector-system-redis-1 redis-cli

# View logs
docker compose logs -f backend
docker compose logs -f frontend
```

### Project Structure

```
inspector-system/
├── backend/
│   ├── src/
│   │   ├── Command/          # Console commands (seeding, etc.)
│   │   ├── Controller/       # API endpoints
│   │   ├── Entity/           # Doctrine entities
│   │   ├── Repository/       # Custom database queries
│   │   ├── Security/         # Authentication handlers
│   │   └── Trait/            # Reusable traits (timezone handling)
│   ├── config/               # Symfony configuration
│   ├── migrations/           # Database migrations
│   └── public/               # Web root
├── frontend/
│   ├── src/
│   │   ├── components/       # Reusable UI components
│   │   │   └── ui/           # shadcn/ui components
│   │   ├── contexts/         # React contexts (Auth)
│   │   ├── lib/              # Utilities and API client
│   │   ├── pages/            # Page components
│   │   ├── services/         # API service functions
│   │   └── types/            # TypeScript types
│   └── public/               # Static assets
├── compose.yaml              # Docker Compose configuration
└── README.md                 # This file
```

## Web Interface

The frontend provides an intuitive interface for inspectors:

**Login/Register:**

- Use the test credentials to login or register a new inspector account
- Session is maintained via cookies with 1-week remember me

**Inspector Dashboard:**

- **Available Jobs Tab:** Browse and view all available jobs in your location
  - View job details (title, description, location, created date)
  - Assign jobs to yourself with a scheduled date (in your timezone)
- **My Jobs Tab:** View your assigned and completed jobs
  - See scheduled dates in your timezone
  - Complete jobs by submitting assessments
  - Optional completion date (defaults to current time)
  - View past completed jobs with assessments

## API Documentation

Once the application is running, visit `https://localhost/api/docs` to explore the full API documentation with Swagger UI. You can test all endpoints directly from the browser.

## Stopping the Application

```bash
docker compose down
```

To remove all data (database, Redis, Caddy config):

```bash
docker compose down -v
```

## Notes

- This is a demo project for assessment purposes
- The `.env` file is committed for convenience (contains only Docker default credentials)
- All passwords are securely hashed using Symfony's password hasher
- HTTPS is enabled by default using FrankenPHP's automatic HTTPS
