# v0.9.56
## 10/16/2026

1. [](#bugfix)
    * Dark mode: links and filled buttons (such as Start Reading and Continue Reading) have enough contrast with any Accent Color, including links on the lighter card behind standard pages

# v0.9.55
## 10/16/2026

1. [](#new)
    * Captions for images in guide pages: an italic line right after an image on its own line (such as `*Photo by …*`) shows as a caption – small, centred and just below the image – so photo credits and captions need no HTML
    * Image captions written in HTML (`<figure>` and `<figcaption>`, as from the Pressbooks Converter) are shown the same way, as in Grav Helios Open Reader
    * Cover Image Alt Text setting on a guide's home page, to describe the cover for screen readers (without it, the alt text is still "<title> cover")
1. [](#improved)
    * On a guide's first page, the left arrow key goes back to the guide's home page, so presenting with the arrow keys works both ways
    * README: the left arrow key on a guide's first page
1. [](#bugfix)
    * The back, Sections and top Previous/Next links on guide pages line up on one row (Previous/Next sat slightly higher, most noticeably on phones)

# v0.9.54
## 10/16/2026

1. [](#new)
    * On guide pages, the left and right arrow keys go to the previous and next page, and on a guide's home page the right arrow key follows Start Reading – handy when presenting by screen sharing (not while typing or scrolling a wide table)
    * [topics]...[/topics] shortcode: an A–Z topics index made from ## A, ## B, ... headings, the same as in Bootstrap4 Open Matter (Open Course Hub) and Grav Helios Course Hub, with large, keyboard-friendly letter links
1. [](#improved)
    * Images in a guide page's text are no taller than a little over half the screen, so an image and its text fit on screen together; an image on its own line is centred
    * README: the arrow keys and the Topics shortcode

# v0.9.53
## 10/14/2026

1. [](#improved)
    * Demo pages: the blog, Standard Page and unpublished examples are numbered 15–19, so a converted Pressbooks book (numbered 10.) appears right after the sample guide, and the hidden Search page has no number, as in Grav Open Publishing Space 2.5.2

# v0.9.52
## 10/14/2026

1. [](#bugfix)
    * Embedded and chromeless pages stay embedded when a page redirects, such as a section without text of its own opening its first page

# v0.9.51
## 10/14/2026

1. [](#bugfix)
    * Embedded blog posts show their title as plain text, not as a link, and leave out their tags

# v0.9.50
## 10/14/2026

1. [](#bugfix)
    * The blog list page leaves out its breadcrumbs when embedded, as blog posts already did

# v0.9.49
## 10/14/2026

1. [](#new)
    * Optional full text link on each guide's home page: the whole guide as one Markdown file (llms-full.txt), as in Grav Helios Open Reader
1. [](#improved)
    * README: every callout shortcode is listed, and the full text link is described
1. [](#bugfix)
    * Printed guide pages are one full-width column, not two narrow ones
    * Printed blog posts start on the first page, with their title, subtitle and date
    * The blog's title prints at the top of the blog list
    * The Copy as Markdown button is left out of print

# v0.9.48
## 10/13/2026

1. [](#bugfix)
    * In chromeless mode (/chromeless:true), links to other sites and email links work
    * In chromeless mode, links with # or ? and links to files such as PDFs open the right address

# v0.9.47
## 10/13/2026

1. [](#bugfix)
    * Yes/No switches in the theme and page settings show their labels in Admin on hosts with PHP's native YAML extension

# v0.9.46
## 10/09/2026

1. [](#improved)
    * No "Setup Git Sync" link is shown before Git Sync is connected to a repository, so visitors don't see an unfinished setup step; set it up in the Admin Panel (its own menu item in Grav 2, or Plugins → Git Sync in Grav 1.7)
    * Demo content: the home page introduces Grav Open Publishing Space and its theme, and the Read Me page describes the "This page as Markdown" link
    * Demo content: the Custom Page Types example is removed, and Read Me is now 20.readme, leaving 08–19 free for new pages (a converted Pressbooks book uses 10.)
1. [](#bugfix)
    * The "This page as Markdown" and Git Sync link notes below the content use the same small, muted text as the attribution

# v0.9.45
## 10/09/2026

1. [](#new)
    * Reuse Pages as Markdown: an optional "This page as Markdown (.md)" link, using Grav 2's Markdown output
1. [](#improved)
    * The Git Sync link (Page location) and Creative Commons license now sit inside the content, centred, on standard, blog and guide pages
    * Open Publishing Options reordered: Creative Commons License first, H5P Setup last
    * Quieter page links (muted, underlined on hover), and the guide's top links and page links are left out of each page's Markdown

# v0.9.44
## 10/08/2026

1. [](#improved)
    * Deprecated the [twitter] shortcode, as X has heavily restricted embedded timelines; it will be removed in a future release

# v0.9.43
## 10/07/2026

1. [](#new)
    * Heading Weight option: lighter headings across the site, using Cal Sans as designed (the default), or Quark 2's bolder default
1. [](#improved)
    * An arrow on every section card, as in Helios Open Reader, to show they open parts of the guide
    * With 1 section card per row, card images are shown beside the text by default, as in Helios Open Reader
    * A Start Reading link after the section cards, as in Helios Open Reader
    * The search box on a Section List page starts on the left when there's no Continue reading bar
    * The Section List page's title is as large as its section pages' titles

# v0.9.42
## 10/06/2026

1. [](#bugfix)
    * The Setup Git Sync link is no longer shown when the Git Sync plugin is turned off

# v0.9.41
## 10/06/2026

1. [](#new)
    * Added demo link

# v0.9.4
## 10/06/2026

1. [](#improved)
    * Portrait cover images, such as book covers, are shown whole with either Cover Image Layout
    * Section cards with only a title have balanced spacing
1. [](#bugfix)
    * Exercise boxes keep their content, with only a link on its own shown as the activity button
    * Previous/Next and the reading progress skip sections that only redirect to their first page

# v0.9.3
## 10/06/2026

1. [](#new)
    * Section Page sub-pages, as in Helios Open Reader, so its publications can be copied in unchanged
    * Badge Label and Badge Color settings for section cards, as in Helios Open Reader
    * Section Author(s) setting, shown on section cards, as in Helios Open Reader
    * Copied Helios Open Reader publications keep their card layout, badges and parts (from part-1-section-1 folder names)
1. [](#improved)
    * Section Label (Plural) moved to the page's front matter (see the README), to keep the Section List settings simple
    * The Embedly script now loads only on pages with an Embedly card
1. [](#bugfix)
    * The Git Sync Link options display correctly in Admin 2

# v0.9.2
## 10/05/2026

1. [](#new)
    * Section Label (Plural) setting, for the Sections link on phones
1. [](#improved)
    * Larger links above section pages, with spacing to match
    * Part headings stand out more from the section cards, and the OER attribution's links are muted
    * Section pages are easier to read: a comfortable line length, slightly larger text without hyphenation, a Sections link on phones, and the page title as the largest heading
    * Wide tables scroll sideways on section pages, footnotes are smaller, and quotations use the reading font
    * The list of sections stays in view beside section pages on wider screens
1. [](#bugfix)
    * Section card images now open their section, like the blog's cards

# v0.9.1
## 10/05/2026

1. [](#new)
    * Continue reading bar for returning readers on Section List pages, as in Helios Open Reader, replacing Continue Reading on the Start button
1. [](#improved)
    * Start button now sits below the guide's title and details, with a larger small cover image
    * Search box now shares a row with the Continue reading bar
1. [](#bugfix)
    * Skip to content link no longer shows in some mobile browsers

# v0.9.0
## 10/04/2026

1. [](#new)
    * Changelog started...
