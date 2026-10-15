# Module Specifications

**Project:** Professional Offline POS & Inventory Management System  
**Version:** 1.0

---

# Purpose

This document defines the functional specifications for every module in the application.

Each module contains:

- Purpose
- Features
- Business Rules
- Validations
- Database Tables
- User Permissions
- Expected Outcome

Cursor must follow these specifications while implementing each module.

---

# Module 1 — Authentication

## Purpose

Provide secure access to the application.

## Features

- Login
- Logout
- Change Password
- Forgot Password (Future)
- Profile Management

## Validation

- Email required
- Password required
- Strong password validation

## Database

users

---

## Permission Matrix (V1)

| Permission | Super Admin | Admin | Manager | Inventory Manager | Cashier |
|---|---|---|---|---|---|
| view_dashboard | ✓ | ✓ | ✓ | ✓ | ✓ |
| manage_users | ✓ | ✓ | | | |
| manage_categories | ✓ | ✓ | ✓ | ✓ | |
| manage_brands | ✓ | ✓ | ✓ | ✓ | |
| manage_units | ✓ | ✓ | ✓ | ✓ | |
| manage_suppliers | ✓ | ✓ | ✓ | ✓ | |
| manage_customers | ✓ | ✓ | ✓ | | ✓ |
| manage_products | ✓ | ✓ | ✓ | ✓ | |
| manage_purchases | ✓ | ✓ | ✓ | ✓ | |
| manage_inventory | ✓ | ✓ | ✓ | ✓ | |
| manage_stock_adjustments | ✓ | ✓ | ✓ | ✓ | |
| manage_sales | ✓ | ✓ | ✓ | | ✓ |
| manage_expenses | ✓ | ✓ | ✓ | | |
| view_reports | ✓ | ✓ | ✓ | ✓ | |
| manage_settings | ✓ | ✓ | | | |
| view_audit_logs | ✓ | ✓ | | | |
| manage_backups | ✓ | ✓ | | | |

---

# Module 2 — Dashboard

## Purpose

Display business statistics.

## Features

- Today's Sales
- Monthly Sales
- Total Products
- Total Customers
- Total Suppliers
- Inventory Value
- Low Stock Alert
- Recent Sales
- Recent Purchases
- Sales Chart
- Inventory Chart

---

# Module 3 — User Management

## Purpose

Manage system users.

## Features

- Add User
- Edit User
- Delete User
- Reset Password
- Activate / Deactivate User
- Assign Role

## Database

users

---

# Module 4 — Category Management

## Features

- Add Category
- Edit Category
- Delete Category
- Search
- Status

## Validation

- Name Required
- Unique Name

## Database

categories

---

# Module 5 — Brand Management

## Features

- Add Brand
- Edit Brand
- Delete Brand
- Search

## Validation

- Brand Name Required

## Database

brands

---

# Module 6 — Unit Management

## Features

- Add Unit
- Edit Unit
- Delete Unit
- Search

Examples

- Piece
- Box
- Kg
- Gram
- Liter
- Carton

## Database

units

---

# Module 7 — Supplier Management

## Features

- Add Supplier
- Edit Supplier
- Delete Supplier
- Search
- Purchase History

## Validation

- Supplier Name Required
- Phone Required

## Database

suppliers

---

# Module 8 — Customer Management

## Features

- Add Customer
- Edit Customer
- Delete Customer
- Search Customer
- Customer Purchase History

## Business Rule

The system shall automatically create a default **Walk-in Customer** during database seeding.

If the cashier does not select a customer, the sale shall automatically use the Walk-in Customer record.

## Database

customers

---

# Module 9 — Product Management

## Features

- Add Product
- Edit Product
- Delete Product
- Restore Product
- Product Image
- Barcode
- SKU
- Search
- Filter
- Import Excel
- Export Excel
- Print Barcode
- Low Stock Alert

## Validation

- Product Name Required
- Category Required
- Supplier Required
- Purchase Price >= 0
- Selling Price >= Purchase Price
- SKU Unique
- Barcode Unique

