# Claude Session Documentation

## Session Start: 2025-08-07

### Context from Previous Session
- User had uncommitted work that was accidentally destroyed with `git reset --hard`
- Recovered files from server including:
  - real_template.php (main unified template with 7 sections)
  - real_components.php (reusable components)
  - template-debug-colors.php (for visualization)

### Current Task: Unified Template Migration
Migrating ALL pages (except login/register/forgot password/privacy) to use the unified template system with real_template.php.

#### Template Structure (7 Sections):
1. **Section 1**: Title/Header (grey background)
2. **Section 2**: Navigation/Categories (grey background)
3. **Section 3**: Metadata (Author, Date, Views) (grey background)
4. **Section 4**: Filters/Sorting (grey background)
5. **Section 5**: Main Content (Posts/Schools/Tests) (grey background)
6. **Section 6**: Pagination (grey background)
7. **Section 7**: Comments Section (blue background) - *Not implemented yet per user request*

#### Available Reusable Components from real_components.php:
- `renderRealTitle()` - Title with optional subtitle
- `renderSearchInline()` - Inline search box
- `renderCategoryNavigation()` - Category navigation tabs
- `renderFiltersDropdown()` - Dropdown for sorting/filtering
- `renderCardsGrid()` - Universal grid for news/posts/tests/schools
- `renderPaginationModern()` - Modern pagination component
- `renderBreadcrumb()` - Breadcrumb navigation

### Migration Progress:
- ✅ Homepage (index.php) - Completed
- ✅ News listing page (pages/common/news/news.php) - Completed
- ✅ News single page (pages/common/news/news-single.php) - Created new file
- ✅ Category page (pages/category/category.php) - Completed
- ✅ Post single page (pages/post/post.php) - Migrated with all sections
- ✅ Schools all regions (schools-all-regions-real.php) - Created new file
- ✅ SPO all regions (spo-all-regions-new.php) - Created new file
- ✅ VPO all regions (vpo-all-regions-new.php) - Created new file
- ✅ Schools in region (schools-in-region-real.php) - Created new file
- ✅ SPO in region (spo-in-region-new.php) - Created new file
- ✅ VPO in region (vpo-in-region-new.php) - Created new file
- ✅ VPO single page (vpo-single-new.php) - Created new file with tabs
- ✅ Tests listing (tests-new.php) - Created new file
- ✅ Search results (search-results-new.php) - Created new file

### Files Created This Session:
1. **schools-all-regions-real.php** - Shows all regions with school counts
2. **spo-all-regions-new.php** - Shows all regions with SPO counts
3. **vpo-all-regions-new.php** - Shows all regions with VPO counts
4. **schools-in-region-real.php** - Lists schools within a specific region
5. **spo-in-region-new.php** - Lists SPO institutions within a region
6. **vpo-in-region-new.php** - Lists VPO institutions within a region
7. **vpo-single-new.php** - Single VPO page with tabs (Info, Contacts, Admission)
8. **tests-new.php** - Tests listing page with categories and filters
9. **search-results-new.php** - Search results page with multiple content types

### Key Implementation Details:
- All pages use consistent 7-section structure
- Breadcrumb navigation on detail pages
- Category navigation tabs on listing pages
- Statistics sections showing counts
- Filter dropdowns and search boxes where appropriate
- Cards grid for displaying items
- Pagination for multi-page results
- Comments section prepared but empty (per user request)

### Remaining Tasks:
- ⏳ Single school page (school-single.php)
- ⏳ Single SPO page
- ⏳ Single test page
- ⏳ Update .htaccess to use new files
- ⏳ Remove old template system files once migration is complete

### Important Notes:
- Use ONLY reusable components from real_components.php
- Do NOT modify the reusable components
- Do NOT add comments functionality yet - will be added later
- Each page should map its content appropriately to the 7 sections
- Leave sections empty if not applicable for that page type

### Git Status at Session Start:
- Current branch: main
- Many modified and untracked files
- Recent commits show performance optimization and 404 fixes

### User Instructions:
- "all pages" should be migrated
- "do not comment yet - we will add later"
- Use real_template.php as the single unified template
- Only real_header.php and real_footer.php should be used (not header.php/footer.php)