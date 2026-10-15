# Changelog

**Project:** Professional Offline POS & Inventory Management System

**Version:** 1.0

---

# Purpose

This document records all significant changes made throughout the development of the project.

Every completed feature, bug fix, improvement, or architectural change must be documented here.

The changelog helps developers and stakeholders understand the evolution of the application over time.

---

# Version Format

Version numbers will follow this format:

Major.Minor.Patch

Example:

- 1.0.0
- 1.1.0
- 1.1.1

Meaning:

- **Major** → Large changes or new releases.
- **Minor** → New features.
- **Patch** → Bug fixes and small improvements.

---

# Version 1.0.0

## Initial Project Setup

### Added

- Project documentation
- Development roadmap
- Database architecture
- Coding standards
- UI/UX guidelines
- Module specifications
- Testing guide

### Updated

- Locked Version 1.0 scope: deferred purchase returns, sale returns/refunds, and supplier payment ledger to V1.1+
- Database design: added customers, sales.customer_id, user_id on sales/purchases, soft deletes, settings key-value model

### Status

Completed

---

## Version 1.0.0 — Phase 1

### Added

- Laravel application scaffold (Laravel 13, PHP 8.3)
- SQLite database configuration
- Bootstrap 5 + Bootstrap Icons + SweetAlert2 via Vite
- Session authentication (login / logout / remember me)
- Master layout with sidebar, navbar, and footer
- Login page and dashboard shell
- Default admin seeder (`admin@example.com` / `password`)

### Notes

- Project docs originally specified Laravel 12; scaffold uses current Laravel 13.
- Roles, user CRUD, profile, and change password belong to Phase 2.

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 2

### Added

- Roles table and five seeded roles (Super Admin, Admin, Manager, Inventory Manager, Cashier)
- Permissions table and role_permissions pivot with permission matrix
- User fields: phone, role_id, status
- User management CRUD with search/filter and role assignment
- Profile update and change password
- Active-user middleware and permission middleware
- Inactive users blocked from login

### Database Changes

- Created `roles`, `permissions`, `role_permissions`
- Altered `users` to add `phone`, `role_id`, `status`

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 3

### Added

- Category management module (CRUD, search, status filter)
- Unique category name validation
- Sidebar link gated by `manage_categories` permission

### Database Changes

- Created `categories` table (`name`, `description`, `status`)

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 4

### Added

- Brand management module (CRUD, search, status filter)
- Unique brand name validation
- Sidebar link gated by `manage_brands` permission

### Database Changes

- Created `brands` table (`name`, `description`, `status`)

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 5

### Added

- Unit management module (CRUD, search)
- Default unit seeder (Piece, Box, Bottle, Carton, Kg, Gram, Liter, Meter)
- Sidebar link gated by `manage_units` permission

### Database Changes

- Created `units` table (`name`, `short_name`)

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 6

### Added

- Supplier management module (CRUD, search, status filter)
- Supplier detail page with purchase history placeholder (wired when Purchases module is built)
- Sidebar link gated by `manage_suppliers` permission

### Database Changes

- Created `suppliers` table (`company_name`, `supplier_name`, `phone`, `email`, `address`, `status`)

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 7

### Added

- Customer management module (CRUD, search, status filter, soft deletes)
- Walk-in Customer seeder (protected from delete / rename / deactivate)
- Customer detail purchase history placeholder (wired when Sales module is built)
- Sidebar link gated by `manage_customers` permission

### Database Changes

- Created `customers` table (`name`, `phone`, `email`, `address`, `status`, `is_default`, `deleted_at`)

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 8

### Added

- Product management module (CRUD, soft delete, restore)
- Auto SKU and barcode generation
- Product image upload
- Search and filters (category, brand, supplier, status, low stock, trash)
- Low stock / out of stock badges and alert count

### Deferred

- Excel import/export
- Barcode label printing

### Database Changes

- Created `products` table with FKs to categories, brands, suppliers, units and soft deletes

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 9

### Added

- Purchase management (create, history, invoice/details)
- Multi-item purchase form with live totals
- Automatic stock increase on purchase completion
- Inventory transaction recording for purchases
- Payment status (paid / unpaid / partial)
- Supplier detail page purchase history linked to invoices

### Database Changes

- Created `purchases`, `purchase_items`, `inventory_transactions`

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 10

### Added

- Inventory list with stock value summary cards
- Filters for category, supplier, status, low stock, out of stock
- Inventory transactions / stock movement history
- Per-product stock history page
- Low stock report with shortage calculation

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 11

### Added

- Stock adjustment module (increase, decrease, damage, expired, lost)
- Required reason and user attribution
- Automatic inventory transaction recording on adjustment
- Adjustment history list with filters and detail view
- Sidebar Adjustments link (`manage_stock_adjustments`)

