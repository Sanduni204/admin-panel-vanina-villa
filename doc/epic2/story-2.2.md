# Story 2.2: Dine & Relax Content CMS

**Epic:** Core Content Management  
**Story ID:** 2.2  
**Priority:** HIGH  
**Status:** In Progress  
**Estimated Duration:** 3-4 working days  
**Last Updated:** January 6, 2026

---

## Story Description

As an **admin**, I need to **manage Dine & Relax page content** with flexible, customizable content blocks (e.g., restaurant, bar & coffee shop, pool, beach) and **publish downloadable menus** (Beverage, Snacking, Today, Breakfast) using a simplified, multi-page interface so I can efficiently manage content without a cluttered admin form.

---

## Acceptance Criteria

### Page Overview (Hero)
- [ ] Edit hero tagline, title, lead paragraph, and cover image (with alt text).
- [ ] Publish/unpublish page per locale.

### Custom Content Blocks
- [x] Add blocks via dedicated "Create Block" page (no predefined topics; themes/names chosen by admin).
- [x] Edit blocks via dedicated "Edit Block" page with all fields.
- [x] Delete blocks from main blocks list with confirmation.
- [x] Each block supports: name, heading, body copy, feature image with alt text.
- [x] Gallery images upload (multiple) with shared alt text and sequential display order; add after block creation.
- [ ] Gallery image reordering/deletion UI (future).
- [x] Display order management via numeric input.

### Menu Downloads
- [x] Upload/replace menu PDFs via dedicated menus page (PDF only, max 15 MB) - OR provide external link.
- [x] Per-menu card image upload with alt text (both EN and FR).
- [x] Per-menu type selection (physical PDF or external link).
- [x] Per-menu description field for additional context.
- [x] Per-menu title and button label; optional version/date note; visibility toggle without deleting file.
- [x] Each menu has its own form on dedicated create/edit pages.
- [x] Menu categories system with nested items for structured menu organization.
- [ ] Front-end shows menu cards (image, title, description, download/link button) and serves the latest active file with correct headers.

---

## Database Schema