## Database

products

---

# Module 10 — Purchase Management

## Features (V1)

- Create Purchase
- Purchase Invoice
- Purchase History
- Purchase Details
- Payment Status (paid / unpaid / partial)

## Deferred to V1.1+

- Supplier Payments ledger
- Purchase Returns

## Business Rule

Completing a purchase must:

- Increase stock.
- Create inventory transaction.
- Record purchase history.
- Store payment_status (no payment history table in V1).

## Database

purchases

purchase_items

inventory_transactions

---

# Module 11 — Inventory Management

## Features

- Inventory List
- Current Stock
- Stock History
- Inventory Transactions
- Stock Value
- Low Stock Report

## Business Rule

Stock must never become negative.

Every stock movement must create an inventory transaction.

---

# Module 12 — Stock Adjustment

## Features

- Increase Stock
- Decrease Stock
- Damaged Items
- Expired Items
- Lost Items
- Manual Adjustment

## Business Rule

Every adjustment must:

- Record previous quantity.
- Record new quantity.
- Save adjustment reason.
- Save user who performed adjustment.

---

# Module 13 — Sales (POS)

## Features (V1)

- Barcode Scanner
- Product Search
- Shopping Cart
- Quantity Update
- Discount
- Tax
- Customer Selection
- Payment Method
- Checkout
- Receipt Printing
- Invoice Generation

## Deferred to V1.1+

- Sale Returns
- Refunds

## Business Rule

Completing a sale must:

- Reduce inventory.
- Create inventory transaction.
- Generate invoice.
- Record payment method and payment status.
- Use Walk-in Customer when no customer is selected.

## Validation

- Product Required
- Quantity > 0
- Stock Available
- Customer Required (defaults to Walk-in)
- Payment Method Required

---

# Module 14 — Expense Management

## Features

- Add Expense
- Edit Expense
- Delete Expense
- Expense Categories
- Expense Reports

## Database

expenses

---

# Module 15 — Reports

## Reports

- Daily Sales
- Monthly Sales
- Purchase Report
- Inventory Report
- Stock Report
- Supplier Report
- Customer Report
- Profit & Loss
- Expense Report

## Features

- Print
- PDF Export
- Date Filters

---

# Module 16 — Settings

## Features

- Shop Information
- Logo
- Currency
- Tax
- Receipt Settings
- Date Format
- Time Zone

---

# Module 17 — Audit Logs

## Features

Track:

- Login
- Logout
- Product Changes
- Purchase Changes
- Sales
- Stock Adjustments
- User Activities

## Database

audit_logs

---

# Common Validation Rules

Every form must validate:

- Required Fields
- Data Types
- Numeric Values
- Unique Values
- Image Uploads
- File Size
- Date Formats

---

# Common UI Rules

Every management page shall include:

- Add Button
- Edit Button
- Delete Button
- Search
- Pagination
- Sorting
- Filters
- Status Badge

---

# Common Business Rules

- Stock can never be negative.
- Every purchase increases inventory.
- Every sale decreases inventory.
- Every adjustment creates an inventory transaction.
- Every sale must have a customer.
- Default customer is Walk-in Customer.
- Products cannot be hard-deleted if they are referenced by transactions; use soft deletes.
- Soft Deletes should be used where appropriate.

---

# Version 1.0 Scope

## In Scope

Authentication, users/roles, dashboard, categories, brands, units, suppliers, customers, products, purchases (with payment_status), inventory, stock adjustments, POS sales, expenses, reports, settings, audit logs, backup & restore, Windows packaging.

## Out of Scope (V1.1+)

Purchase returns, sale returns/refunds, supplier payment ledger, customer credit/due balances, multi-branch, multi-warehouse, cloud sync.

---

# Cursor Instructions

For every module:

1. Read this document.
2. Read the database design.
3. Explain the implementation.
4. Generate production-ready code.
5. Test the module.
6. Stop and wait for approval.

Never implement multiple modules in one step.

---

# End of Document