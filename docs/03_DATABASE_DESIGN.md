# Database Design

**Project:** Professional Offline POS & Inventory Management System

**Version:** 1.0

---

# Purpose

This document defines the complete database architecture for the POS & Inventory Management System.

The database must be:

- Normalized
- Scalable
- Secure
- Easy to maintain
- Optimized for performance
- Compatible with SQLite (Default)
- Compatible with MySQL (Future)

---

# Database Engine

Default Database

SQLite

Future Support

MySQL

---

# Naming Conventions

## Tables

Use plural names.

Example:

users

products

categories

suppliers

sales

purchase_items

---

## Primary Keys

Every table uses:

id

BIGINT

Auto Increment

---

## Foreign Keys

Use singular table name followed by "_id"

Examples

category_id

supplier_id

brand_id

purchase_id

sale_id

---

## Timestamps

Every table should include

created_at

updated_at

Soft deletes should be used where appropriate.

---

# Database Relationships

User

↓

creates

↓

Purchase

↓

contains

↓

Purchase Items

↓

references

↓

Product

↓

belongs to

↓

Category

↓

Brand

↓

Supplier

---

Sales

↓

contains

↓

Sale Items

↓

references

↓

Product

---

Stock Adjustments

↓

references

↓

Product

---

# Database Tables

---

## users

Stores application users.

Columns

- id
- name
- email
- phone
- password
- role_id
- status
- remember_token
- created_at
- updated_at

Relationship

User belongs to Role.

---

## roles

Columns

- id
- name
- description

Relationship

One role has many users.

---

## permissions

Stores application permissions.

Example

manage_products

manage_sales

view_reports

manage_inventory

---

## role_permissions

Pivot Table

role_id

permission_id

---

## categories

Columns

- id
- name
- description
- status
- created_at
- updated_at

Relationship

One category has many products.

---

## brands

Columns

- id
- name
- description
- status

Relationship

One brand has many products.

---

## units

Columns

- id
- name
- short_name

Examples

Piece

Kg

Box

Bottle

Liter

---

## suppliers

Columns

- id
- company_name
- supplier_name
- phone
- email
- address
- status

Relationship

One supplier supplies many products.

---

## customers

Columns

- id
- name
- phone
- email
- address
- status
- is_default
- created_at
- updated_at
- deleted_at

Business Rule

A default **Walk-in Customer** is created during seeding (`is_default = true`).
It cannot be deleted and must remain active.

Relationship

One customer has many sales.

---

## products

Columns

- id
- category_id
- brand_id
- supplier_id
- unit_id
- sku
- barcode
- name
- purchase_price
- selling_price
- tax
- discount
- current_stock
- minimum_stock
- maximum_stock
- image
- description
- expiry_date
- status
- created_at
- updated_at
- deleted_at

Relationships

Belongs to Category

Belongs to Brand

Belongs to Supplier

Belongs to Unit

Has Many Purchase Items

Has Many Sale Items

Has Many Inventory Transactions

---

## purchases

Columns

- id
- supplier_id
- user_id
- purchase_number
- purchase_date
- subtotal
- discount
- tax
- total
- payment_status
- notes
- created_at
- updated_at

payment_status values (V1)

paid

unpaid

partial

Note: V1 stores payment status only. Supplier payment history / ledger is deferred to V1.1+.

Relationship

One purchase has many purchase items.

Belongs to Supplier

Belongs to User (created_by cashier/staff)

---

## purchase_items

Columns

- id
- purchase_id
- product_id
- quantity
- purchase_price
- subtotal

Relationship

Belongs to Purchase

Belongs to Product

---

## sales

Columns

- id
- invoice_number
- customer_id
- user_id
- subtotal
- discount
- tax
- total
- payment_method
- payment_status
- notes
- created_at
- updated_at

Relationship

Belongs to Customer

Belongs to User (cashier)

One sale has many sale items.

