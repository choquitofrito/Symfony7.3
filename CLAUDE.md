# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Repository Structure

This repository contains multiple Symfony 7.1 learning projects, each demonstrating different features and concepts:

- **ProjetGeminiAPI**: Integration with Google's Gemini API for AI-powered content generation
- **ProjetMessenger**: Symfony Messenger component implementation
- **ProjetLoginPass**: Authentication and login functionality
- **ProjetLoginPassRoles**: Role-based access control with authentication
- **FormsManyToMany**: Complex form handling with many-to-many relationships
- **ProjetRelationsSymfony**: Doctrine entity relationships
- **ProjetDQLSymfony**: Doctrine Query Language examples
- **ProjetFormulairesSymfony**: Advanced form handling
- **ProjetAssetMapper**: Asset management with Symfony's AssetMapper
- **ProjetCalendrierEvenements**: Event calendar implementation
- **ProjetForum**: Forum/discussion board functionality
- **ProjetImporExport-QuizHistoire**: Import/export functionality with quiz system
- **ProjetModeleSymfony**: Model architecture patterns

## Common Development Commands

All projects follow standard Symfony conventions. Navigate to the specific project directory first:

```bash
cd ProjetGeminiAPI  # or any other project directory
```

### Development Server
```bash
symfony serve
# or
php -S localhost:8000 -t public/
```

### Database Operations
```bash
# Create database
php bin/console doctrine:database:create

# Run migrations
php bin/console doctrine:migrations:migrate

# Generate migration from entities
php bin/console make:migration
```

### Testing
```bash
# Run PHPUnit tests
php bin/phpunit
# or
vendor/bin/phpunit
```

### Code Generation
```bash
# Generate controller
php bin/console make:controller

# Generate entity
php bin/console make:entity

# Generate form
php bin/console make:form

# Clear cache
php bin/console cache:clear
```

### Docker Development
Most projects include Docker Compose configuration for database services:

```bash
# Start database services
docker compose up -d

# Stop services
docker compose down
```

## Architecture Overview

### Standard Symfony Structure
Each project follows Symfony 7.1 conventions:
- `src/Controller/`: Controllers using PHP 8+ attributes for routing
- `src/Entity/`: Doctrine ORM entities
- `src/Form/`: Symfony form types
- `src/Repository/`: Custom Doctrine repositories
- `config/`: Configuration files (services.yaml, packages/)
- `templates/`: Twig templates
- `migrations/`: Database migrations
- `tests/`: PHPUnit tests

### Key Technologies
- **PHP 8.2+**: Modern PHP with attributes and typed properties
- **Symfony 7.1**: Full-stack framework
- **Doctrine ORM 3.3**: Database abstraction and ORM
- **Twig**: Template engine
- **PostgreSQL**: Primary database (configured in most projects)
- **Asset Mapper**: Modern asset management
- **Stimulus/Turbo**: Frontend interactivity

### Configuration Patterns
- Environment variables in `.env` files
- Service configuration in `config/services.yaml`
- Database connections via `DATABASE_URL` environment variable
- API keys and secrets via environment parameters

### Form and Entity Patterns
- Forms use `FormType` classes with modern field types
- Entities use PHP 8+ attributes for Doctrine mapping
- Repository pattern for custom queries
- Validation using Symfony Validator constraints

## Project-Specific Notes

### ProjetGeminiAPI
- Integrates with Google Gemini API for AI content generation
- Uses HttpClient service for API calls
- API key configured via `API_KEY_GEMINI` environment variable
- Example of external API integration patterns

### Authentication Projects (ProjetLoginPass, ProjetLoginPassRoles)
- Implement Symfony Security component
- User entity with password hashing
- Role-based access control examples
- Security configuration in `config/packages/security.yaml`

### Database-Heavy Projects
- Use PostgreSQL by default
- Extensive use of Doctrine relationships (OneToMany, ManyToMany)
- Migration-based schema management
- Repository pattern for complex queries