### Database Changes

- Created `stock_adjustments` table (`product_id`, `adjustment_type`, `quantity`, `stock_before`, `stock_after`, `reason`, `created_by`)

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 12

### Added

- POS checkout screen with product search, barcode/SKU scan, category filter, and cart
- Customer selection (defaults to Walk-in Customer)
- Discount, tax, and payment method (cash / card / transfer / other)
- Sale completion via `SaleService` with stock decrease through `InventoryService`
- Invoice numbers (`INV-YYYYMMDD-####`), sales history, and printable receipt
- Customer detail sales history wired to real sale records
- Sidebar: Sales (POS) and Sales History (`manage_sales`)

### Database Changes

- Created `sales` table (`invoice_number`, `customer_id`, `user_id`, totals, payment fields, notes)
- Created `sale_items` table (`sale_id`, `product_id`, `quantity`, `selling_price`, `subtotal`)

### Notes

- Sale returns / refunds remain deferred to V1.1+
- Completing a sale records inventory transaction type `sale`

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 13

### Added

- Expense CRUD (add, edit, delete, view)
- Category as free-text field with suggestions (defaults + previously used)
- Expense list filters (search, category, date range) with filtered total
- Expense report page: period totals, breakdown by category, printable detail list
- Sidebar Expenses link (`manage_expenses`)

### Database Changes

- Created `expenses` table (`category`, `amount`, `description`, `expense_date`, `created_by`)

### Notes

- Dedicated `expense_categories` table deferred; PDF export belongs to Phase 14 Reports module

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 14

### Added

- Reports hub (`/reports`) with `view_reports` permission
- Sales report (daily + monthly aggregates and invoice detail)
- Purchase report (by period/supplier)
- Inventory / stock value report
- Low stock report
- Expense report (by category/period)
- Profit & Loss (sales − COGS − expenses)
- Customer and supplier period summaries
- Print / Save as PDF on all report screens

### Notes

- PDF export uses browser Print → Save as PDF (no DomPDF package; offline-friendly)
- COGS uses sold qty × product current purchase price

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 15

### Added

- Key-value `settings` store with `SettingService` and helpers (`setting()`, `shop_name()`, `money()`)
- Settings page: shop info, logo upload/remove, currency, tax rate, receipt header/footer, date format, timezone, theme
- Shop name and theme applied across app layouts, login, and sidebar
- Receipt uses shop logo, address/phone, header/footer, date format, and currency symbol
- Default settings seeder; timezone applied at boot
- Sidebar Settings link (`manage_settings` — Super Admin / Admin)

### Database Changes

- Created `settings` table (`key`, `value`)

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 16

### Added

- Backup & Restore module for SQLite (`manage_backups`)
- Create backup via SQLite `VACUUM INTO` into `storage/app/backups`
- Backup list with download, restore, and delete
- Automatic safety backup before restore
- Sidebar Backup link (Super Admin / Admin)

### Notes

- V1 backs up the database file only (not uploaded media)
- Automatic/scheduled backups deferred

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 17

### Added

- `audit_logs` table and `AuditService`
- Audit log viewer with filters (action, table, user, date, search) and detail page
- Logging for login/logout, users, products, sales, purchases, stock adjustments, expenses, settings, backups
- Sidebar Audit Logs link (`view_audit_logs` — Super Admin / Admin)

### Database Changes

