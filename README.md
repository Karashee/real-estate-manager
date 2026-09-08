# Real Estate Management System

A web-based platform for Kenyan rental property management. It centralises property listings, tenant applications and lease records in one place, replacing the spreadsheets, WhatsApp threads and paper files that small-scale landlords and property managers typically rely on.

Final year project — BSc Informatics and Computer Science, Strathmore University.

## Features

**Tenant**
- Browse and filter listings by price, location and unit features
- Create and manage a profile
- Submit rental applications with supporting documents
- Track application status and view lease details
- In-platform messaging with the property manager

**Property manager**
- Add, edit and remove listings (location, price, amenities, lease terms)
- Review, approve or reject tenant applications
- Manage lease agreements and tenant records
- Notifications for new applications, lease expiry and maintenance requests
- Reporting on occupancy, rental income and application trends

**Administrator**
- User management and role assignment
- Oversight of listings, including removal of stale or fraudulent entries
- Platform monitoring and data integrity checks

## Tech stack

| Layer | Technology |
|---|---|
| Frontend | HTML, CSS, JavaScript, Bootstrap |
| Backend | PHP |
| Database | PostgreSQL |
| Tools | Visual Studio Code, pgAdmin 4 |

## Requirements

- PHP 8.x with the PostgreSQL extension (`pdo_pgsql`)
- PostgreSQL 13 or later
- A modern browser (Chrome, Firefox, Edge)
- 8 GB RAM, 8 GB free disk space

## Setup

```bash
git clone <repo-url>
cd <project-folder>
```

1. Create the database and load the schema:

```bash
createdb <db_name>
psql -d <db_name> -f database/schema.sql
```

2. Set your database credentials in the config file (`config/database.php` or equivalent).

3. Start the server from the project root:

```bash
php -S localhost:8000
```

4. Open `http://localhost:8000` and register as a tenant or manager. Admin accounts are seeded directly in the database.

## Testing

Modules were verified with both white box and black box testing. White box testing covered authentication and role-based access control, checking logic paths and guarding against SQL injection and session hijacking. Black box testing covered the user-facing flows — registration, login, listing creation, application submission and tracking — using valid and invalid input to confirm graceful error handling.

## Roadmap

- M-PESA integration for in-platform rent payment and automatic receipts
- SMS and USSD notifications for feature-phone users
- Third-party identity verification for property managers
- Native mobile app

## Authors

- Kimutai Derrick Kibey — 169651
- Andrew Karanja Gathirwa — 167144

Supervisor: Allan Vikiru, School of Computing and Engineering Science, Strathmore University.

## Screenshots

**Landing page**

<img width="502" height="734" alt="image" src="https://github.com/user-attachments/assets/9626a0d5-03da-4470-a99a-25d4ba9ccc75" />
<img width="932" height="722" alt="image" src="https://github.com/user-attachments/assets/199634b8-7ed8-470b-9074-4e345b1c44bc" />


**Platform Oversight**

<img width="1218" height="628" alt="image" src="https://github.com/user-attachments/assets/daf52349-80f6-46a8-8d72-1e6773117e48" />


**Notification/ Lease Progress Page**

<img width="1229" height="728" alt="image" src="https://github.com/user-attachments/assets/5f3f4948-ebde-4d74-bd6a-92c5c3bcfb3c" />


**Contact Us**

<img width="972" height="717" alt="image" src="https://github.com/user-attachments/assets/1863742e-d302-4b32-8491-aaed9156948e" />


**Property listings**

<img width="1031" height="737" alt="image" src="https://github.com/user-attachments/assets/1fc6c692-ebec-4d51-9404-152fc5101065" />

