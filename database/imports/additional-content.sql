-- MRK Laravel Portal: additional content import (50 posts + 50 services)
-- Safe behavior: INSERT IGNORE skips rows whose unique slug already exists; existing rows are not updated or deleted.
-- Take a fresh database export before import. Confirm the selected database is if0_43057695_lara853.
SET NAMES utf8mb4;
START TRANSACTION;

INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Laravel Website Development Cost Guide','laravel-website-development-cost-guide','Web Development','Understand the main factors that affect Laravel project cost, including features, authentication, database design, integrations, testing and ongoing maintenance.','Understand the main factors that affect Laravel project cost, including features, authentication, database design, integrations, testing and ongoing maintenance.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Laravel Website Development Cost Guide | MRK Digital','Understand the main factors that affect Laravel project cost, including features, authentication, database design, integrations, testing and ongoing mainte','2026-09-01 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('WordPress vs Laravel: Which Should You Choose?','wordpress-vs-laravel-which-should-you-choose','Web Development','Compare WordPress and Laravel by content editing, custom workflows, development complexity, maintenance and the kind of website your business needs.','Compare WordPress and Laravel by content editing, custom workflows, development complexity, maintenance and the kind of website your business needs.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'WordPress vs Laravel: Which Should You Choose? | MRK Digital','Compare WordPress and Laravel by content editing, custom workflows, development complexity, maintenance and the kind of website your business needs.','2026-09-02 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('How to Plan a Small Business Website','how-to-plan-a-small-business-website','Business','Start with your audience, service list, contact path, trust information and mobile layout before selecting a theme or framework.','Start with your audience, service list, contact path, trust information and mobile layout before selecting a theme or framework.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'How to Plan a Small Business Website | MRK Digital','Start with your audience, service list, contact path, trust information and mobile layout before selecting a theme or framework.','2026-09-03 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Technical SEO Checklist for a New Website','technical-seo-checklist-for-a-new-website','SEO','Review page titles, descriptions, canonical URLs, robots rules, sitemap responses, internal links, mobile rendering and indexable content.','Review page titles, descriptions, canonical URLs, robots rules, sitemap responses, internal links, mobile rendering and indexable content.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Technical SEO Checklist for a New Website | MRK Digital','Review page titles, descriptions, canonical URLs, robots rules, sitemap responses, internal links, mobile rendering and indexable content.','2026-09-04 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('How XML Sitemaps Help Search Engines','how-xml-sitemaps-help-search-engines','SEO','An XML sitemap helps search engines discover canonical URLs, but inclusion does not guarantee crawling, indexing or rankings.','An XML sitemap helps search engines discover canonical URLs, but inclusion does not guarantee crawling, indexing or rankings.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'How XML Sitemaps Help Search Engines | MRK Digital','An XML sitemap helps search engines discover canonical URLs, but inclusion does not guarantee crawling, indexing or rankings.','2026-09-05 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('How to Write Useful Meta Descriptions','how-to-write-useful-meta-descriptions','SEO','Write a concise, accurate summary for each important page and avoid repeating generic descriptions across unrelated pages.','Write a concise, accurate summary for each important page and avoid repeating generic descriptions across unrelated pages.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'How to Write Useful Meta Descriptions | MRK Digital','Write a concise, accurate summary for each important page and avoid repeating generic descriptions across unrelated pages.','2026-09-06 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Internal Linking for Service Businesses','internal-linking-for-service-businesses','SEO','Connect related services, guides and contact pages with descriptive anchor text so visitors and crawlers can discover useful pages.','Connect related services, guides and contact pages with descriptive anchor text so visitors and crawlers can discover useful pages.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Internal Linking for Service Businesses | MRK Digital','Connect related services, guides and contact pages with descriptive anchor text so visitors and crawlers can discover useful pages.','2026-09-07 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Google Search Console Indexing Basics','google-search-console-indexing-basics','SEO','Use URL Inspection to review crawl and indexing status, fix the underlying issue, then request indexing when appropriate.','Use URL Inspection to review crawl and indexing status, fix the underlying issue, then request indexing when appropriate.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Google Search Console Indexing Basics | MRK Digital','Use URL Inspection to review crawl and indexing status, fix the underlying issue, then request indexing when appropriate.','2026-09-08 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Local SEO Basics for Small Businesses','local-seo-basics-for-small-businesses','SEO','Keep business details consistent, describe service areas accurately, publish helpful local information and request genuine customer reviews.','Keep business details consistent, describe service areas accurately, publish helpful local information and request genuine customer reviews.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Local SEO Basics for Small Businesses | MRK Digital','Keep business details consistent, describe service areas accurately, publish helpful local information and request genuine customer reviews.','2026-09-09 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Website Speed Improvements for Beginners','website-speed-improvements-for-beginners','Web Development','Start with image sizing, caching, unused assets, hosting response time and measured Core Web Vitals rather than guessing.','Start with image sizing, caching, unused assets, hosting response time and measured Core Web Vitals rather than guessing.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Website Speed Improvements for Beginners | MRK Digital','Start with image sizing, caching, unused assets, hosting response time and measured Core Web Vitals rather than guessing.','2026-09-10 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Responsive Web Design Essentials','responsive-web-design-essentials','Web Development','Use flexible layouts, readable type, accessible controls and real-device checks to keep pages usable on mobile screens.','Use flexible layouts, readable type, accessible controls and real-device checks to keep pages usable on mobile screens.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Responsive Web Design Essentials | MRK Digital','Use flexible layouts, readable type, accessible controls and real-device checks to keep pages usable on mobile screens.','2026-09-11 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Website Security Basics for Small Businesses','website-security-basics-for-small-businesses','Security','Keep dependencies updated, protect credentials, use HTTPS, validate input, restrict admin access and maintain backups.','Keep dependencies updated, protect credentials, use HTTPS, validate input, restrict admin access and maintain backups.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Website Security Basics for Small Businesses | MRK Digital','Keep dependencies updated, protect credentials, use HTTPS, validate input, restrict admin access and maintain backups.','2026-09-12 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Laravel Routes and Controllers Explained','laravel-routes-and-controllers-explained','Laravel','Routes map requests to handlers while controllers organize request logic; keep validation and authorization explicit.','Routes map requests to handlers while controllers organize request logic; keep validation and authorization explicit.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Laravel Routes and Controllers Explained | MRK Digital','Routes map requests to handlers while controllers organize request logic; keep validation and authorization explicit.','2026-09-13 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Laravel Blade Templates: Practical Guide','laravel-blade-templates-practical-guide','Laravel','Blade helps compose reusable page layouts and safely display escaped content while keeping presentation separate from business logic.','Blade helps compose reusable page layouts and safely display escaped content while keeping presentation separate from business logic.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Laravel Blade Templates: Practical Guide | MRK Digital','Blade helps compose reusable page layouts and safely display escaped content while keeping presentation separate from business logic.','2026-09-14 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Laravel Database Migrations Explained','laravel-database-migrations-explained','Laravel','Migrations version database structure. Back up production data and review schema changes before applying them to a live site.','Migrations version database structure. Back up production data and review schema changes before applying them to a live site.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Laravel Database Migrations Explained | MRK Digital','Migrations version database structure. Back up production data and review schema changes before applying them to a live site.','2026-09-15 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Laravel Validation for Contact Forms','laravel-validation-for-contact-forms','Laravel','Validate required fields, lengths, email formats and allowed values on the server; add rate limiting to reduce abuse.','Validate required fields, lengths, email formats and allowed values on the server; add rate limiting to reduce abuse.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Laravel Validation for Contact Forms | MRK Digital','Validate required fields, lengths, email formats and allowed values on the server; add rate limiting to reduce abuse.','2026-09-16 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Laravel Eloquent Models Basics','laravel-eloquent-models-basics','Laravel','Eloquent models represent database records and support readable queries; define fillable fields carefully and avoid exposing sensitive data.','Eloquent models represent database records and support readable queries; define fillable fields carefully and avoid exposing sensitive data.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Laravel Eloquent Models Basics | MRK Digital','Eloquent models represent database records and support readable queries; define fillable fields carefully and avoid exposing sensitive data.','2026-09-17 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Laravel Deployment Checklist','laravel-deployment-checklist','Laravel','Confirm environment settings, dependencies, writable storage, cache configuration, database connectivity and production error logging.','Confirm environment settings, dependencies, writable storage, cache configuration, database connectivity and production error logging.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Laravel Deployment Checklist | MRK Digital','Confirm environment settings, dependencies, writable storage, cache configuration, database connectivity and production error logging.','2026-09-18 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('How to Troubleshoot an HTTP 500 Error','how-to-troubleshoot-an-http-500-error','Troubleshooting','A 500 response is generic. Check application logs, server logs, recent deployments, environment values and database connectivity.','A 500 response is generic. Check application logs, server logs, recent deployments, environment values and database connectivity.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'How to Troubleshoot an HTTP 500 Error | MRK Digital','A 500 response is generic. Check application logs, server logs, recent deployments, environment values and database connectivity.','2026-09-19 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Free Website Hosting: What to Check','free-website-hosting-what-to-check','Hosting','Compare PHP versions, database limits, file permissions, uptime, SSL, backups, domain rules and acceptable-use limits before deployment.','Compare PHP versions, database limits, file permissions, uptime, SSL, backups, domain rules and acceptable-use limits before deployment.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Free Website Hosting: What to Check | MRK Digital','Compare PHP versions, database limits, file permissions, uptime, SSL, backups, domain rules and acceptable-use limits before deployment.','2026-09-20 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('How to Choose a Domain Name','how-to-choose-a-domain-name','Business','Prefer a memorable, easy-to-spell name that matches your brand and can be used consistently across business profiles.','Prefer a memorable, easy-to-spell name that matches your brand and can be used consistently across business profiles.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'How to Choose a Domain Name | MRK Digital','Prefer a memorable, easy-to-spell name that matches your brand and can be used consistently across business profiles.','2026-09-21 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Website Backup and Recovery Planning','website-backup-and-recovery-planning','Security','Keep separate copies of files and database exports, protect backups and periodically test restoration rather than merely assuming it works.','Keep separate copies of files and database exports, protect backups and periodically test restoration rather than merely assuming it works.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Website Backup and Recovery Planning | MRK Digital','Keep separate copies of files and database exports, protect backups and periodically test restoration rather than merely assuming it works.','2026-09-22 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('E-commerce Product Listing Quality Checklist','e-commerce-product-listing-quality-checklist','E-commerce','Use accurate product titles, consistent attributes, clear images, truthful descriptions and carefully checked variations.','Use accurate product titles, consistent attributes, clear images, truthful descriptions and carefully checked variations.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'E-commerce Product Listing Quality Checklist | MRK Digital','Use accurate product titles, consistent attributes, clear images, truthful descriptions and carefully checked variations.','2026-09-23 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Product Category Mapping for Online Stores','product-category-mapping-for-online-stores','E-commerce','Create a manageable category hierarchy with clear definitions, limited overlap and documented rules for edge cases.','Create a manageable category hierarchy with clear definitions, limited overlap and documented rules for edge cases.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Product Category Mapping for Online Stores | MRK Digital','Create a manageable category hierarchy with clear definitions, limited overlap and documented rules for edge cases.','2026-09-24 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Virtual Assistant Workflow for Online Sellers','virtual-assistant-workflow-for-online-sellers','Freelancing','Document recurring tasks, expected formats, deadlines and review rules; use least-privilege access and never share passwords in plain text.','Document recurring tasks, expected formats, deadlines and review rules; use least-privilege access and never share passwords in plain text.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Virtual Assistant Workflow for Online Sellers | MRK Digital','Document recurring tasks, expected formats, deadlines and review rules; use least-privilege access and never share passwords in plain text.','2026-09-25 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Data Entry Quality Control Checklist','data-entry-quality-control-checklist','Freelancing','Use validation rules, duplicate checks, consistent formats, source verification and a final sample review to reduce avoidable errors.','Use validation rules, duplicate checks, consistent formats, source verification and a final sample review to reduce avoidable errors.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Data Entry Quality Control Checklist | MRK Digital','Use validation rules, duplicate checks, consistent formats, source verification and a final sample review to reduce avoidable errors.','2026-09-26 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('How to Prepare a Freelance Service Package','how-to-prepare-a-freelance-service-package','Freelancing','Define deliverables, exclusions, revision limits, timeline, required client inputs and a clear acceptance process.','Define deliverables, exclusions, revision limits, timeline, required client inputs and a clear acceptance process.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'How to Prepare a Freelance Service Package | MRK Digital','Define deliverables, exclusions, revision limits, timeline, required client inputs and a clear acceptance process.','2026-09-27 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Writing a Strong Freelance Proposal','writing-a-strong-freelance-proposal','Freelancing','Refer to the actual requirement, explain a realistic approach, show relevant evidence and ask focused questions about scope.','Refer to the actual requirement, explain a realistic approach, show relevant evidence and ask focused questions about scope.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Writing a Strong Freelance Proposal | MRK Digital','Refer to the actual requirement, explain a realistic approach, show relevant evidence and ask focused questions about scope.','2026-09-28 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Building a Beginner Developer Portfolio','building-a-beginner-developer-portfolio','Career','Show a small number of clear projects with screenshots, technology choices, your contribution and working demo links where available.','Show a small number of clear projects with screenshots, technology choices, your contribution and working demo links where available.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Building a Beginner Developer Portfolio | MRK Digital','Show a small number of clear projects with screenshots, technology choices, your contribution and working demo links where available.','2026-09-29 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('GitHub Basics for Web Developers','github-basics-for-web-developers','Development','Use repositories, meaningful commits, README instructions and branches to track work and make changes reviewable.','Use repositories, meaningful commits, README instructions and branches to track work and make changes reviewable.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'GitHub Basics for Web Developers | MRK Digital','Use repositories, meaningful commits, README instructions and branches to track work and make changes reviewable.','2026-09-30 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Deploying a Static Website on GitHub Pages','deploying-a-static-website-on-github-pages','Hosting','Check repository settings, build output, custom domain configuration, HTTPS and the actual published path.','Check repository settings, build output, custom domain configuration, HTTPS and the actual published path.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Deploying a Static Website on GitHub Pages | MRK Digital','Check repository settings, build output, custom domain configuration, HTTPS and the actual published path.','2026-09-01 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Website Contact Forms: Privacy and Safety','website-contact-forms-privacy-and-safety','Security','Collect only necessary information, explain how it is used, protect submissions and avoid requesting account passwords or identity secrets.','Collect only necessary information, explain how it is used, protect submissions and avoid requesting account passwords or identity secrets.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Website Contact Forms: Privacy and Safety | MRK Digital','Collect only necessary information, explain how it is used, protect submissions and avoid requesting account passwords or identity secrets.','2026-09-02 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('How to Improve Website Accessibility','how-to-improve-website-accessibility','Accessibility','Use semantic headings, labels, keyboard navigation, visible focus states, sufficient contrast and descriptive link text.','Use semantic headings, labels, keyboard navigation, visible focus states, sufficient contrast and descriptive link text.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'How to Improve Website Accessibility | MRK Digital','Use semantic headings, labels, keyboard navigation, visible focus states, sufficient contrast and descriptive link text.','2026-09-03 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Creating Useful FAQ Pages','creating-useful-faq-pages','Content','Answer real customer questions directly, keep answers current and link to the relevant service or official source when needed.','Answer real customer questions directly, keep answers current and link to the relevant service or official source when needed.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Creating Useful FAQ Pages | MRK Digital','Answer real customer questions directly, keep answers current and link to the relevant service or official source when needed.','2026-09-04 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('How to Write Service Page Content','how-to-write-service-page-content','Content','Explain who the service helps, what is included, what is excluded, what the client provides and how to request a quote.','Explain who the service helps, what is included, what is excluded, what the client provides and how to request a quote.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'How to Write Service Page Content | MRK Digital','Explain who the service helps, what is included, what is excluded, what the client provides and how to request a quote.','2026-09-05 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('PLC Automation Project Planning Basics','plc-automation-project-planning-basics','Automation','Document process requirements, I/O lists, operating states, fault behavior and safety constraints before writing control logic.','Document process requirements, I/O lists, operating states, fault behavior and safety constraints before writing control logic.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'PLC Automation Project Planning Basics | MRK Digital','Document process requirements, I/O lists, operating states, fault behavior and safety constraints before writing control logic.','2026-09-06 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Arduino Sensor Project Testing Checklist','arduino-sensor-project-testing-checklist','Automation','Test sensor readings, power stability, wiring, failure conditions and safe output states before relying on a prototype.','Test sensor readings, power stability, wiring, failure conditions and safe output states before relying on a prototype.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Arduino Sensor Project Testing Checklist | MRK Digital','Test sensor readings, power stability, wiring, failure conditions and safe output states before relying on a prototype.','2026-09-07 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Relay Control Safety: What to Consider','relay-control-safety-what-to-consider','Automation','Choose components for the actual load, isolate low-voltage control appropriately and have hazardous mains wiring assessed by a qualified person.','Choose components for the actual load, isolate low-voltage control appropriately and have hazardous mains wiring assessed by a qualified person.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Relay Control Safety: What to Consider | MRK Digital','Choose components for the actual load, isolate low-voltage control appropriately and have hazardous mains wiring assessed by a qualified person.','2026-09-08 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('How to Document an Automation Project','how-to-document-an-automation-project','Automation','Record wiring, I/O mapping, software versions, test results, limitations and a recovery procedure for future troubleshooting.','Record wiring, I/O mapping, software versions, test results, limitations and a recovery procedure for future troubleshooting.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'How to Document an Automation Project | MRK Digital','Record wiring, I/O mapping, software versions, test results, limitations and a recovery procedure for future troubleshooting.','2026-09-09 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('CSV and JSON: Key Differences','csv-and-json-key-differences','Tools','CSV is convenient for tabular rows while JSON represents nested structured data; choose the format that fits the data model.','CSV is convenient for tabular rows while JSON represents nested structured data; choose the format that fits the data model.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'CSV and JSON: Key Differences | MRK Digital','CSV is convenient for tabular rows while JSON represents nested structured data; choose the format that fits the data model.','2026-09-10 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('How to Use a QR Code on a Business Card','how-to-use-a-qr-code-on-a-business-card','Digital Tools','Use a QR code that opens a stable, mobile-friendly destination and test it on multiple devices before printing.','Use a QR code that opens a stable, mobile-friendly destination and test it on multiple devices before printing.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'How to Use a QR Code on a Business Card | MRK Digital','Use a QR code that opens a stable, mobile-friendly destination and test it on multiple devices before printing.','2026-09-11 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Password Manager Basics for Small Teams','password-manager-basics-for-small-teams','Security','Use unique passwords, multifactor authentication, controlled sharing and recovery procedures rather than reusing credentials.','Use unique passwords, multifactor authentication, controlled sharing and recovery procedures rather than reusing credentials.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Password Manager Basics for Small Teams | MRK Digital','Use unique passwords, multifactor authentication, controlled sharing and recovery procedures rather than reusing credentials.','2026-09-12 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Email Deliverability Basics for Websites','email-deliverability-basics-for-websites','Web Development','Configure a reputable mail service and review SPF, DKIM and DMARC records; test both delivery and spam placement.','Configure a reputable mail service and review SPF, DKIM and DMARC records; test both delivery and spam placement.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Email Deliverability Basics for Websites | MRK Digital','Configure a reputable mail service and review SPF, DKIM and DMARC records; test both delivery and spam placement.','2026-09-13 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Website Analytics: Useful Starter Metrics','website-analytics-useful-starter-metrics','Analytics','Track meaningful actions such as enquiries and service-page visits while respecting privacy and applicable consent requirements.','Track meaningful actions such as enquiries and service-page visits while respecting privacy and applicable consent requirements.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Website Analytics: Useful Starter Metrics | MRK Digital','Track meaningful actions such as enquiries and service-page visits while respecting privacy and applicable consent requirements.','2026-09-14 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('How to Create a Simple Business Landing Page','how-to-create-a-simple-business-landing-page','Business','Focus the page on one audience and one primary action, support claims with evidence and make contact options easy to find.','Focus the page on one audience and one primary action, support claims with evidence and make contact options easy to find.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'How to Create a Simple Business Landing Page | MRK Digital','Focus the page on one audience and one primary action, support claims with evidence and make contact options easy to find.','2026-09-15 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Online Government Service Guidance in Pakistan','online-government-service-guidance-in-pakistan','Pakistan Digital Services','Explain eligibility, documents and official application channels carefully; fees and rules can change, so verify with the relevant government portal.','Explain eligibility, documents and official application channels carefully; fees and rules can change, so verify with the relevant government portal.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Online Government Service Guidance in Pakistan | MRK Digital','Explain eligibility, documents and official application channels carefully; fees and rules can change, so verify with the relevant government portal.','2026-09-16 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('NADRA and PakID Application Preparation Guide','nadra-and-pakid-application-preparation-guide','Pakistan Digital Services','Help applicants prepare information and understand official instructions; identity decisions and submissions must be completed through authorized channels.','Help applicants prepare information and understand official instructions; identity decisions and submissions must be completed through authorized channels.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'NADRA and PakID Application Preparation Guide | MRK Digital','Help applicants prepare information and understand official instructions; identity decisions and submissions must be completed through authorized channels.','2026-09-17 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('FBR NTN and Tax Filing: Preparation Checklist','fbr-ntn-and-tax-filing-preparation-checklist','Pakistan Digital Services','Organize identity, income and business records, then verify current requirements on official FBR resources or consult a qualified tax professional.','Organize identity, income and business records, then verify current requirements on official FBR resources or consult a qualified tax professional.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'FBR NTN and Tax Filing: Preparation Checklist | MRK Digital','Organize identity, income and business records, then verify current requirements on official FBR resources or consult a qualified tax professional.','2026-09-18 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Passport Application Preparation in Pakistan','passport-application-preparation-in-pakistan','Pakistan Digital Services','Check the current official passport authority requirements, appointment process, fee schedule and required original documents before applying.','Check the current official passport authority requirements, appointment process, fee schedule and required original documents before applying.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Passport Application Preparation in Pakistan | MRK Digital','Check the current official passport authority requirements, appointment process, fee schedule and required original documents before applying.','2026-09-19 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `blog_posts` (`title`,`slug`,`category`,`excerpt`,`content`,`status`,`featured`,`seo_title`,`seo_description`,`published_at`,`created_at`,`updated_at`) VALUES ('Vehicle Token Tax and Driving Licence Guidance','vehicle-token-tax-and-driving-licence-guidance','Pakistan Digital Services','Use the relevant provincial government portal to verify current fees, eligibility, documents and appointment instructions.','Use the relevant provincial government portal to verify current fees, eligibility, documents and appointment instructions.

Practical checklist:
1. Confirm your goal and requirements.
2. Verify current platform or official-source instructions.
3. Test changes before using them in production.
4. Keep records and review the result.

MRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client''s requirements.','published',0,'Vehicle Token Tax and Driving Licence Guidance | MRK Digital','Use the relevant provincial government portal to verify current fees, eligibility, documents and appointment instructions.','2026-09-20 12:00:00','2026-10-22 12:00:00','2026-10-22 12:00:00');

INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Laravel Website Development','laravel-website-development','Custom Laravel websites, dashboards and database-backed portals built around documented business requirements.','Custom Laravel websites, dashboards and database-backed portals built around documented business requirements.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Web Development','Laravel','published',0,110,'Request a Quote',NULL,'Laravel Website Development | MRK Digital','Custom Laravel websites, dashboards and database-backed portals built around documented business requirements.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('WordPress Website Development','wordpress-website-development','Mobile-friendly WordPress websites with editable pages, contact forms and a maintainable content structure.','Mobile-friendly WordPress websites with editable pages, contact forms and a maintainable content structure.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Web Development','WordPress','published',0,120,'Request a Quote',NULL,'WordPress Website Development | MRK Digital','Mobile-friendly WordPress websites with editable pages, contact forms and a maintainable content structure.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Business Website Design','business-website-design','A clear multi-page business website with service information, contact details and responsive layouts.','A clear multi-page business website with service information, contact details and responsive layouts.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Web Development','Web Design','published',0,130,'Request a Quote',NULL,'Business Website Design | MRK Digital','A clear multi-page business website with service information, contact details and responsive layouts.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Custom Web Application Development','custom-web-application-development','Browser-based workflows, dashboards, forms and reports scoped to your operational needs.','Browser-based workflows, dashboards, forms and reports scoped to your operational needs.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Web Applications','Web Apps','published',0,140,'Request a Quote',NULL,'Custom Web Application Development | MRK Digital','Browser-based workflows, dashboards, forms and reports scoped to your operational needs.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Website Bug Fixing and Maintenance','website-bug-fixing-and-maintenance','Troubleshooting, dependency review, backups and planned maintenance for existing websites.','Troubleshooting, dependency review, backups and planned maintenance for existing websites.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Technical Support','Maintenance','published',0,150,'Request a Quote',NULL,'Website Bug Fixing and Maintenance | MRK Digital','Troubleshooting, dependency review, backups and planned maintenance for existing websites.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Technical SEO Audit','technical-seo-audit','Review metadata, sitemap, robots rules, indexability, internal links and common technical SEO issues.','Review metadata, sitemap, robots rules, indexability, internal links and common technical SEO issues.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','SEO','SEO','published',0,160,'Request a Quote',NULL,'Technical SEO Audit | MRK Digital','Review metadata, sitemap, robots rules, indexability, internal links and common technical SEO issues.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('On-Page SEO Improvements','on-page-seo-improvements','Improve page titles, headings, descriptions, content structure and relevant internal links without ranking guarantees.','Improve page titles, headings, descriptions, content structure and relevant internal links without ranking guarantees.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','SEO','SEO','published',0,170,'Request a Quote',NULL,'On-Page SEO Improvements | MRK Digital','Improve page titles, headings, descriptions, content structure and relevant internal links without ranking guarantees.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Sitemap and Robots.txt Setup','sitemap-and-robots-txt-setup','Create and validate sitemap and robots rules that reflect the site''s actual public pages.','Create and validate sitemap and robots rules that reflect the site''s actual public pages.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','SEO','SEO','published',0,180,'Request a Quote',NULL,'Sitemap and Robots.txt Setup | MRK Digital','Create and validate sitemap and robots rules that reflect the site''s actual public pages.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Google Search Console Setup Guidance','google-search-console-setup-guidance','Help verify site ownership, submit a sitemap and interpret indexing reports using the site owner''s account.','Help verify site ownership, submit a sitemap and interpret indexing reports using the site owner''s account.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','SEO','SEO','published',0,190,'Request a Quote',NULL,'Google Search Console Setup Guidance | MRK Digital','Help verify site ownership, submit a sitemap and interpret indexing reports using the site owner''s account.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Website Speed Optimization','website-speed-optimization','Measure and address image weight, caching, asset delivery and other practical performance bottlenecks.','Measure and address image weight, caching, asset delivery and other practical performance bottlenecks.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Web Development','Performance','published',0,200,'Request a Quote',NULL,'Website Speed Optimization | MRK Digital','Measure and address image weight, caching, asset delivery and other practical performance bottlenecks.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('E-commerce Product Listing','e-commerce-product-listing','Organize product titles, attributes, descriptions, categories and variation data for supported stores.','Organize product titles, attributes, descriptions, categories and variation data for supported stores.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','E-commerce','E-commerce','published',0,210,'Request a Quote',NULL,'E-commerce Product Listing | MRK Digital','Organize product titles, attributes, descriptions, categories and variation data for supported stores.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Product Category Mapping','product-category-mapping','Design a manageable product taxonomy with clear category rules and fewer overlapping classifications.','Design a manageable product taxonomy with clear category rules and fewer overlapping classifications.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','E-commerce','E-commerce','published',0,220,'Request a Quote',NULL,'Product Category Mapping | MRK Digital','Design a manageable product taxonomy with clear category rules and fewer overlapping classifications.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Spreadsheet Cleanup and Formatting','spreadsheet-cleanup-and-formatting','Standardize columns, formats, duplicates and validation rules in client-provided spreadsheets.','Standardize columns, formats, duplicates and validation rules in client-provided spreadsheets.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Data Services','Data','published',0,230,'Request a Quote',NULL,'Spreadsheet Cleanup and Formatting | MRK Digital','Standardize columns, formats, duplicates and validation rules in client-provided spreadsheets.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Virtual Assistant Support','virtual-assistant-support','Support defined administrative tasks, online research, document organization and routine digital workflows.','Support defined administrative tasks, online research, document organization and routine digital workflows.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Virtual Assistance','VA','published',0,240,'Request a Quote',NULL,'Virtual Assistant Support | MRK Digital','Support defined administrative tasks, online research, document organization and routine digital workflows.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Web Research and Data Collection','web-research-and-data-collection','Collect and structure publicly available information according to agreed fields and source requirements.','Collect and structure publicly available information according to agreed fields and source requirements.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Data Services','Research','published',0,250,'Request a Quote',NULL,'Web Research and Data Collection | MRK Digital','Collect and structure publicly available information according to agreed fields and source requirements.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('PDF and Document Formatting','pdf-and-document-formatting','Format reports, forms and business documents with consistent headings, tables and page layout.','Format reports, forms and business documents with consistent headings, tables and page layout.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Office Services','Documents','published',0,260,'Request a Quote',NULL,'PDF and Document Formatting | MRK Digital','Format reports, forms and business documents with consistent headings, tables and page layout.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Business Presentation Design','business-presentation-design','Create clear slide decks from supplied content, with consistent layout and readable information hierarchy.','Create clear slide decks from supplied content, with consistent layout and readable information hierarchy.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Graphic Design','Design','published',0,270,'Request a Quote',NULL,'Business Presentation Design | MRK Digital','Create clear slide decks from supplied content, with consistent layout and readable information hierarchy.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Social Media Post Design','social-media-post-design','Prepare branded social graphics sized for the agreed social platforms and campaign purpose.','Prepare branded social graphics sized for the agreed social platforms and campaign purpose.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Graphic Design','Design','published',0,280,'Request a Quote',NULL,'Social Media Post Design | MRK Digital','Prepare branded social graphics sized for the agreed social platforms and campaign purpose.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Business Card and Flyer Design','business-card-and-flyer-design','Design print-ready business cards, flyers and promotional materials from approved business details.','Design print-ready business cards, flyers and promotional materials from approved business details.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Graphic Design','Design','published',0,290,'Request a Quote',NULL,'Business Card and Flyer Design | MRK Digital','Design print-ready business cards, flyers and promotional materials from approved business details.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Logo and Brand Starter Kit','logo-and-brand-starter-kit','Develop a simple visual identity direction with logo concepts, colors and practical usage guidance.','Develop a simple visual identity direction with logo concepts, colors and practical usage guidance.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Graphic Design','Branding','published',0,300,'Request a Quote',NULL,'Logo and Brand Starter Kit | MRK Digital','Develop a simple visual identity direction with logo concepts, colors and practical usage guidance.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Website Content Formatting','website-content-formatting','Structure supplied copy with headings, lists, links and readable page formatting.','Structure supplied copy with headings, lists, links and readable page formatting.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Content Services','Content','published',0,310,'Request a Quote',NULL,'Website Content Formatting | MRK Digital','Structure supplied copy with headings, lists, links and readable page formatting.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Blog Article Formatting and Upload','blog-article-formatting-and-upload','Prepare approved articles for a CMS with headings, excerpts, categories and metadata.','Prepare approved articles for a CMS with headings, excerpts, categories and metadata.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Content Services','Content','published',0,320,'Request a Quote',NULL,'Blog Article Formatting and Upload | MRK Digital','Prepare approved articles for a CMS with headings, excerpts, categories and metadata.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Website Migration Planning','website-migration-planning','Plan a controlled move between supported platforms or hosts, including files, database and URL checks.','Plan a controlled move between supported platforms or hosts, including files, database and URL checks.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Web Development','Migration','published',0,330,'Request a Quote',NULL,'Website Migration Planning | MRK Digital','Plan a controlled move between supported platforms or hosts, including files, database and URL checks.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Domain and DNS Configuration Guidance','domain-and-dns-configuration-guidance','Help review domain records, HTTPS setup and common DNS configuration for supported providers.','Help review domain records, HTTPS setup and common DNS configuration for supported providers.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Hosting','Hosting','published',0,340,'Request a Quote',NULL,'Domain and DNS Configuration Guidance | MRK Digital','Help review domain records, HTTPS setup and common DNS configuration for supported providers.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('PHP Application Troubleshooting','php-application-troubleshooting','Investigate reproducible PHP errors through logs, dependency checks and a safe test workflow.','Investigate reproducible PHP errors through logs, dependency checks and a safe test workflow.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Technical Support','PHP','published',0,350,'Request a Quote',NULL,'PHP Application Troubleshooting | MRK Digital','Investigate reproducible PHP errors through logs, dependency checks and a safe test workflow.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Laravel Database and Migration Support','laravel-database-and-migration-support','Review migration order, schema changes and database connectivity while preserving existing production data.','Review migration order, schema changes and database connectivity while preserving existing production data.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Laravel','Laravel','published',0,360,'Request a Quote',NULL,'Laravel Database and Migration Support | MRK Digital','Review migration order, schema changes and database connectivity while preserving existing production data.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Laravel Admin Dashboard Development','laravel-admin-dashboard-development','Build role-aware admin screens for managing approved business data and common workflows.','Build role-aware admin screens for managing approved business data and common workflows.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Laravel','Laravel','published',0,370,'Request a Quote',NULL,'Laravel Admin Dashboard Development | MRK Digital','Build role-aware admin screens for managing approved business data and common workflows.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('API Integration Planning','api-integration-planning','Connect supported APIs after verifying access, permissions, rate limits and provider documentation.','Connect supported APIs after verifying access, permissions, rate limits and provider documentation.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Integration','APIs','published',0,380,'Request a Quote',NULL,'API Integration Planning | MRK Digital','Connect supported APIs after verifying access, permissions, rate limits and provider documentation.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Contact Form Setup','contact-form-setup','Add validated contact forms with spam controls and an appropriate enquiry workflow.','Add validated contact forms with spam controls and an appropriate enquiry workflow.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Web Development','Forms','published',0,390,'Request a Quote',NULL,'Contact Form Setup | MRK Digital','Add validated contact forms with spam controls and an appropriate enquiry workflow.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Website Privacy and Security Review','website-privacy-and-security-review','Review common configuration, access control, data collection and backup practices; no guarantee of perfect security.','Review common configuration, access control, data collection and backup practices; no guarantee of perfect security.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Security','Security','published',0,400,'Request a Quote',NULL,'Website Privacy and Security Review | MRK Digital','Review common configuration, access control, data collection and backup practices; no guarantee of perfect security.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('GitHub Repository Setup','github-repository-setup','Organize source code, README instructions, ignore rules and a maintainable repository workflow.','Organize source code, README instructions, ignore rules and a maintainable repository workflow.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Development','GitHub','published',0,410,'Request a Quote',NULL,'GitHub Repository Setup | MRK Digital','Organize source code, README instructions, ignore rules and a maintainable repository workflow.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('GitHub Actions CI Setup','github-actions-ci-setup','Automate repeatable checks such as tests, dependency setup and build validation for supported projects.','Automate repeatable checks such as tests, dependency setup and build validation for supported projects.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Development','CI/CD','published',0,420,'Request a Quote',NULL,'GitHub Actions CI Setup | MRK Digital','Automate repeatable checks such as tests, dependency setup and build validation for supported projects.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Static Website Deployment Guidance','static-website-deployment-guidance','Prepare and validate a static site''s build output, publishing path, domain and HTTPS settings.','Prepare and validate a static site''s build output, publishing path, domain and HTTPS settings.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Hosting','Hosting','published',0,430,'Request a Quote',NULL,'Static Website Deployment Guidance | MRK Digital','Prepare and validate a static site''s build output, publishing path, domain and HTTPS settings.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Responsive UI Fixes','responsive-ui-fixes','Improve layout behavior on mobile, tablet and desktop using tested breakpoints and accessible controls.','Improve layout behavior on mobile, tablet and desktop using tested breakpoints and accessible controls.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Web Development','UI','published',0,440,'Request a Quote',NULL,'Responsive UI Fixes | MRK Digital','Improve layout behavior on mobile, tablet and desktop using tested breakpoints and accessible controls.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Website Accessibility Improvements','website-accessibility-improvements','Address common accessibility issues such as labels, keyboard access, semantic structure and contrast.','Address common accessibility issues such as labels, keyboard access, semantic structure and contrast.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Accessibility','Accessibility','published',0,450,'Request a Quote',NULL,'Website Accessibility Improvements | MRK Digital','Address common accessibility issues such as labels, keyboard access, semantic structure and contrast.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('QR Code Generator Integration','qr-code-generator-integration','Add QR code generation for appropriate URLs or text and verify output before use in print.','Add QR code generation for appropriate URLs or text and verify output before use in print.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Digital Tools','Tools','published',0,460,'Request a Quote',NULL,'QR Code Generator Integration | MRK Digital','Add QR code generation for appropriate URLs or text and verify output before use in print.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('CSV and JSON Conversion Tool','csv-and-json-conversion-tool','Build a browser-based converter for well-formed CSV and JSON data with clear input and output handling.','Build a browser-based converter for well-formed CSV and JSON data with clear input and output handling.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Digital Tools','Tools','published',0,470,'Request a Quote',NULL,'CSV and JSON Conversion Tool | MRK Digital','Build a browser-based converter for well-formed CSV and JSON data with clear input and output handling.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Text and Data Utility Tools','text-and-data-utility-tools','Create small browser-based utilities for text cleanup, sorting, comparison and formatting.','Create small browser-based utilities for text cleanup, sorting, comparison and formatting.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Digital Tools','Tools','published',0,480,'Request a Quote',NULL,'Text and Data Utility Tools | MRK Digital','Create small browser-based utilities for text cleanup, sorting, comparison and formatting.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('PLC Logic Documentation','plc-logic-documentation','Document supplied PLC logic, I/O mappings, operating states and troubleshooting notes.','Document supplied PLC logic, I/O mappings, operating states and troubleshooting notes.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Automation','PLC','published',0,490,'Request a Quote',NULL,'PLC Logic Documentation | MRK Digital','Document supplied PLC logic, I/O mappings, operating states and troubleshooting notes.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Arduino Prototype Development','arduino-prototype-development','Develop and test small Arduino prototypes based on agreed sensors, outputs and operating requirements.','Develop and test small Arduino prototypes based on agreed sensors, outputs and operating requirements.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Automation','Arduino','published',0,500,'Request a Quote',NULL,'Arduino Prototype Development | MRK Digital','Develop and test small Arduino prototypes based on agreed sensors, outputs and operating requirements.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Sensor and Relay Project Planning','sensor-and-relay-project-planning','Plan low-voltage prototype wiring, sensor selection and relay control logic with safety constraints documented.','Plan low-voltage prototype wiring, sensor selection and relay control logic with safety constraints documented.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Automation','Automation','published',0,510,'Request a Quote',NULL,'Sensor and Relay Project Planning | MRK Digital','Plan low-voltage prototype wiring, sensor selection and relay control logic with safety constraints documented.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Automation Troubleshooting Documentation','automation-troubleshooting-documentation','Organize symptoms, test results, wiring references and likely fault-isolation steps for automation prototypes.','Organize symptoms, test results, wiring references and likely fault-isolation steps for automation prototypes.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Automation','Automation','published',0,520,'Request a Quote',NULL,'Automation Troubleshooting Documentation | MRK Digital','Organize symptoms, test results, wiring references and likely fault-isolation steps for automation prototypes.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Official NADRA/PakID Process Guidance','official-nadra-pakid-process-guidance','Help organize applicant questions and explain where to verify current requirements; applications are submitted through authorized official channels.','Help organize applicant questions and explain where to verify current requirements; applications are submitted through authorized official channels.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Pakistan Digital Services','Guidance','published',0,530,'Request a Quote',NULL,'Official NADRA/PakID Process Guidance | MRK Digital','Help organize applicant questions and explain where to verify current requirements; applications are submitted through authorized official channels.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('FBR/NTN Process Preparation Guidance','fbr-ntn-process-preparation-guidance','Help organize documents and navigate official information; tax advice and submissions should be verified with FBR or a qualified professional.','Help organize documents and navigate official information; tax advice and submissions should be verified with FBR or a qualified professional.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Pakistan Digital Services','Guidance','published',0,540,'Request a Quote',NULL,'FBR/NTN Process Preparation Guidance | MRK Digital','Help organize documents and navigate official information; tax advice and submissions should be verified with FBR or a qualified professional.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Passport Application Preparation Guidance','passport-application-preparation-guidance','Provide a checklist based on current official instructions; final applications and identity verification remain with authorized authorities.','Provide a checklist based on current official instructions; final applications and identity verification remain with authorized authorities.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Pakistan Digital Services','Guidance','published',0,550,'Request a Quote',NULL,'Passport Application Preparation Guidance | MRK Digital','Provide a checklist based on current official instructions; final applications and identity verification remain with authorized authorities.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Vehicle Token Tax Process Guidance','vehicle-token-tax-process-guidance','Help users locate relevant provincial portals and prepare information while verifying current official fees and rules.','Help users locate relevant provincial portals and prepare information while verifying current official fees and rules.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Pakistan Digital Services','Guidance','published',0,560,'Request a Quote',NULL,'Vehicle Token Tax Process Guidance | MRK Digital','Help users locate relevant provincial portals and prepare information while verifying current official fees and rules.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Driving Licence Process Guidance','driving-licence-process-guidance','Explain how to find current provincial licence requirements and official appointment or application channels.','Explain how to find current provincial licence requirements and official appointment or application channels.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Pakistan Digital Services','Guidance','published',0,570,'Request a Quote',NULL,'Driving Licence Process Guidance | MRK Digital','Explain how to find current provincial licence requirements and official appointment or application channels.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Online Form Filling Assistance','online-form-filling-assistance','Assist with user-provided information on permitted forms; the applicant must review and authorize all submissions.','Assist with user-provided information on permitted forms; the applicant must review and authorize all submissions.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Digital Services','Forms','published',0,580,'Request a Quote',NULL,'Online Form Filling Assistance | MRK Digital','Assist with user-provided information on permitted forms; the applicant must review and authorize all submissions.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('CV and Resume Formatting','cv-and-resume-formatting','Format a clear, role-focused CV using the client''s accurate education, skills and experience.','Format a clear, role-focused CV using the client''s accurate education, skills and experience.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Career Services','Career','published',0,590,'Request a Quote',NULL,'CV and Resume Formatting | MRK Digital','Format a clear, role-focused CV using the client''s accurate education, skills and experience.','2026-10-22 12:00:00','2026-10-22 12:00:00');
INSERT IGNORE INTO `services` (`title`,`slug`,`summary`,`description`,`category`,`icon`,`status`,`featured`,`sort_order`,`cta_label`,`cta_url`,`seo_title`,`seo_description`,`created_at`,`updated_at`) VALUES ('Freelance Profile and Gig Setup Guidance','freelance-profile-and-gig-setup-guidance','Improve service descriptions, deliverables and profile clarity for freelance platforms without promising orders or earnings.','Improve service descriptions, deliverables and profile clarity for freelance platforms without promising orders or earnings.

Process: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority''s current process.','Freelancing','Freelancing','published',0,600,'Request a Quote',NULL,'Freelance Profile and Gig Setup Guidance | MRK Digital','Improve service descriptions, deliverables and profile clarity for freelance platforms without promising orders or earnings.','2026-10-22 12:00:00','2026-10-22 12:00:00');

-- Check affected counts before committing. If phpMyAdmin reports an SQL error, do not blindly rerun; inspect the error first.
COMMIT;
