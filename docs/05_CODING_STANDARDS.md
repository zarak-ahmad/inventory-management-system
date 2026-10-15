# Coding Standards

**Project:** Professional Offline POS & Inventory Management System

**Version:** 1.0

---

# Purpose

This document defines the coding standards that must be followed throughout the project.

The objective is to keep the code clean, organized, reusable, maintainable, and easy to understand.

These standards apply to every file generated during development.

---

# General Principles

Always write code that is:

- Clean
- Readable
- Reusable
- Maintainable
- Secure
- Scalable

Follow these principles:

- SOLID
- DRY (Don't Repeat Yourself)
- KISS (Keep It Simple, Stupid)
- Separation of Concerns

---

# Laravel Standards

Always follow Laravel 12 best practices.

Use:

- Eloquent ORM
- Resource Controllers
- Route Model Binding
- Form Requests
- Middleware
- Database Transactions
- Soft Deletes (where appropriate)

Avoid writing raw SQL unless necessary.

---

# Project Folder Structure

Keep the default Laravel folder structure.

Important folders:

app/
database/
resources/
routes/
storage/
public/

Do not create unnecessary folders.

---

# Naming Conventions

## Controllers

Singular + Controller

Examples:

ProductController

CategoryController

SupplierController

---

## Models

Singular

Examples:

Product

Category

Supplier

Purchase

Sale

---

## Database Tables

Plural

Examples:

products

categories

suppliers

sales

purchase_items

---

## Migrations

Use Laravel default naming.

Example:

create_products_table

---

## Variables

Use meaningful camelCase names.

Good:

$product

$totalAmount

$purchasePrice

Bad:

$p

$a

$x

---

## Methods

Method names should describe the action.

Examples:

store()

update()

destroy()

calculateStock()

generateInvoice()

Avoid unclear names.

---

# Controllers

Controllers should only handle:

- Receiving requests
- Calling business logic
- Returning responses

Do not write large business logic inside controllers.

Keep controllers small and readable.

---

# Models

Models should contain:

- Relationships
- Accessors
- Mutators
- Scopes

Avoid placing complex business logic in models.

---

# Validation

Always validate user input.

Use Laravel Form Requests for:

- Create
- Update

Never trust user input.

---

# Database

Every database change must include:

- Migration
- Foreign Keys
- Indexes (where appropriate)

Never edit production data manually.

---

# Relationships

Always use Eloquent relationships.

Examples:

belongsTo()

hasMany()

hasOne()

belongsToMany()

Avoid manual joins unless necessary.

---

# Security

Always implement:

- Authentication
- Authorization
- CSRF Protection
- XSS Protection
- SQL Injection Prevention

Never expose sensitive information.

Never store plain-text passwords.

---

# Error Handling

Use try/catch when performing critical operations such as:

- Purchases
- Sales
- Stock Updates
- File Uploads

Return user-friendly messages.

Log unexpected exceptions.

---

# Transactions

Use database transactions whenever multiple database operations must succeed together.

Example:

Purchase

↓

Purchase Items

↓

Inventory Update

↓

Inventory Transaction

If one operation fails, rollback everything.

---

# Inventory Rules

Never update product stock directly.

Every stock change must create an Inventory Transaction.

This includes:

- Purchase
- Sale
- Return
- Adjustment
- Damage

Inventory history must always remain accurate.

---

# Comments

Write comments only when they explain important business logic.

Do not write obvious comments.

Bad:

// Save Product

Good:

// Automatically reduce inventory after successful sale

---

# Blade Templates

Keep Blade templates clean.

Avoid large PHP blocks.

Use Blade directives.

Reuse layouts and components.

---

# JavaScript

Keep JavaScript organized.

Avoid inline JavaScript.

Use event listeners instead of inline onclick attributes where practical.

---

# CSS

Use Bootstrap 5 classes whenever possible.

Avoid unnecessary custom CSS.

Keep styling consistent across all pages.

---

# Logging

Important actions should be logged.

Examples:

- Product Created
- Product Updated
- Product Deleted
- Purchase Created
- Sale Completed
- Stock Adjusted
- User Login

---

# File Uploads

Validate:

- File Type
- File Size

Store uploaded files in Laravel's storage system.

Do not overwrite existing files.

---

# Git Commit Messages

Use clear commit messages.

Examples:

Add Product Management

Create Supplier Module

Implement Inventory Transactions

Fix Stock Calculation

Avoid messages like:

Update

Changes

Fix

---

# Code Review Checklist

Before completing any module, verify:

- Code works correctly
- Validation exists
- Error handling exists
- Relationships are correct
- No duplicate code
- UI is responsive
- Database integrity is maintained

---

# Development Workflow

For every module:

1. Design database changes.
2. Create migration.
3. Create model.
4. Define relationships.
5. Create controller.
6. Create validation.
7. Build views.
8. Test functionality.
9. Fix issues.
10. Commit changes.

Do not skip any step.

---

# Cursor Instructions

When generating code:

- Follow this document.
- Do not generate placeholder code.
- Do not skip validation.
- Do not skip migrations.
- Keep controllers clean.
- Follow Laravel conventions.
- Explain major architectural decisions before coding.
- Stop after completing the requested module.



# Project Folder Structure

app/
│
├── Console/
├── Exceptions/
├── Helpers/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── Dashboard/
│   │   ├── Products/
│   │   ├── Purchases/
│   │   ├── Sales/
│   │   ├── Reports/
│   │   └── Settings/
│   │
│   ├── Middleware/
│   └── Requests/
│
├── Models/
│
├── Services/
│
├── Traits/
│
└── Providers/

database/
│
├── factories/
├── migrations/
├── seeders/

resources/
│
├── views/
│   ├── layouts/
│   ├── dashboard/
│   ├── products/
│   ├── categories/
│   ├── suppliers/
│   ├── purchases/
│   ├── sales/
│   ├── reports/
│   └── settings/
│
├── css/
└── js/

routes/
│
├── web.php
├── auth.php

public/
│
├── images/
├── uploads/
└── build/

storage/
│
├── app/
├── logs/
└── framework/

---

# End of Document
