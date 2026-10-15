# Testing Guide

**Project:** Professional Offline POS & Inventory Management System

**Version:** 1.0

---

# Purpose

This document defines the testing standards for the POS & Inventory Management System.

Every module must be tested before it is considered complete.

Cursor must perform testing logically after implementing each module and provide a testing checklist.

---

# Testing Principles

Every feature must be tested for:

- Functionality
- Validation
- Business Logic
- Database Integrity
- Security
- Performance
- User Interface
- Error Handling

No module is complete until all tests pass.

---

# Testing Workflow

For each module:

1. Implement the feature.
2. Verify functionality.
3. Verify database changes.
4. Verify validation.
5. Verify permissions.
6. Verify UI.
7. Test edge cases.
8. Fix any issues.
9. Re-test.
10. Wait for user approval.

---

# Authentication Testing

Verify:

- Login works.
- Invalid credentials are rejected.
- Logout works.
- Password change works.
- Unauthorized users cannot access protected pages.
- Session expires correctly.

---

# User Management Testing

Verify:

- Add user.
- Edit user.
- Delete user.
- Activate/deactivate user.
- Duplicate email is rejected.
- Required fields are validated.
- Password is securely stored.

---

# Category Testing

Verify:

- Add category.
- Edit category.
- Delete category.
- Duplicate category names are rejected.
- Search works.
- Status changes correctly.

---

# Brand Testing

Verify:

- Add brand.
- Edit brand.
- Delete brand.
- Search works.
- Duplicate names are handled.

---

# Unit Testing

Verify:

- Add unit.
- Edit unit.
- Delete unit.
- Duplicate names are rejected.

---

# Supplier Testing

Verify:

- Add supplier.
- Edit supplier.
- Delete supplier.
- Phone validation.
- Email validation.
- Search works.

---

# Customer Testing

Verify:

- Add customer.
- Edit customer.
- Delete customer.
- Search works.
- Walk-in Customer exists after installation.
- Customer purchase history loads correctly.

---

# Product Testing

Verify:

- Add product.
- Edit product.
- Delete product.
- Restore product.
- Upload image.
- Generate barcode.
- SKU uniqueness.
- Barcode uniqueness.
- Low stock alert.
- Search.
- Filters.

---

# Purchase Testing

Verify:

- Purchase creation.
- Purchase editing.
- Purchase details.
- Purchase history.
- Stock increases correctly.
- Inventory transaction is created.
- Totals are calculated correctly.

---

# Inventory Testing

Verify:

- Current stock.
- Stock history.
- Inventory movement.
- Inventory value.
- Low stock report.

Business Rule:

Stock must always remain accurate.

---

# Stock Adjustment Testing

Verify:

- Increase stock.
- Decrease stock.
- Damage adjustment.
- Expired adjustment.
- Lost adjustment.
- Manual adjustment.

Ensure:

Inventory transaction is recorded.

---

# Sales (POS) Testing

Verify:

- Product search.
- Barcode search.
- Shopping cart.
- Quantity update.
- Discount.
- Tax calculation.
- Customer selection.
- Payment method.
- Checkout.
- Receipt generation.
- Invoice generation.

Business Rule:

Completing a sale must:

- Reduce inventory.
- Create inventory transaction.
- Generate invoice.
- Save payment.

---

# Expense Testing

Verify:

- Add expense.
- Edit expense.
- Delete expense.
- Expense reports.

---

# Report Testing

Verify:

- Sales reports.
- Purchase reports.
- Inventory reports.
- Customer reports.
- Supplier reports.
- Profit & Loss.
- Expense reports.

Verify:

- Printing.
- PDF export.
- Date filters.

---

# Settings Testing

Verify:

- Shop information.
- Logo upload.
- Currency.
- Tax.
- Receipt settings.
- Date format.

---

# Audit Log Testing

Verify:

- Login activity.
- Product changes.
- Purchase activity.
- Sales activity.
- Stock adjustments.
- User actions.

---

# Database Testing

Verify:

- Foreign keys.
- Relationships.
- Cascade rules.
- Soft deletes.
- Transactions.
- Data integrity.

No orphan records should exist.

---

# Validation Testing

Every form must test:

- Empty fields.
- Invalid values.
- Duplicate values.
- Negative values.
- Invalid file types.
- Large file uploads.
- Invalid dates.

---

# Security Testing

Verify:

- Authentication.
- Authorization.
- CSRF protection.
- SQL Injection prevention.
- XSS prevention.
- Password hashing.
- Unauthorized page access.

---

# UI Testing

Verify:

- Responsive layout.
- Sidebar navigation.
- Navbar.
- Forms.
- Buttons.
- Tables.
- Icons.
- Toast notifications.
- SweetAlert confirmations.
- Loading indicators.

---

# Performance Testing

Verify:

- Product search speed.
- Large product lists.
- Large sales history.
- Report generation.
- Inventory calculations.

The application should remain responsive with thousands of records.

---

# Backup & Restore Testing

Verify:

- Backup creation.
- Backup download.
- Database restore.
- Restored data integrity.

---

# Regression Testing

Whenever a new feature is added:

Verify that existing modules still work correctly.

Examples:

Adding Purchases should not break Products.

Adding Sales should not break Inventory.

Adding Reports should not affect Dashboard.

---

# Bug Reporting

Every bug report should include:

- Module
- Description
- Steps to reproduce
- Expected result
- Actual result
- Severity
- Screenshot (if applicable)

---

# Module Completion Checklist

Before marking a module complete:

- Functionality works.
- Validation works.
- Business rules are followed.
- Database updates correctly.
- UI is complete.
- No critical errors exist.
- Code follows coding standards.

Only after completing this checklist should the module be approved.

---

# Cursor Instructions

After completing each module:

1. Perform a logical review of the implementation.
2. List the tests that should be executed.
3. Highlight any known limitations.
4. Wait for user approval before moving to the next module.

Never skip testing.

---

# Phase 20 — Final Verification Results

**Date:** 2026-07-30  
**Status:** Passed (automated suite)

## Automated Suite

Command:

```bash
php artisan test --testsuite=Feature
```

Result: **16 passed** (49 assertions)

Coverage highlights:

| Area | Result |
|------|--------|
| Guest redirect / login page | Pass |
| Invalid login rejected | Pass |
| Login + logout + audit logs | Pass |
| Inactive user blocked | Pass |
| Cashier forbidden from settings/users/reports | Pass |
| Cashier can open POS | Pass |
| Purchase increases stock + inventory tx + audit | Pass |
| Sale decreases stock; insufficient stock blocked | Pass |
| Stock adjustment (damage) updates stock | Pass |
| Password hashing | Pass |
| Backup filename security + SQLite VACUUM backup | Pass |
| Dashboard loads | Pass |

## Manual Checklist (recommended before packaging)

- [ ] Login as Super Admin, Cashier, and Inventory Manager
- [ ] Create purchase and confirm stock increases
- [ ] Complete POS sale and confirm stock decreases + receipt print
- [ ] Run Sales / P&L / Low Stock reports
- [ ] Create backup, download, restore on a copy DB
- [ ] Toggle Settings theme and shop name
- [ ] Confirm low-stock navbar badge updates

## Known Limitations

- Backup create cannot run inside an open DB transaction (normal for SQLite VACUUM INTO).
- PDF export uses browser Print then Save as PDF.
- COGS in P&L uses current product purchase price times sold qty.
- Automatic/scheduled backups deferred.

---

# End of Document
