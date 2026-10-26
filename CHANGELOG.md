# v1.0.4
## XX/XX/2026

1. [](#new)
    * Hide sidebar: on wider screens, a "Hide sidebar" button at the top right of a guide's sidebar hides it (the side list, its search box and Side Box), leaving a small button in the same place to show it again, so readers can focus on the page; the page is centred at the same comfortable width. The choice is remembered in the reader's browser, and phones are unchanged. It can be turned off with the new Hide Sidebar Button theme option

# v1.0.3
## 10/26/2026

1. [](#new)
    * Side Box: Markdown shown under a guide's side list, such as a call to action or a download link – set once in the guide's Section List page settings for every page, replaced on any page, or hidden with `false` on a page (or a section and its pages). The same `side_box` setting as in Grav Helios Open Reader, so it moves with a guide. When the side list and the Side Box fit in the window together, the side list stays in view while reading; otherwise both scroll with the page
1. [](#improved)
    * Section, Section Page and Subsection page settings: Grav's Taxonomies (not used on guide pages) moves to the end of the Options tab, so the guide settings come first

# v1.0.2
## 10/23/2026

1. [](#improved)
    * Dark mode: code blocks switch to the One Dark colours that match the Prism Highlight plugin's default One Light theme, instead of staying light

# v1.0.1
## 10/23/2026

1. [](#improved)
    * Demo content: a Mermaid flowchart on the Getting Started with OER page and a LaTeX Correcting a Formula example on the Maintaining an Open Reader page, matching the skeleton (drawn when the Mermaid and LaTeX Math plugins are installed)
1. [](#bugfix)
    * An iFrame page shown as a link with Page Inject (template=partials/iframelinkonly) now links to its iFrame Source address, instead of an empty link; a site address starting with / gets the site's base URL, as in Open Matter
    * The PDF and SpeakerDeck link-only templates no longer start their link address with a space

# v1.0.0
## 10/20/2026

1. [](#new)
    * Version 1.0: the first stable release, after the 0.9 series. Updating from any 0.9 version works like any other update, with pages, settings and child themes kept as they are
1. [](#improved)
    * README: updating from an earlier version, and what looks different on guide pages after updating from before 0.9.63

# v0.9.66
## 10/20/2026

1. [](#improved)
    * The back link, Sections link and top Previous/Next links are in the link colour rather than grey, so they look tappable, as in Quark Open Publishing and Grav Open Course Hub

# v0.9.65
## 10/18/2026

1. [](#improved)
    * French and German translations are complete, for every text the theme shows readers (search, guide pages, Previous/Next, Keep My Place, error pages and more), using the same words as Grav Helios Open Reader where they overlap
    * README: which languages are complete
    * Demo content: the Open Education Essentials guide's section pages' headings no longer repeat the names of their own pages, so the side list doesn't show a page's heading and a page with the same name (for example LMS Embedding) one under the other

# v0.9.64
## 10/18/2026

1. [](#new)
    * On guide pages, tap or click an image shown smaller than its full size to see it larger, filling the screen (handy for diagrams and screenshots on phones); Escape, the Close button or a tap closes it
1. [](#improved)
    * Demo content: a Creative Commons license chart, with its attribution as a caption, on the Creative Commons Licenses page (it can be tapped to see it larger)
    * README: seeing an image larger

# v0.9.63
## 10/18/2026

1. [](#new)
    * On guide pages, the / key jumps to the guide's search box, ready to type (as on GitHub and many documentation sites)
    * With the Page TOC plugin, the side list marks the section you're reading as you scroll, as in Grav Helios Open Reader (screen readers hear it as the current location)
    * Keep My Place also remembers the section you were reading, so Continue reading goes back to it rather than the top of the page (when its headings have ids, as with the Page TOC plugin)
    * On phones, Previous/Next sit in a compact bar fixed to the bottom of the screen, in place of the links at the top and the tiles at the end of the page, as in Grav Helios Open Reader (the guide's Show Sticky Prev/Next Bar setting, the same as in Open Reader, on by default). It steps aside while typing in a box such as the search box, keeps links reached with the Tab key above it, and isn't used on phones turned sideways
1. [](#improved)
    * The search box and side list of sections share one card on section pages, so they read as one place to find your way around the guide
    * The side list is a little wider on laptop screens (at least 17rem), so longer page titles wrap less
    * Previous/Next tiles are a little more compact on phones (with the Previous/Next bar turned off), so more titles fit on one line
    * On phones, the search box has 16px text, as iPhones zoom the whole page in when a box with smaller text is tapped
    * README: the / key for search, and the section you're reading marked in the side list
    * README: the Previous/Next bar on phones

# v0.9.62
## 10/18/2026

1. [](#bugfix)
    * Search finds the pages of the site's home page even when it's set to visible: false to keep it out of the menu (for example a guide that is the home page); other top-level pages set to visible: false are still left out
    * The search box on a guide's home page and in its side list is rounded on all four corners, not just the left side
    * Search boxes have a clearer border (at least 3:1 contrast), and the guide's search box says "Search these pages…" instead of "Search this content..." (screen readers hear the same words without the "…"), in translatable texts

# v0.9.61
## 10/17/2026

1. [](#bugfix)
    * On phones, a page whose text starts with a heading no longer has a large gap below the "On this page" box

# v0.9.60
## 10/17/2026

1. [](#bugfix)
    * "On this page" sections in the side list use the same colour as the page names, rather than a lighter shade, so their contrast matches in Light and Dark modes

# v0.9.59
## 10/17/2026

1. [](#new)
    * Hide Header While Scrolling (phones), on by default: on guide pages on phones and narrow windows, with Quark 2's Header Fixed on, the header and menu button slide away while scrolling down and come back when scrolling up, giving the text the whole screen (Themes → Quark 2 Open Publishing → Quark 2 Options → Header)
1. [](#improved)
    * Guides show Previous/Next at the top and bottom of their pages, and the reading progress, unless their home page sets otherwise, as in Grav Helios Open Reader (the Prev/Next Page Controls Position and Show Reading Progress settings); to keep the earlier look, set Prev/Next Page Controls Position to Bottom and turn off Show Reading Progress. Multi-Section pages are unchanged
    * README: Hide Header While Scrolling

# v0.9.58
## 10/17/2026

1. [](#new)
    * Hide from Menu setting on guides (`hide_from_menu: true`, which works on any page): the page is left out of the site menu but stays visible, so a guide keeps its card on a Guides List page (`visible: false` still leaves a guide off the list, as in Grav Helios Open Reader)

# v0.9.57
## 10/17/2026

1. [](#new)
    * [hideifembedded]...[/hideifembedded] and [showifembedded]...[/showifembedded] shortcodes: content shown only on the site, or only when a page is embedded (for example in an LMS), the same as in Grav Open Course Hub and Grav Helios Course Hub
    * "On this page": with the Page TOC plugin, a guide page's sections (its headings) appear under it in the side list, and in an "On this page" box above the text on phones, as on Grav Open Course Hub's module pages; without the plugin, nothing changes
    * [badge] shortcode: a small label in the text, such as "Draft" or "Due Friday", in the same colours as section card badges, with the same options as Grav Open Course Hub and Grav Helios Course Hub
    * A page's Badge Label (such as "Draft") now also shows beside its section label on the page, and section pages have Badge Label and Badge Color settings too
    * Guides List page type (`reader-list.md`, the same as Grav Helios Open Reader's readers list): a card for each guide on the site, with its cover image, subtitle, author(s), edition and last updated date, optional group headings and badges (new Guides List Group, Badge Label and Badge Color settings on Section List pages), and cards per row, card image layout and description line limit settings
1. [](#improved)
    * The "This page as Markdown" link is hidden on Guides List pages by default, as on Section List pages
    * README: the Show/Hide If Embedded shortcodes, the Page TOC plugin for a page's sections, badges, and the Guides List page
1. [](#bugfix)
    * Search leaves out the pages inside an unpublished page, such as the sections of an example guide that is turned off (Grav still counts them as published)

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