- Created `audit_logs` table (`user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `created_at`)

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 18

### Added

- Professional dashboard with live metrics (today/month sales, inventory value, products, customers, suppliers, expenses, low/out of stock)
- Low-stock alert banner with links
- Sales line chart (last 14 days) and inventory value by category bar chart (offline canvas charts, no CDN)
- Recent sales, recent purchases, and low-stock alert tables

### Notes

- Charts use built-in Canvas drawing (Chart.js install skipped due to network; remains offline-friendly)

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 19

### Added / Improved

- Global UI polish: spacing, color tokens, card shadows, sidebar nav inset active states
- Form focus styles and refined table typography/padding
- Responsive content padding and mobile table/button tweaks
- Stronger dark-theme surface/form/table support
- Loading overlay + button spinners on form submit
- SweetAlert2 toast notifications for success/error flash messages
- Navbar low-stock bell badge (live count) and Settings shortcut in profile menu
- Login page visual polish

### Status

Completed — awaiting approval

---

## Version 1.0.0 — Phase 20

### Added

- Feature test suite for final verification (`tests/Feature/FinalVerificationTest.php`, `BackupServiceTest.php`)
- Shared test seeding helpers (`tests/Concerns/SeedsApplicationData.php`)
- Phase 20 results section in `docs/08_TESTING_GUIDE.md`

### Verified

- Auth (login/logout/inactive/permissions)
- Purchase stock increase + inventory transactions
- Sale stock decrease + insufficient stock protection
- Stock adjustments
- Backup filename safety + SQLite backup create/delete
- Dashboard and reports permission gates
- Audit logs on login/logout and purchase/sale

### Fixed

- Example feature test expected 200 on `/`; now asserts guest redirect to login

### Status

Completed — awaiting approval (16/16 feature tests passing)

---

## Version 1.0.0 — Phase 21

### Added

- Tauri 2 desktop wrapper under `desktop/`
- Packaging scripts: prepare Laravel runtime, download portable PHP, build NSIS/MSI installers
- Runtime boots bundled PHP + `artisan serve`, uses AppData SQLite on first launch
- Portable non-Tauri launcher (`desktop/scripts/run-portable.ps1`) for packaging smoke tests
- Desktop packaging README

### Updated

- Portable PHP download pinned to current windows.php.net **8.3.33 NTS x64**; script sets `extension_dir=ext` for Laravel extensions
- Desktop bundle id set to `com.posinventory.desktop` (avoid `.app` suffix warning)

### Notes

- Building the `.exe` requires Rust (`rustup` MSVC host) + Visual Studio **C++ Build Tools** on the developer machine
- Portable packaging smoke-tested: bundled PHP + Laravel `/login` returned HTTP 200
- Full NSIS/MSI `.exe` compile on this machine is blocked until MSVC `link.exe` is installed (winget Build Tools failed with exit `5008`; Git `link.exe` must not shadow MSVC)
- End-user install does not require PHP/Composer/Apache/MySQL when packaged with bundled PHP

### Status

Approved

---

## Version 1.0.1 — Remove POS module

### Removed

- POS billing screen and Sales History UI
- Sale create path (`SaleController`, `SaleService`, `StoreSaleRequest`)
- Sales, Profit & Loss, and Customer sales reports
- `manage_sales` permission

### Updated

- Dashboard focuses on purchases and inventory
- Navbar branding: Offline Inventory Management

### Notes

- `sales` / `sale_items` tables and models remain for existing data; no new sales can be created from the app

### Status

Completed

---

## Version 1.0.2 — Remove Purchases module

### Removed

- Purchases UI (list / create / show)
- Purchase create path (`PurchaseController`, `PurchaseService`, `StorePurchaseRequest`)
- Purchase and Supplier purchase reports
- `manage_purchases` permission

### Updated

- Dashboard is inventory-focused (stock value, low stock, expenses)
- Supplier detail page no longer shows purchase history

### Notes

- `purchases` / `purchase_items` tables and models remain for existing data
- Product `purchase_price` (cost) is unchanged

### Status

Completed

---

## Version 1.0.3 — Remove Expenses module

### Removed

- Expenses UI (CRUD + expense report page)
- Expense report under Reports
- `manage_expenses` permission
- Month expenses metric on dashboard

### Notes

- `expenses` table and model remain for existing data

### Status

Completed

---

# Future Version Template

## Version X.X.X

### Added

-

### Updated

-

### Improved

-

### Fixed

-

### Removed

-

### Database Changes

-

### Notes

-

---

# Change Categories

Use the following categories when updating this file:

### Added

New functionality.

Example:

- Product Management module.

---

### Updated

Existing functionality has been enhanced.

Example:

- Improved inventory calculations.

---

### Improved

Performance or UI enhancements.

Example:

- Faster product search.

---

### Fixed

Bug fixes.

Example:

- Fixed incorrect stock calculation after purchase.

---

### Removed

Removed deprecated functionality.

Example:

- Removed unused inventory fields.

---

### Database Changes

Document:

- New tables
- Removed tables
- New columns
- Foreign key changes
- Index changes

---

# Example Entry

## Version 1.2.0

### Added

- Customer Management
- Barcode Printing

### Updated

- Product Search

### Improved

- POS Performance

### Fixed

- Duplicate SKU validation

### Database Changes

- Added customers table.
- Added customer_id to sales table.

### Notes

Stable release.

---

# Release Status

Use one of the following:

- Development
- Alpha
- Beta
- Release Candidate
- Stable
- Production

Current Status:

**Release Candidate** (all 21 phases approved; final NSIS `.exe` compile optional on a machine with MSVC Build Tools)

---

# Cursor Instructions

Whenever a module is completed:

1. Update this changelog.
2. Record the version number.
3. List all completed work.
4. Mention any database changes.
5. Record important notes.

Never modify previous entries.

Always append new versions at the end of this document.

---

# End of Document