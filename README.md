# Bitflow LMS

NMC-compliant Learning Management System for medical colleges.

## High-Level Roadmap
1. Scope & Tech Stack Locked (DONE)
2. Backend foundation: DB schema, auth/RBAC, core APIs
3. Temporary frontend (React + Tailwind) wired to backend
4. Figma design system integration
5. Full feature build-out & stabilization
6. AWS deployment (infrastructure hardening, CI/CD)
7. Mobile app (Flutter) leveraging same APIs

## Tech Stack
Backend: PHP 8 + Laravel 10, MySQL 8, Redis, S3, MediaConvert
Frontend: React + TS, TailwindCSS, shadcn/ui (later tokenized)
Mobile (later): Flutter
Infra: AWS (RDS, S3+CloudFront, ElastiCache, ECS/Beanstalk, SES, SNS/Twilio)

## Directories
- backend/ : Laravel application
- frontend/ : React application
- docs/ : Specifications (features, schema, OpenAPI, NFRs)

## Getting Started (Will Be Expanded)
See docs/setup.md (to be added).