---

## sale_items

Columns

- id
- sale_id
- product_id
- quantity
- selling_price
- subtotal

Relationship

Belongs to Sale

Belongs to Product

---

## inventory_transactions

Purpose

Store every inventory movement.

Columns

- id
- product_id
- transaction_type
- reference_type
- reference_id
- quantity
- stock_before
- stock_after
- remarks
- created_by
- created_at

Transaction Types (V1)

Purchase

Sale

Adjustment

Manual

Reserved for V1.1+

Return

---

## stock_adjustments

Columns

- id
- product_id
- adjustment_type
- quantity
- stock_before
- stock_after
- reason
- created_by
- created_at
- updated_at

Adjustment Types

Increase

Decrease

Damage

Expired

Lost

---

## expenses

Columns

- id
- category
- amount
- description
- expense_date
- created_by
- created_at
- updated_at

Note: V1 stores category as a text value. A dedicated expense_categories table may be added later.

---

## settings

Key-value style settings store.

Columns

- id
- key
- value
- created_at
- updated_at

Examples of keys

shop_name

shop_logo

currency

tax_rate

receipt_footer

date_format

timezone

theme

---

## audit_logs

Stores every important activity.

Columns

- id
- user_id
- action
- table_name
- record_id
- old_values
- new_values
- ip_address
- created_at

---

## backups

Stores backup history.

Columns

- id
- filename
- backup_date
- created_by

---

# Version 1.0 Scope Notes

Included in V1 schema

- customers
- customer_id and user_id on sales
- user_id on purchases
- payment_status on purchases (field only)
- soft deletes (deleted_at) on products and customers

Explicitly deferred to V1.1+ (do not create in V1)

- purchase_returns
- sale_returns
- supplier_payments / payment ledger tables
- customer credit / due balances

---

# Inventory Flow

Supplier

↓

Purchase

↓

Purchase Items

↓

Increase Product Stock

↓

Inventory Transaction Created

↓

Available For Sale

↓

Sale

↓

Sale Items

↓

Decrease Product Stock

↓

Inventory Transaction Created

---

# Foreign Key Summary

users.role_id

→ roles.id

products.category_id

→ categories.id

products.brand_id

→ brands.id

products.supplier_id

→ suppliers.id

products.unit_id

→ units.id

purchases.supplier_id

→ suppliers.id

purchases.user_id

→ users.id

purchase_items.purchase_id

→ purchases.id

purchase_items.product_id

→ products.id

sales.customer_id

→ customers.id

sales.user_id

→ users.id

sale_items.sale_id

→ sales.id

sale_items.product_id

→ products.id

inventory_transactions.product_id

→ products.id

inventory_transactions.created_by

→ users.id

stock_adjustments.product_id

→ products.id

stock_adjustments.created_by

→ users.id

expenses.created_by

→ users.id

audit_logs.user_id

→ users.id

backups.created_by

→ users.id

---

# Recommended Indexes

Products

sku

barcode

name

category_id

supplier_id

status

Customers

name

phone

Purchases

purchase_number

purchase_date

supplier_id

Sales

invoice_number

customer_id

user_id

created_at

Inventory

product_id

transaction_type

created_at

---

# Migration Order

1. roles
2. permissions
3. role_permissions
4. users
5. categories
6. brands
7. units
8. suppliers
9. customers
10. products
11. purchases
12. purchase_items
13. sales
14. sale_items
15. inventory_transactions
16. stock_adjustments
17. expenses
18. settings
19. audit_logs
20. backups

---

# Database Design Principles

- Third Normal Form (3NF)
- Avoid duplicate data.
- Use foreign keys for referential integrity.
- Use transactions for stock updates.
- Never update stock directly without recording an inventory transaction.
- Use soft deletes where business rules allow.
- Index searchable columns.
- Keep the database extensible for future multi-warehouse and multi-branch support.

---

# End of Document