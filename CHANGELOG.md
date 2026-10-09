# v0.9.46
## 10/09/2026

1. [](#improved)
    * No "Setup Git Sync" link is shown before Git Sync is connected to a repository, so visitors don't see an unfinished setup step; set it up in the Admin Panel (Plugins → Git Sync)
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