### dine_relax_pages
```sql
CREATE TABLE dine_relax_pages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    hero_image_path VARCHAR(255),
    hero_image_alt VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### dine_relax_page_translations
```sql
CREATE TABLE dine_relax_page_translations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    dine_relax_page_id BIGINT UNSIGNED NOT NULL,
    locale VARCHAR(5) NOT NULL,
    hero_tagline VARCHAR(255),
    hero_title VARCHAR(255),
    hero_lead TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (dine_relax_page_id) REFERENCES dine_relax_pages(id) ON DELETE CASCADE,
    UNIQUE KEY unique_page_locale (dine_relax_page_id, locale)
);
```

### dine_relax_blocks
```sql
CREATE TABLE dine_relax_blocks (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    dine_relax_page_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(50) NOT NULL,
    image_path VARCHAR(255),
    image_alt VARCHAR(255),
    display_order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (dine_relax_page_id) REFERENCES dine_relax_pages(id) ON DELETE CASCADE,
    UNIQUE KEY unique_page_slug (dine_relax_page_id, slug)
);
```

### dine_relax_block_translations
```sql
CREATE TABLE dine_relax_block_translations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    dine_relax_block_id BIGINT UNSIGNED NOT NULL,
    locale VARCHAR(5) NOT NULL,
    heading VARCHAR(255) NOT NULL,
    body LONGTEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (dine_relax_block_id) REFERENCES dine_relax_blocks(id) ON DELETE CASCADE,
    UNIQUE KEY unique_block_locale (dine_relax_block_id, locale)
);
```

### dine_relax_galleries
```sql
CREATE TABLE dine_relax_galleries (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    dine_relax_block_id BIGINT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    image_alt VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (dine_relax_block_id) REFERENCES dine_relax_blocks(id) ON DELETE CASCADE
);
```

### dine_relax_menus
```sql
CREATE TABLE dine_relax_menus (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(255) NOT NULL,  -- Changed from ENUM to varchar for flexibility
    file_path VARCHAR(255) NULL,  -- Nullable to support link-only menus
    file_name VARCHAR(255),
    file_mime VARCHAR(100),
    card_image_path VARCHAR(255),
    card_image_alt VARCHAR(255),
    card_image_alt_fr VARCHAR(255),  -- New: French alt text for card image
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### dine_relax_menu_translations
```sql
CREATE TABLE dine_relax_menu_translations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    dine_relax_menu_id BIGINT UNSIGNED NOT NULL,
    locale VARCHAR(5) NOT NULL,
    title VARCHAR(255) NOT NULL,
    button_label VARCHAR(100) NOT NULL,
    description TEXT NULL,  -- New: additional description field
    version_note VARCHAR(100) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (dine_relax_menu_id) REFERENCES dine_relax_menus(id) ON DELETE CASCADE,
    UNIQUE KEY unique_menu_locale (dine_relax_menu_id, locale)
);
```

### dine_relax_menu_categories
```sql
CREATE TABLE dine_relax_menu_categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    dine_relax_menu_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (dine_relax_menu_id) REFERENCES dine_relax_menus(id) ON DELETE CASCADE
);
```

### dine_relax_menu_category_items
```sql
CREATE TABLE dine_relax_menu_category_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    dine_relax_menu_category_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (dine_relax_menu_category_id) REFERENCES dine_relax_menu_categories(id) ON DELETE CASCADE
);
```

---

## Models

```php
class DineRelaxPage extends Model {
    protected $fillable = ['hero_image_path', 'hero_image_alt'];
    public function translations() { return $this->hasMany(DineRelaxPageTranslation::class); }
    public function blocks() { return $this->hasMany(DineRelaxBlock::class); }
}

class DineRelaxPageTranslation extends Model {
    protected $fillable = ['dine_relax_page_id','locale','hero_tagline','hero_title','hero_lead'];
}

class DineRelaxBlock extends Model {
    protected $fillable = ['dine_relax_page_id','name','slug','image_path','image_alt','display_order'];
    public function page() { return $this->belongsTo(DineRelaxPage::class, 'dine_relax_page_id'); }
    public function translations() { return $this->hasMany(DineRelaxBlockTranslation::class); }
    public function gallery() { return $this->hasMany(DineRelaxGallery::class)->orderBy('display_order'); }
}

class DineRelaxBlockTranslation extends Model {
    protected $fillable = ['dine_relax_block_id','locale','heading','body'];
}

class DineRelaxGallery extends Model {
    protected $fillable = ['dine_relax_block_id','image_path','image_alt','display_order'];
}

class DineRelaxMenu extends Model {
    protected $fillable = ['type','file_path','file_name','file_mime','card_image_path','card_image_alt','card_image_alt_fr','is_active','display_order'];
    public function translations() { return $this->hasMany(DineRelaxMenuTranslation::class); }
    public function categories() { return $this->hasMany(DineRelaxMenuCategory::class)->orderBy('display_order'); }
}

class DineRelaxMenuTranslation extends Model {
    protected $fillable = ['dine_relax_menu_id','locale','title','button_label','description','version_note'];
}

class DineRelaxMenuCategory extends Model {
    protected $fillable = ['dine_relax_menu_id','name','display_order'];
    public function menu() { return $this->belongsTo(DineRelaxMenu::class, 'dine_relax_menu_id'); }
    public function items() { return $this->hasMany(DineRelaxMenuCategoryItem::class)->orderBy('display_order'); }
}

class DineRelaxMenuCategoryItem extends Model {
    protected $fillable = ['dine_relax_menu_category_id','name','display_order'];
    public function category() { return $this->belongsTo(DineRelaxMenuCategory::class, 'dine_relax_menu_category_id'); }
}
```

---

## Controller

### DineRelaxController (Admin)
```
- edit()        - Show main page with hero form and blocks list table
- update()      - Save hero section only (image, tagline, title, lead)
- blockCreate() - Show empty block creation form
- blockEdit()   - Show block edit form with existing data
- blockStore()  - Create new block or update existing block (handles both) + optional gallery uploads
- blockDelete() - Delete a block by ID
```

### DineRelaxMenuController (Admin/Public)
```
- index()             - Show menus management page with list of all menus
- create()            - Show menu creation form
- edit(menu)          - Show menu edit form with existing data
- storeOrUpdate()     - Create or update menu (PDF/link, card image, translations, categories)
- toggle(menu)        - Activate/deactivate a menu without deletion
- destroy(menu)       - Delete a menu
- download(menu)      - Serve PDF with correct headers (if file-based menu)
```

### DineRelaxMenuCategoryController (Admin)
```
- store(menu)         - Create new category for a menu
- update(category)    - Update category name and order
- destroy(category)   - Delete a category and its items
```

### DineRelaxPageController (Public)
```
- show() - Render public Dine & Relax page with hero, blocks, gallery, and menu cards
```

---

## Routes

```php
Route::middleware(['auth','role:admin'])->prefix('admin')->group(function () {
    Route::get('dine-relax', [DineRelaxController::class, 'edit'])->name('dine-relax.edit');
    Route::post('dine-relax', [DineRelaxController::class, 'update'])->name('dine-relax.update');

    // Block Management (Separate Pages)
    Route::get('dine-relax/blocks/create', [DineRelaxController::class, 'blockCreate'])->name('dine-relax.blocks.create');
    Route::post('dine-relax/blocks', [DineRelaxController::class, 'blockStore'])->name('dine-relax.blocks.store');
    Route::get('dine-relax/blocks/{block}/edit', [DineRelaxController::class, 'blockEdit'])->name('dine-relax.blocks.edit');
    Route::put('dine-relax/blocks/{block}', [DineRelaxController::class, 'blockStore'])->whereNumber('block')->name('dine-relax.blocks.update');
    Route::delete('dine-relax/blocks/{block}', [DineRelaxController::class, 'blockDelete'])->name('dine-relax.blocks.delete');

    // Menu Management (Dedicated Pages)
    Route::get('dine-relax/menus', [DineRelaxMenuController::class, 'index'])->name('dine-relax.menus.index');
    Route::get('dine-relax/menus/create', [DineRelaxMenuController::class, 'create'])->name('dine-relax.menus.create');
    Route::post('dine-relax/menus', [DineRelaxMenuController::class, 'store'])->name('dine-relax.menus.store');
    Route::get('dine-relax/menus/{menu}/edit', [DineRelaxMenuController::class, 'edit'])->name('dine-relax.menus.edit');
    Route::put('dine-relax/menus/{menu}', [DineRelaxMenuController::class, 'update'])->name('dine-relax.menus.update');
    Route::delete('dine-relax/menus/{menu}', [DineRelaxMenuController::class, 'destroy'])->name('dine-relax.menus.destroy');
    Route::post('dine-relax/menus/{menu}/toggle', [DineRelaxMenuController::class, 'toggle'])->name('dine-relax.menus.toggle');
    
    // Menu Category Management
    Route::post('dine-relax/menus/{menu}/categories', [DineRelaxMenuCategoryController::class, 'store'])->name('dine-relax.menu-categories.store');
    Route::put('dine-relax/menu-categories/{category}', [DineRelaxMenuCategoryController::class, 'update'])->name('dine-relax.menu-categories.update');
    Route::delete('dine-relax/menu-categories/{category}', [DineRelaxMenuCategoryController::class, 'destroy'])->name('dine-relax.menu-categories.destroy');
});

Route::get('/dine-relax', [DineRelaxPageController::class, 'show'])->name('dine-relax.show');
Route::get('/dine-relax/menu/{menu}/download', [DineRelaxMenuController::class, 'download'])
    ->name('dine-relax.menu.download');
```

---

## Views (Blade Templates)

### Admin Views
1. `resources/views/admin/dine-relax/edit.blade.php`
   - Hero section form (image, tagline, title, lead) with bilingual fields
   - Blocks list table showing Name, Heading EN/FR, with Edit/Delete actions
   - "Add Block" button linking to block creation page
   - Link to dedicated menus management page

2. `resources/views/admin/dine-relax/block-form.blade.php`
   - Dedicated page for creating/editing individual blocks
   - Sections: Basic Info (name, image, display order), English Content, French Content, CTA
   - Cancel button returns to main edit page

3. `resources/views/admin/dine-relax/menus-index.blade.php`
   - List of all menus with their details (type, title, status)
   - Actions: Create, Edit, Delete, Toggle Active
   - Each row displays menu type, titles (EN/FR), and active status

4. `resources/views/admin/dine-relax/menus-create.blade.php`
   - Dedicated page for creating new menu
   - Sections: Type selection, PDF upload OR external link, card image, bilingual fields (title, button label, description)
   - Optional menu categories with nested items
   - Cancel/Save buttons

5. `resources/views/admin/dine-relax/menus-edit.blade.php`
   - Dedicated page for editing existing menu
   - Pre-filled form with all current data
   - Support for updating PDF/link, card image, translations, and categories
   - Delete button with confirmation

6. Admin navigation and dashboard
   - Dashboard Quick Action "Menus" links directly to menus index page
   - Simplified description: "Manage Dine & Relax menu PDFs"

### Public Views
1. `resources/views/pages/dine-relax.blade.php`
   - Hero, Restaurant, Bar & Coffee Shop (slider), Pool, Beach
   - Dynamic menu cards based on active menus (not limited to four)
   - Each card can be PDF download or external link

2. `resources/views/components/menu-download-card.blade.php`
   - Card component for title, description, version note, button label
   - Support for both download and external link types

---

## Component: Bilingual Editor

Reusable blade component for EN/FR inputs (titles, leads, headings, button labels).

```blade
<x-bilingual-editor 
    field="hero_title"
    label="Hero Title"
    value_en="{{ $page->translation('en')->hero_title ?? '' }}"
    value_fr="{{ $page->translation('fr')->hero_title ?? '' }}"
/>
```

---

## Image & File Upload

### Upload Requirements
- Images: JPG/PNG/WebP, max 5 MB, alt text required when image present.
- PDFs: Max 15 MB, PDF only, stored per menu type.
- Signed URLs for downloads; correct content disposition/filename.

### Storage
- Images: `storage/app/public/dine-relax/blocks/{slug}/` and `.../gallery/`
- PDFs: `storage/app/public/dine-relax/menus/{type}/`
- Ensure `php artisan storage:link` is set up.

---

## Validation Rules

```php
$pageRules = [
    'hero_image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
    'hero_image_alt' => 'required_with:hero_image|string|max:255',
    'hero_title.*' => 'required|string|max:255',
    'hero_tagline.*' => 'nullable|string|max:255',
    'hero_lead.*' => 'nullable|string',
];

$blockRules = [
    'name' => 'required|string|max:255',
    'heading_en' => 'required|string|max:255',
    'heading_fr' => 'required|string|max:255',
    'body_en' => 'nullable|string',
    'body_fr' => 'nullable|string',
    'image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
    'image_alt' => 'required_with:image|string|max:255',
    'display_order' => 'nullable|integer',
    'gallery_images' => 'nullable|array',
    'gallery_images.*' => 'image|mimes:jpeg,png,webp|max:5120',
    'gallery_images_alt' => 'nullable|string|max:255',
];

$galleryRules = [
    'gallery.*.image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
    'gallery.*.image_alt' => 'required_with:gallery.*.image|string|max:255',
];

$menuRules = [
    'type' => 'required|string|max:255',
    'file' => 'nullable|mimes:pdf|max:15360',
    'external_link' => 'nullable|url|max:500',
    'card_image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
    'card_image_alt' => 'required_with:card_image|string|max:255',
    'card_image_alt_fr' => 'required_with:card_image|string|max:255',
    'title_en' => 'required|string|max:255',
    'title_fr' => 'required|string|max:255',
    'description_en' => 'nullable|string',
    'description_fr' => 'nullable|string',
    'button_label_en' => 'required|string|max:100',
    'button_label_fr' => 'required|string|max:100',
    'version_note_en' => 'nullable|string|max:100',
    'version_note_fr' => 'nullable|string|max:100',
    'is_active' => 'sometimes|boolean',
];

$categoryRules = [
    'categories.*.name' => 'required|string|max:255',
    'categories.*.display_order' => 'nullable|integer',
    'categories.*.items.*.name' => 'required|string|max:255',
    'categories.*.items.*.display_order' => 'nullable|integer',
];
```

---

## Implementation Checklist

- [x] Create migrations (pages, translations, blocks, galleries, menus, menu translations, menu categories, menu category items).
- [x] Build enhanced admin interface:
    - [x] Main edit page with hero form and blocks list table.
    - [x] Separate block creation page with full form.
    - [x] Separate block edit page with pre-filled form.
    - [x] Block delete via DELETE request from main page.
    - [x] Dedicated menus index page with list view.
    - [x] Separate menu create page with full form.
    - [x] Separate menu edit page with pre-filled form.
- [x] Block CRUD routes (create, store, edit, update, delete).
- [x] Menu CRUD routes (index, create, store, edit, update, destroy, toggle).
- [x] Menu categories system with nested items support.
- [x] Menu upload/update with card image support (including French alt text).
- [x] Support for both PDF uploads and external links.
- [x] Menu description field for additional context.
- [x] Gallery image upload per block (multiple files, shared alt, sequential order).
- [ ] Gallery image reordering and deletion UI.
- [x] Enforce image/PDF validation and alt-text requirement (including French).
- [ ] Implement signed PDF downloads with correct headers/filenames.
- [ ] Render public page matching live layout (hero, dynamic blocks, gallery slider, dynamic menu cards).
- [ ] Write feature tests for CRUD (blocks, galleries, menus, categories), uploads, toggles, downloads, and locale rendering.

---

## Testing Scenarios

- [x] Navigate to "Add Block" page and create new block with name, headings, body, and main image.
- [x] Edit existing block via Edit button and verify changes persist.
- [x] Delete block from main page and confirm removal.
- [ ] Verify blocks appear on public page in correct display order.
- [x] Upload multiple gallery images to a block and confirm they save with sequential order and alt text.
- [ ] Reorder or delete gallery images (pending UI).
- [x] Navigate to menus index page via dashboard quick action.
- [x] Create new menu via dedicated create page with type, card image, and bilingual fields.
- [x] Upload menu PDF and card image; verify form submission and storage.
- [x] Create menu with external link instead of PDF.
- [x] Add menu categories with nested items.
- [x] Edit existing menu and update fields.
- [ ] Download menu from public page and verify correct file/filename.
- [x] Update menu with new PDF; verify newest file reference saved.
- [x] Toggle menu active/inactive and verify state changes.
- [x] Delete menu and confirm removal.
- [x] Reject invalid images/PDFs (type/size) and require alt text (EN and FR) when images provided.
- [ ] Verify locale fallback for missing menu translations on public page.
- [ ] Test menu categories display and ordering on public page.

---

## Frontend Integration

### Public Page
- `/dine-relax` - Full page with hero and dynamically rendered content blocks, gallery sliders, and four menu cards with downloads.

### Downloads
- `/dine-relax/menu/{menu}/download` - Serves the active PDF for specified menu (if file-based).

---

## Recent Updates (January 6, 2026)

### Menu System Enhancements
The menu management system has been significantly enhanced with the following improvements:

1. **Flexible Menu Types**
   - Changed `type` column from ENUM to VARCHAR for unlimited menu types
   - No longer restricted to four predefined menu types (beverage, snacking, today, breakfast)
   - Admins can create custom menu types as needed

2. **Link Support**
   - Made `file_path` nullable to support external links
   - Menus can now be either PDF uploads or external URLs
   - Useful for linking to third-party menu platforms or external resources

3. **Enhanced Accessibility**
   - Added `card_image_alt_fr` field for French alt text
   - Both English and French alt text now supported for menu card images
   - Improves accessibility compliance for bilingual content

4. **Additional Context**
   - Added `description` field to menu translations
   - Allows admins to provide additional context or promotional text
   - Supports both English and French descriptions

5. **Menu Categories System**
   - New `dine_relax_menu_categories` table for organizing menu content
   - New `dine_relax_menu_category_items` table for nested items
   - Each menu can have multiple categories with ordered items
   - Supports structured menu organization (e.g., Appetizers, Main Courses, Desserts)

6. **Improved Admin Interface**
   - Separate create and edit pages for better UX
   - Index page shows all menus in a list view
   - Full CRUD operations: Create, Read, Update, Delete
   - Better form organization with clear sections

### Database Migrations Created
- `2026_01_06_070236_create_dine_relax_menu_categories_table.php`
- `2026_01_06_070324_create_dine_relax_menu_category_items_table.php`
- `2026_01_06_094733_add_description_to_dine_relax_menu_translations.php`
- `2026_01_06_104859_make_file_path_nullable_in_dine_relax_menus_table.php`
- `2026_01_06_120500_add_card_image_alt_fr_to_dine_relax_menus_table.php`
- `2026_01_06_121000_change_type_column_to_varchar_in_dine_relax_menus.php`

### Files Modified/Created
- Models: `DineRelaxMenu`, `DineRelaxMenuTranslation`, `DineRelaxMenuCategory`, `DineRelaxMenuCategoryItem`
- Controllers: `DineRelaxMenuController`, `DineRelaxMenuCategoryController`
- Views: `menus-index.blade.php`, `menus-create.blade.php`, `menus-edit.blade.php`, `menu-download-card.blade.php`
- Routes: Updated with new CRUD routes and category management routes

---

## Additional Notes
- Use signed/storage URLs; avoid exposing raw storage paths.
- Maintain ordering for blocks and gallery images to match live layout.
- Locale fallback can mirror existing bilingual pattern (e.g., Villa module) if content missing.
- SEO meta fields (title, description, OG image) are managed centrally in Story 2.4: SEO Settings module, not within individual content pages.

---

## Related Stories
- Story 2.1: Villa Management Module
- Story 2.3: Things to Do Module
- Story 2.4: SEO Settings
- Story 3.1: Bilingual Editor Interface
- Story 4.1: Media Upload System
- Story 5.2: Content Display Pages

---

## Dependencies
- None mandatory; optional image processing library if resizing/cropping is desired.

---

## Definition of Done
- [x] Admin can create blocks via dedicated creation page.
- [x] Admin can edit blocks via dedicated edit page with all fields pre-filled.
- [x] Admin can delete blocks from main blocks list.
- [x] Display order managed via numeric input field per block.
- [x] Main admin page simplified: hero form + blocks table + menus link.
- [x] Gallery images can be uploaded (multiple) with shared alt text and sequential order.
- [ ] Gallery images can be reordered or deleted via UI.
- [x] Dedicated menus list page showing all menus.
- [x] Separate create/edit pages for menus with comprehensive forms.
- [x] Each menu supports type selection, PDF upload OR external link, card image upload with alt text (EN/FR), bilingual fields (title, description, button label), version notes, and active toggle.
- [x] Menu categories system with nested items for structured organization.
- [x] Type field changed from ENUM to varchar for flexibility.
- [x] File_path made nullable to support link-only menus.
- [x] French alt text support (card_image_alt_fr) for accessibility.
- [x] Description field added to menu translations for additional context.
- [ ] Menu cards display on public page with working signed PDF downloads (for file-based) or links and correct filenames.
- [x] Validation enforced for images/PDFs; alt text required (EN and FR) when images exist.
- [x] Dashboard quick action labeled "Menus" links to menus index page.
- [ ] Public page renders dynamically with hero and all custom blocks, galleries, and menu cards.
- [ ] Automated feature tests pass for block CRUD, gallery management, menu operations, categories, downloads, and locale rendering.
- [x] Story documentation updated with latest changes.
- [x] All changes committed with appropriate commit messages.
- [x] Code pushed to remote repository.# Story 2.2: Dine & Relax Content CMS

**Epic:** Core Content Management  
**Story ID:** 2.2  
**Priority:** HIGH  
**Status:** Not Started  
**Estimated Duration:** 3-4 working days

---

## Story Description

As an **admin**, I need to **manage all Dine & Relax page content** (restaurant, bar & coffee shop, pool, beach) and **publish downloadable menus** (Beverage, Snacking, Today, Breakfast) so guests see the same experience as the live site and can download each menu.

---

## Acceptance Criteria

### Page Overview (Hero)
- [ ] Edit hero tagline, title, lead paragraph, and cover image (with alt text).
- [ ] Publish/unpublish page per locale.

### Restaurant Block
- [ ] Edit heading and body copy (supports basic formatting) and set a supporting image with alt text.
- [ ] Optional highlights list (e.g., sourcing, tailoring to dietary needs).

### Bar & Coffee Shop Block
- [ ] Edit heading, body, and operating hours.
- [ ] Manage gallery/slider images with ordering and alt text.
- [ ] Configure CTA label and URL (e.g., “Gallery”).

### Pool Block
- [ ] Edit heading and descriptive copy, feature image with alt text, and CTA label + URL.

### Beach Block
- [ ] Edit heading and descriptive copy, feature image with alt text, and CTA label + URL.

### Menu Downloads
- [ ] Upload/replace four menu PDFs: Beverage, Snacking, Today, Breakfast (max 15 MB, PDF only).
- [ ] Per-menu title and button label; optional version/date note; visibility toggle without deleting file.
- [ ] Front-end shows four distinct cards (image, title, download button) and serves the latest active file with correct headers.

---

## Database Schema (minimal)

- dine_relax_pages: hero image + alt, publish flag, meta image.
- dine_relax_page_translations: per-locale hero tagline/title/lead + meta title/description.
- dine_relax_blocks: slugs (restaurant, bar-coffee, pool, beach), image + alt, CTA label/URL, display order.
- dine_relax_block_translations: per-locale heading/body/hours/highlights (JSON array).
- dine_relax_galleries: bar-coffee gallery images with alt + display order.
- dine_relax_menus: four menu types (beverage, snacking, today, breakfast), file path/name/mime, active flag, display order.
- dine_relax_menu_translations: per-locale title, button label, optional version note.

---

## Implementation Checklist

- [ ] Admin single-page form (locale tabs) for hero, blocks, gallery, and four menu uploads.
- [ ] Signed/secure download links with proper PDF headers.
- [ ] Validation: images (jpeg/png/webp, <=5 MB) with required alt; PDFs (<=15 MB, PDF only).
- [ ] Seed default blocks (restaurant, bar-coffee, pool, beach) with display order.
- [ ] Render public page matching the live layout: hero, Restaurant, Bar & Coffee Shop slider, Pool, Beach, and four menu cards with downloads.
- [ ] Feature tests for CRUD, uploads, toggles, downloads, and locale rendering.

---

## Testing Scenarios

- [ ] Upload Beverage, Snacking, Today, and Breakfast PDFs; each card downloads the correct file and filename.
- [ ] Replace a menu file and confirm the newest file is served.
- [ ] Hide one menu card and confirm others remain visible.
- [ ] Update hero/block copy and verify correct locale output.
- [ ] Add bar/coffee gallery images and confirm order and alt text.
- [ ] Reject invalid images/PDFs (type/size) and require alt text when images are provided.

---

## Definition of Done

- [ ] Hero, Restaurant, Bar & Coffee Shop, Pool, and Beach sections editable per locale and visible on the public page.
- [ ] Four menu download cards live with working, signed PDF downloads and correct filenames.
- [ ] Image and PDF validation enforced; all images carry alt text.
- [ ] Automated feature tests for CRUD, uploads, toggles, downloads, and locale rendering are passing.
- [ ] Content matches the live layout shown in the provided screenshots.

---

## Related Stories
- Story 2.1: Villa Management Module
- Story 2.3: Things to Do Module
- Story 2.4: SEO Settings
- Story 3.1: Bilingual Editor Interface
- Story 4.1: Media Upload System
- Story 5.2: Content Display Pages
