# URL Redirect & Path Fix Checklist

## Core Infrastructure
- [ ] **connection.php** - No changes needed
- [ ] **auth_required.php** - Fix redirect paths from `../pages/` to `pages/` (for root context)
- [ ] **cart_helpers.php** - No changes needed

## Header & Footer (critical - included from multiple contexts)
- [ ] **includes/header.php** - Make all nav links work from BOTH root and pages/ context
- [ ] **includes/footer.php** - Make all quick links work from BOTH contexts

## Root-Level Files
- [ ] **index.php** - Fix `../product-category.php` to `./pages/product-category.php`
- [ ] **add_to_cart.php** - Fix redirects from `product.php`/`cart.php` to `pages/product.php`/`pages/cart.php`
- [ ] **product-details.php** - Fix includes and links (header/footer includes, all page URLs)
- [ ] **process_complaint.php** - Fix `header('Location: complaints.php')` to `pages/complaints.php`
- [ ] **process_message.php** - Fix `header("Location: contact.php")` to `pages/contact.php`
- [ ] **setup_products.php** - Fix links to pages/ directory
- [ ] **view_orders.php** (root) - Fix all links and redirects

## Pages Directory
- [ ] **pages/login.php** - Fix redirect paths, footer include, admin redirect
- [ ] **pages/signup.php** - Fix `index.php` link to `../index.php`
- [ ] **pages/logout.php** - Fix contact link
- [ ] **pages/product.php** - Fix product-details.php and add_to_cart.php links (need `../`)
- [ ] **pages/product-category.php** - Fix includes, links, redirect (most broken file)
- [ ] **pages/cart.php** - Fix CSS paths
- [ ] **pages/order.php** - Fix email_config.php include
- [ ] **pages/my_orders.php** - Fix includes (header, footer, auth_required, connection)
- [ ] **pages/about.php** - Fix self-referencing `./pages/` links
- [ ] **pages/complaints.php** - Fix form action to `../process_complaint.php`
- [ ] **pages/contact.php** - Fix form action to `../process_message.php`
- [ ] **pages/profile.php** - Verify paths (looks mostly correct)

## Admin Directory
- [ ] **Admin/admin.php** - Fix connection.php include, photo.php link
- [ ] **Admin/adlogout.php** - Fix redirect to `../pages/login.php`
- [ ] **Admin/order_detail.php** - Fix connection.php include
- [ ] **Admin/view_orders.php** - Fix connection.php include, fix `order_row['']` bug
- [ ] **Admin/upload_photo.php** - Fix connection.php include
- [ ] **Admin/view_complaints.php** - Fix connection.php include
- [ ] **Admin/view_messages.php** - Fix connection.php include
- [ ] **Admin/login_activity.php** - Fix connection.php include

## Verification
- [ ] Run `php -l` syntax check on all modified files
- [ ] Test navigation from root (index.php → all pages → back)
- [ ] Test navigation from pages/ (any page → root → another page)
