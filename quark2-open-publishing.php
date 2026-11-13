<?php

// Developed with the assistance of Claude Code (claude.ai)
// Quark 2 version of Quark Open Publishing: the same pages, settings and search, on the Quark 2 theme

namespace Grav\Theme;

use Grav\Common\Grav;
use Grav\Common\Theme;
use RocketTheme\Toolbox\Event\Event;

class Quark2OpenPublishing extends Quark2
{
    // guide pages
    const GUIDE_TEMPLATES = ['section-list', 'guide', 'section', 'section-page', 'sections', 'subsection'];

    // a guide's home page and the Guides page: the names used so far, and the new ones (guide.md and guide-list.md)
    const GUIDE_HOME_TEMPLATES = ['section-list', 'guide'];
    const GUIDE_LIST_TEMPLATES = ['reader-list', 'guide-list'];

    // The search route set in the SimpleSearch plugin, for example '/search'
    protected $searchRoute = '/search';

    public static function getSubscribedEvents()
    {
        return [
            'onThemeInitialized' => ['onThemeInitialized', 0],
            'onTwigLoader' => ['onTwigLoader', 0],
            // after plugins (priority 0), so a shortcode name a plugin already uses is left to that plugin
            'onShortcodeHandlers' => ['onShortcodeHandlers', -10],
            'onTwigSiteVariables' => ['onTwigSiteVariables', 0],
            'onTwigInitialized' => ['onTwigInitialized', 0],
            'onPagesInitialized' => [
                ['onSearchPagesInitialized', 10],
                ['onGuideFullTextRoute', 0],
                // first, before anything opens the page (Grav makes a page's redirect when it opens it)
                ['onEmbeddedRedirect', 1000]
            ],
            'onSimpleSearchCollection' => ['onSimpleSearchCollection', 0],
            'onPageInitialized' => [
                ['skipCacheForNumberedPages', 0],
                ['onCourseOwnPage', 10]
            ],
            'onPageContentRaw' => ['onPageContentRaw', 0],
            'onPageContentProcessed' => ['onPageContentProcessed', -100]
        ];
    }

    /**
     * Guide pages' content, shared with Helios Open Reader (classes/GuideContent.php): a Glossary page's list, Markdown
     * Extra and accessible footnotes, figure and table numbers, and skipping the content cache - hibbittsdesign.org
     */
    public function onPageContentRaw(Event $event)
    {
        // front end only: in Admin, a changed header could be saved into the page
        if (!$this->isAdmin()) {
            $this->guideContent()::beforeConversion($event['page'], self::GUIDE_TEMPLATES);
        }
    }

    public function onPageContentProcessed(Event $event)
    {
        // front end only: in Admin, a changed header could be saved into the page
        if (!$this->isAdmin()) {
            $this->guideContent()::afterConversion($event['page'], self::GUIDE_TEMPLATES, 'THEME_QUARK_OPEN_PUBLISHING');
        }
    }

    public function skipCacheForNumberedPages(Event $event)
    {
        // front end only: in Admin, a changed header could be saved into the page
        if (!$this->isAdmin()) {
            $this->guideContent()::skipCacheIfNumbered($event['page'], self::GUIDE_TEMPLATES);
        }
    }

    // the shared GuideContent class (loaded once, even with two copies installed)
    protected function guideContent(): string
    {
        if (!class_exists('HibbittsDesign\GuideContent')) {
            require_once __DIR__ . '/classes/GuideContent.php';
        }

        return \HibbittsDesign\GuideContent::class;
    }

    public function onThemeInitialized()
    {
        parent::onThemeInitialized();

        // Style GitHub-style alerts (> [!NOTE] etc., as in Helios) with the theme's own callout styles (css/callouts.css),
        // unless the GitHub Markdown Alerts plugin's wrapper class has been customised
        if ($this->config->get('plugins.github-markdown-alerts.wrapper_class', 'md-alert md-alert--') === 'md-alert md-alert--') {
            $this->config->set('plugins.github-markdown-alerts.include_css', false);
        }

        // A page's .md address (Grav 2) keeps its callouts as > [!TIP] and so on (see classes/CalloutsMarkdownOutput.php)
        if (class_exists('Grav\Common\Page\Markdown\MarkdownOutput')) {
            if (!class_exists('HibbittsDesign\CalloutsMarkdownOutput')) {
                require_once __DIR__ . '/classes/CalloutsMarkdownOutput.php';
            }
            \HibbittsDesign\CalloutsMarkdownOutput::useForMarkdownVersions($this->grav);
        }
    }

    // Quark 2 adds the images folder; this also makes Quark 2's own templates available as @quark2/...,
    // so this theme's templates can extend them (for example @quark2/partials/base.html.twig)
    public function onTwigLoader()
    {
        parent::onTwigLoader();

        $quark2_templates = $this->grav['locator']->findResource('themes://quark2/templates');
        if ($quark2_templates) {
            $this->grav['twig']->addPath($quark2_templates, 'quark2');
        }
    }

    public function onTwigInitialized()
    {
        // Quark 2's form classes and Twig helpers first
        parent::onTwigInitialized();

        // Make open_publishing_search_groups() available to the search results template
        $this->grav['twig']->twig()->addFunction(
            new \Twig\TwigFunction('open_publishing_search_groups', [$this, 'searchGroups'])
        );

        // course_home_page(course) gives a course's home page, for its card on the Courses page
        $this->grav['twig']->twig()->addFunction(
            new \Twig\TwigFunction('course_home_page', [$this, 'courseHomePage'])
        );
    }

    public function onShortcodeHandlers()
    {
        $this->grav['shortcode']->registerAllShortcodes('theme://shortcodes');
    }

    public function onTwigSiteVariables()
    {
        $uri = $this->grav['uri'];
        $twig = $this->grav['twig'];

        // Embedded mode via Grav params (/chromeless:true, /embedded:true, /standalone:true)
        // or query strings (?embedded=true, ?chromeless=true, ?standalone=true – as in Helios)
        $embeddedMode = false;
        foreach (['chromeless', 'embedded', 'standalone'] as $name) {
            if ($this->isTrueParam($uri->param($name)) || $this->isTrueParam($uri->query($name))) {
                $embeddedMode = true;
            }
        }
        $twig->twig_vars['embedded_mode'] = $embeddedMode;

        // Hide the Git Sync link via ?edit_link=false or ?hidegitlink=true (as in Helios), or /edit_link:false or /hidegitlink:true
        $hideGitLink = false;
        if ($uri->query('edit_link') === 'false' || $uri->param('edit_link') === 'false') {
            $hideGitLink = true;
        }
        if ($this->isTrueParam($uri->query('hidegitlink')) || $this->isTrueParam($uri->param('hidegitlink'))) {
            $hideGitLink = true;
        }
        $twig->twig_vars['hide_git_link'] = $hideGitLink;

        // The SimpleSearch route, for the search box
        $twig->twig_vars['search_route'] = $this->searchRoute;

        // The Multi-Page Content (Section List) page that search is limited to, if any (see getSearchScopePage)
        $twig->twig_vars['search_scope_page'] = $this->getSearchScopePage();

        // The guide the current page belongs to (its home page, guide.md or section-list.md, or null), and how many guides the site has –
        // for the guide's name and colour in the header (see templates/partials/logo.html.twig and base.html.twig)
        $twig->twig_vars['guide_home'] = $this->guideHomeOf($this->grav['page']);
        $guideCount = 0;
        foreach (self::GUIDE_HOME_TEMPLATES as $type) {
            $guideCount += count($this->grav['pages']->all()->ofType($type)->published());
        }
        $twig->twig_vars['guide_count'] = $guideCount;

        // The course the current page is in: its course_page (the course's subsite.md or course.md page, or null), plus
        // the address of its home page and its name – for the course navbar, colour, sidebar and footer
        $coursePage = $this->coursePageOf($this->grav['page']);
        $twig->twig_vars['course_page'] = $coursePage;
        $twig->twig_vars['course_home_url'] = $coursePage ? $this->courseHomeUrl($coursePage) : null;
        $twig->twig_vars['course_title'] = $coursePage ? $this->courseTitle($coursePage) : null;

        // A course's home page is called by the course's name, as a guide's home page is by the guide's: in the browser
        // tab and its heading (for screen readers, or on screen), instead of "Home". Only for this request, not for a
        // Modules page used as the home page (whose "Modules" heading is part of the page), and not for the page's
        // Markdown version (.md), which keeps the page's own title, as in its file
        $currentPage = $this->grav['page'];
        $isMarkdownVersion = $uri->extension() === 'md';
        if ($coursePage && !$isMarkdownVersion && $currentPage->url() === $twig->twig_vars['course_home_url'] && $currentPage->template() !== 'module') {
            // keep its menu label, which otherwise follows the title
            $currentPage->menu($currentPage->menu());
            $currentPage->title($twig->twig_vars['course_title']);
        }
        // does the course have its own "sidebar" page with text? (its pages then show it, as in Open Matter)
        $courseHasSidebar = false;
        if ($coursePage) {
            $courseSidebar = $coursePage->find($coursePage->route() . '/sidebar');
            $courseHasSidebar = $courseSidebar && trim($courseSidebar->rawMarkdown()) !== '';
        }
        $twig->twig_vars['course_has_sidebar'] = $courseHasSidebar;
        $listedCourses = $this->listedCourses();

        // Only one course (and no guides), or only one guide (and no courses): the site is that course or guide, so the
        // logo goes straight to its home page, as in Grav Helios Course Hub (the Courses or Guides page, if there is one,
        // stays at its own address) – unless the Single Guide or Course Logo Link setting keeps it on the site's home page
        $logoLinkTarget = $this->config->get('theme.logo_link_target', 'single_course');
        if ($logoLinkTarget === 'single_course') {
            if (count($listedCourses) === 1 && $twig->twig_vars['guide_count'] === 0) {
                $twig->twig_vars['home_url'] = $this->courseHomeUrl($listedCourses[0]);
            } elseif ($twig->twig_vars['guide_count'] === 1 && count($listedCourses) === 0) {
                foreach (self::GUIDE_HOME_TEMPLATES as $type) {
                    foreach ($this->grav['pages']->all()->ofType($type)->published() as $guideHome) {
                        $twig->twig_vars['home_url'] = $guideHome->url();
                    }
                }
            }
        }

        if ($this->isAdmin() && ($this->grav['config']->get('plugins.shortcode-core.enabled'))) {
            $this->grav['assets']->add('theme://editor-buttons/admin/js/shortcode-pdf.js');
            $this->grav['assets']->add('theme://editor-buttons/admin/js/shortcode-h5p.js');
            $this->grav['assets']->add('theme://editor-buttons/admin/js/shortcode-term.js');
        }
    }

    /**
     * Keep the embedded view through a page's redirect: a section with no text of its own (as the Pressbooks
     * Converter makes) redirects to its first page, and Grav's own redirect would leave out ?embedded=true or
     * /chromeless:true, showing the whole site again. This runs before Grav makes that redirect.
     */
    public function onEmbeddedRedirect()
    {
        if ($this->isAdmin()) {
            return;
        }
        $uri = $this->grav['uri'];

        // Step 1: the embedded parts of this address, to carry across
        $params = '';
        foreach (['chromeless', 'embedded', 'standalone', 'hidepagetitle'] as $name) {
            if ($this->isTrueParam($uri->param($name))) {
                $params .= '/' . $name . ':' . $uri->param($name);
            }
        }
        $query = '';
        foreach (['embedded', 'chromeless', 'standalone'] as $name) {
            if ($query === '' && $this->isTrueParam($uri->query($name))) {
                $query = '?' . $name . '=' . rawurlencode($uri->query($name));
            }
        }
        // not embedded: Grav redirects as usual
        if ($params === '' && $query === '') {
            return;
        }

        // Step 2: the page being opened, and where it redirects to (if anywhere)
        $page = $this->grav['pages']->find($uri->route());
        if (!$page || !$page->redirect()) {
            return;
        }
        $target = $page->redirect();

        // a redirect to another site is left to Grav (embedded mode only applies to this site)
        if (preg_match('#^https?://#', $target)) {
            return;
        }

        // a redirect can end with its own status code, for example "/book/section-1/intro[301]"
        $code = (int) $this->config->get('system.pages.redirect_default_code', 302);
        if (preg_match('/^(.*)\[(\d{3})\]$/', $target, $match)) {
            $target = $match[1];
            $code = (int) $match[2];
        }

        // Step 3: the same redirect, with the embedded parts added to the page's address
        // (before any # or ? already in the target)
        $hash = '';
        $hashPosition = strpos($target, '#');
        if ($hashPosition !== false) {
            $hash = substr($target, $hashPosition);
            $target = substr($target, 0, $hashPosition);
        }
        $existingQuery = '';
        $queryPosition = strpos($target, '?');
        if ($queryPosition !== false) {
            $existingQuery = substr($target, $queryPosition + 1);
            $target = substr($target, 0, $queryPosition);
        }
        if ($existingQuery !== '') {
            if ($query === '') {
                $query = '?' . $existingQuery;
            } else {
                $query = $query . '&' . $existingQuery;
            }
        }
        // Grav's own redirect would otherwise still happen later in this request, whenever something asks
        // for the current page (such as the Admin plugin's page popularity count), and replace this one;
        // setting the current page here means Grav doesn't open it, or redirect, a second time
        unset($this->grav['page']);
        $this->grav['page'] = $page;

        $this->grav->redirectLangSafe(rtrim($target, '/') . $params . $query . $hash, $code);
    }

    /**
     * Is a URL parameter turned on? (anything except empty, false or 0)
     */
    private function isTrueParam($value)
    {
        return $value !== null && $value !== false && $value !== '' && $value !== 'false' && $value !== '0';
    }

    /*
     * ------------------------------------------------------------------------------------------------
     * Full text of a guide: /<guide>/llms-full.txt gives the whole guide as one Markdown file, as in
     * Grav Helios Open Reader. Only the guide's own pages are included, not the blog or other pages.
     * Needs Grav 2.1 or later, which can output any page as Markdown - hibbittsdesign.org
     * ------------------------------------------------------------------------------------------------
     */

    /**
     * Answer /<guide>/llms-full.txt when the Display Full Text Link on Guides setting is on
     */
    public function onGuideFullTextRoute()
    {
        if ($this->isAdmin() || !$this->config->get('theme.full_text_link.enabled')) {
            return;
        }

        // Grav leaves the .txt off the route, so /open-education-essentials/llms-full.txt is .../llms-full
        $route = $this->grav['uri']->route();
        if (!str_ends_with($route, '/llms-full')) {
            return;
        }

        // the guide's own address; the site root is left alone (it isn't a guide)
        $guideRoute = substr($route, 0, -strlen('/llms-full'));
        if ($guideRoute === '') {
            return;
        }

        // only a guide's home page (guide.md, or section-list.md)
        $guide = $this->grav['pages']->find($guideRoute);
        if (!$guide || !in_array($guide->template(), self::GUIDE_HOME_TEMPLATES, true)) {
            return;
        }

        $this->outputGuideFullText($guide);
    }

    /**
     * Send the guide's pages, in reading order, as one plain text Markdown file
     */
    private function outputGuideFullText($guide)
    {
        // Grav's Markdown output (Grav 2.1 and later); without it, Grav shows its usual "page not found"
        if (!isset($this->grav['markdown_output'])) {
            return;
        }

        // kept in Grav's cache, and rebuilt whenever a page or the site settings change
        $cache = $this->grav['cache'];
        $cacheId = md5('q2op-full-text-' . $this->grav['pages']->getPagesCacheId() . $guide->route() . $this->grav['config']->checksum());
        $content = $cache->fetch($cacheId);

        if (!is_string($content)) {
            $pages = [];
            $this->collectGuidePages($guide, $pages);

            $output = $this->grav['markdown_output'];
            $documents = [];
            foreach ($pages as $page) {
                $documents[] = rtrim($output->render($page));
            }

            $content = implode("\n\n", $documents) . "\n";
            $cache->save($cacheId, $content);
        }

        header('Content-Type: text/plain; charset=utf-8');
        echo $content;
        exit();
    }

    /**
     * Add a page and then its visible child pages, in order, so the file follows the guide's reading order
     * (pages that are hidden, can't be visited, or only redirect elsewhere are left out)
     */
    private function collectGuidePages($page, array &$pages)
    {
        $redirect = false;
        if (isset($page->header()->redirect)) {
            $redirect = $page->header()->redirect;
        }
        if ($page->visible() && $page->routable() && !$redirect) {
            $pages[] = $page;
        }
        foreach ($page->children()->visible() as $child) {
            $this->collectGuidePages($child, $pages);
        }
    }

    /*
     * ------------------------------------------------------------------------------------------------
     * Search (SimpleSearch plugin), with results grouped by section, best matches first and the
     * search words highlighted, as in Grav Helios Open Reader - hibbittsdesign.org
     * ------------------------------------------------------------------------------------------------
     */

    /**
     * Remember the search route set in the SimpleSearch plugin (a page-based '@self' route is left to the plugin)
     */
    public function onSearchPagesInitialized()
    {
        if ($this->isAdmin() || !$this->config->get('plugins.simplesearch.enabled')) {
            return;
        }

        $route = (string) $this->config->get('plugins.simplesearch.route', '/search');
        if ($route !== '' && $route !== '/' && $route[0] === '/') {
            $this->searchRoute = $route;
        }
    }

    /**
     * Before SimpleSearch looks for the search words: leave out pages that are not worth finding
     */
    public function onSimpleSearchCollection(Event $event)
    {
        $collection = $event['collection'];

        // the footer and sidebar are shared parts of every page, and the Search page only describes searching
        $skipRoutes = ['/footer', '/sidebar', $this->searchRoute];

        // When searching from a Multi-Page Content page, only its own pages are searched
        $scopePage = $this->getSearchScopePage();

        // First find the pages to leave out, then remove them (removing pages while looping over them can skip some)
        $pagesToRemove = [];
        foreach ($collection as $page) {
            if (in_array($page->route(), $skipRoutes, true)) {
                $pagesToRemove[] = $page;
            } elseif (in_array($page->slug(), ['footer', 'sidebar', 'headerimage'], true) && $page->parent()
                && in_array($page->parent()->template(), ['subsite', 'course'], true)) {
                // a course's own footer, sidebar and header image pages are shared parts of its pages too
                $pagesToRemove[] = $page;
            } elseif ($this->isInHiddenTopLevelPage($page)) {
                $pagesToRemove[] = $page;
            } elseif ($this->isInsideUnpublishedPage($page)) {
                $pagesToRemove[] = $page;
            } elseif ($scopePage && in_array($scopePage->template(), self::GUIDE_LIST_TEMPLATES, true) && !$this->isInListedGuide($page)) {
                // from a Guides List page: only the pages of the guides it shows (not the blog or other pages)
                $pagesToRemove[] = $page;
            } elseif ($scopePage && in_array($scopePage->template(), ['course-list', 'subsitelist'], true)
                && !in_array($this->coursePageOf($page), $this->listedCourses(), true)) {
                // from the Courses page: only the pages of the courses it shows
                $pagesToRemove[] = $page;
            } elseif ($scopePage && in_array($scopePage->template(), ['section-list', 'guide', 'subsite', 'course'], true) && !$this->isPageOrChildOf($page, $scopePage)) {
                // from a guide or a course: only its own pages
                $pagesToRemove[] = $page;
            }
        }
        foreach ($pagesToRemove as $page) {
            $collection->remove($page);
        }
    }

    /**
     * The page that search is limited to, from ?scope=/its-route (added by the search box on a Multi-Page Content page,
     * or on a Guides List or Courses page to search all the guides or courses). Only a guide, a course, or a Guides List
     * or Courses page is accepted; otherwise (or with no scope) this returns null and the whole site is searched.
     */
    protected function getSearchScopePage()
    {
        // The scope from the address, for example '/open-education-essentials' (empty when there isn't one)
        $scope = (string) $this->grav['uri']->query('scope');

        // No scope, or not a page address (page addresses start with /): search the whole site
        if ($scope === '') {
            return null;
        }
        if (substr($scope, 0, 1) !== '/') {
            return null;
        }

        // Find the page at that address
        $scopePage = $this->grav['pages']->find($scope);

        // No page there, or it isn't a guide, a Guides List page, a course's own page (subsite.md or course.md) or the
        // Courses page: search the whole site
        if (!$scopePage) {
            return null;
        }
        if (!in_array($scopePage->template(), ['section-list', 'guide', 'reader-list', 'guide-list', 'course-list', 'subsitelist', 'subsite', 'course'], true)) {
            return null;
        }

        return $scopePage;
    }

    /**
     * The guide a page belongs to: its home page itself (guide.md or section-list.md), or the nearest one above it – or null when the page
     * isn't part of a guide.
     */
    protected function guideHomeOf($page)
    {
        // go up from the page through its parents, looking for a guide's home page
        while ($page) {
            if (in_array($page->template(), self::GUIDE_HOME_TEMPLATES, true)) {
                return $page;
            }
            $page = $page->parent();
        }
        return null;
    }

    /**
     * A course's own page (subsite.md or course.md) has no content of its own, so visiting its address (for example
     * /cpt363) goes to the course's home page instead (its course_home or subsite_home setting), as in Helios Course Hub.
     */
    public function onCourseOwnPage(Event $event)
    {
        if ($this->isAdmin()) {
            return;
        }
        $page = $event['page'];
        if (!$page || !in_array($page->template(), ['subsite', 'course'], true)) {
            return;
        }
        // (Grav's redirect adds the site's base address itself, so it gets the home page's route, such as /cpt363/home)
        $homePage = $this->courseHomePage($page);
        if ($homePage && $homePage->route() !== $page->route()) {
            $this->grav->redirect($homePage->route(), 302);
        }
    }

    /**
     * The course a page belongs to: the course's own page (subsite.md, as in Open MultiCourse Hub, or course.md, as in
     * Grav Helios Course Hub) – the page itself or the nearest one above it – or null when the page isn't in a course.
     */
    protected function coursePageOf($page)
    {
        // go up from the page through its parents, looking for a course's own page
        while ($page) {
            if (in_array($page->template(), ['subsite', 'course'], true)) {
                return $page;
            }
            $page = $page->parent();
        }
        return null;
    }

    /**
     * A course's home page: its course_home or subsite_home setting (such as "home", as in Open Matter), or else its
     * first visible page (as in Grav Helios Course Hub, whose course.md has no home setting) – or null when it has none.
     */
    public function courseHomePage($coursePage)
    {
        $header = (array) $coursePage->header();
        $homeSlug = trim((string) ($header['course_home'] ?? $header['subsite_home'] ?? ''), '/');
        if ($homeSlug !== '') {
            $homePage = $coursePage->find($coursePage->route() . '/' . $homeSlug);
            if ($homePage) {
                return $homePage;
            }
        }
        return $coursePage->children()->visible()->published()->first() ?: null;
    }

    /**
     * The address of a course's home page (see courseHomePage), or the course's own address when it has none.
     */
    protected function courseHomeUrl($coursePage)
    {
        $homePage = $this->courseHomePage($coursePage);
        return $homePage ? $homePage->url() : $coursePage->url();
    }

    /**
     * The courses listed on the site: published course and subsite pages not hidden from the course list.
     */
    protected function listedCourses()
    {
        $listed = [];
        $pages = $this->grav['pages']->all();
        foreach (['course', 'subsite'] as $type) {
            foreach ($pages->ofType($type)->published() as $coursePage) {
                $header = (array) $coursePage->header();
                if (empty($header['hide_from_course_list']) && empty($header['hide_from_subsite_list'])) {
                    $listed[] = $coursePage;
                }
            }
        }
        return $listed;
    }

    /**
     * A course's name for the header: its course_site_title or subsite_site_title setting, or else its title
     * (the same order as Open Matter).
     */
    protected function courseTitle($coursePage)
    {
        $header = (array) $coursePage->header();
        foreach (['course_site_title', 'subsite_site_title'] as $key) {
            if (!empty($header[$key])) {
                return $header[$key];
            }
        }
        return $coursePage->title();
    }

    /**
     * Is the page in a guide that its Guides List page shows (Show on Guides List not turned off)?
     */
    protected function isInListedGuide($page)
    {
        $guideHome = $this->guideHomeOf($page);
        return $guideHome && empty(((array) $guideHome->header())['hide_from_guides_list']);
    }

    /**
     * Is the page the given parent page itself, or one of the pages inside it?
     */
    protected function isPageOrChildOf($page, $parentPage)
    {
        // rawRoute() is the page's address from its folders, for example '/open-education-essentials'. Unlike route(), it
        // stays the same when the page is the site's home page (whose route is '/', which every page would be inside)
        $route = $page->rawRoute();
        $parentRoute = $parentPage->rawRoute();

        // the parent page itself
        if ($route === $parentRoute) {
            return true;
        }

        // a page inside it: its route starts with the parent's route and a slash,
        // for example '/open-education-essentials/what-is-open-education' starts with '/open-education-essentials/'
        $parentRouteWithSlash = $parentRoute . '/';
        $start = substr($route, 0, strlen($parentRouteWithSlash));
        if ($start === $parentRouteWithSlash) {
            return true;
        }

        return false;
    }

    /**
     * Is the page a top-level page set to visible: false, or inside one? (for example, an older demo page kept
     * at its address but hidden from the menu). Only an explicit visible: false counts, so top-level pages
     * that are hidden just because their folder has no number (such as "readme") are still searched.
     * The site's home page is never counted as hidden: hiding it from the menu with visible: false is common,
     * and its pages (for example a guide that is the home page) should still be found.
     */
    protected function isInHiddenTopLevelPage($page)
    {
        // the top-level page is the first of the page's parents, or the page itself when it has none
        $topLevelPage = $page;
        $parents = $this->getParentPages($page);
        if (count($parents) > 0) {
            $topLevelPage = $parents[0];
        }

        // the home page (and the pages inside it) is always searched
        if ($topLevelPage->home()) {
            return false;
        }

        // so are courses, which are usually hidden from the site's menu (the Courses page lists them instead)
        if (in_array($topLevelPage->template(), ['subsite', 'course'], true)) {
            return false;
        }

        // hidden only when its settings say visible: false
        $header = $topLevelPage->header();
        if (isset($header->visible) && $header->visible === false) {
            return true;
        }

        return false;
    }

    /**
     * Is the page inside an unpublished page? (for example, a section of an example guide that ships turned off).
     * Grav still counts pages inside an unpublished page as published, so without this they would be found by search
     * although the page they belong to is off.
     */
    protected function isInsideUnpublishedPage($page)
    {
        foreach ($this->getParentPages($page) as $parent) {
            if (!$parent->published()) {
                return true;
            }
        }

        return false;
    }

    /**
     * The pages above a page, from the top of the site down (the site's hidden root page is not included)
     */
    protected function getParentPages($page)
    {
        $parents = [];
        $parent = $page->parent();

        // stop at the site's hidden root page, the only page without a parent of its own
        while ($parent && $parent->parent()) {
            // add each parent to the start of the list, so the list runs from the top down
            array_unshift($parents, $parent);
            $parent = $parent->parent();
        }

        return $parents;
    }

    /**
     * Search results for the results template, grouped by top-level section (for example Blog) with the best matches first.
     *
     * Returns a list of groups, each like:
     *   ['title' => 'Blog', 'items' => [ ['url' => ..., 'title_html' => ..., 'trail' => ..., 'snippet_html' => ...], ... ]]
     */
    public function searchGroups($results, $query)
    {
        // SimpleSearch treats commas as separators between search words, so do the same
        $terms = [];
        foreach (explode(',', (string) $query) as $term) {
            $term = trim($term);
            // skip empty words, and words with unreadable characters (they cannot be highlighted)
            if ($term !== '' && mb_check_encoding($term, 'UTF-8')) {
                $terms[] = $term;
            }
        }
        if (!$results || count($terms) === 0) {
            return [];
        }

        $siteTitle = (string) $this->config->get('site.title');
        $groups = [];
        $position = 0;

        foreach ($results as $page) {
            // the group is the page's top-level section; pages at the top of the site go under the site title
            $parents = $this->getParentPages($page);
            $groupTitle = $siteTitle;
            $trailTitles = [];
            if (count($parents) > 0) {
                $groupTitle = $parents[0]->title();
                foreach (array_slice($parents, 1) as $parent) {
                    $trailTitles[] = $parent->title();
                }
            } elseif (in_array($page->template(), self::GUIDE_HOME_TEMPLATES, true)) {
                // a guide's home page at the top of the site goes with its own guide's pages, not under the site title
                $groupTitle = $page->title();
            }

            $title = (string) $page->title();

            // Pages whose title has a search word come first, then the others in SimpleSearch's order
            $rank = $position;
            if (!$this->containsAnyTerm($title, $terms)) {
                $rank = $rank + 100000;
            }
            $position++;

            $item = [
                'url' => $page->url(),
                'title_html' => $this->highlightTerms($title, $terms),
                'trail' => implode(' › ', $trailTitles),
                'snippet_html' => $this->highlightTerms($this->searchSnippet($page, $terms), $terms),
                'rank' => $rank,
            ];

            // Add the page to its group, creating the group the first time
            if (!isset($groups[$groupTitle])) {
                $groups[$groupTitle] = ['title' => $groupTitle, 'items' => [], 'rank' => $rank];
            }
            $groups[$groupTitle]['items'][] = $item;

            // A group's rank is the rank of its best page
            if ($rank < $groups[$groupTitle]['rank']) {
                $groups[$groupTitle]['rank'] = $rank;
            }
        }

        // Sort the pages in each group, then the groups, by rank (lowest first)
        $sortedGroups = [];
        foreach ($groups as $group) {
            usort($group['items'], [$this, 'compareRank']);
            $sortedGroups[] = $group;
        }
        usort($sortedGroups, [$this, 'compareRank']);

        return $sortedGroups;
    }

    /**
     * For sorting search results and groups: the lower rank comes first
     */
    public function compareRank($a, $b)
    {
        return $a['rank'] - $b['rank'];
    }

    /**
     * Does the text contain any of the search words (ignoring upper and lower case)?
     */
    protected function containsAnyTerm($text, $terms)
    {
        foreach ($terms as $term) {
            if (mb_stripos($text, $term) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * About 160 characters of a page's text, from just before the first search word, with … where it is cut
     */
    protected function searchSnippet($page, $terms)
    {
        $length = 160;

        // The page's text without HTML, on one line
        $text = strip_tags((string) $page->content());
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/\s+/u', ' ', $text));

        // Leave out the page title if the text starts with it (it is already shown)
        $title = (string) $page->title();
        if ($title !== '' && mb_stripos($text, $title) === 0) {
            $text = trim(mb_substr($text, mb_strlen($title)));
        }
        if ($text === '') {
            return '';
        }

        // Start about 50 characters before the first search word found
        $start = 0;
        foreach ($terms as $term) {
            $found = mb_stripos($text, $term);
            if ($found !== false) {
                $start = max(0, $found - 50);
                break;
            }
        }

        // Begin at the start of a word
        if ($start > 0) {
            $nextSpace = mb_strpos($text, ' ', $start);
            if ($nextSpace !== false && $nextSpace - $start < 20) {
                $start = $nextSpace + 1;
            }
        }

        $snippet = mb_substr($text, $start, $length);

        // End at the end of a word, and show … where the text is cut
        $isCutAtEnd = $start + $length < mb_strlen($text);
        if ($isCutAtEnd) {
            $lastSpace = mb_strrpos($snippet, ' ');
            if ($lastSpace !== false && $lastSpace > $length - 30) {
                $snippet = mb_substr($snippet, 0, $lastSpace);
            }
            $snippet = $snippet . '…';
        }
        if ($start > 0) {
            $snippet = '…' . $snippet;
        }

        return trim($snippet);
    }

    /**
     * Make text safe for HTML, with each search word wrapped in <mark> to highlight it
     */
    protected function highlightTerms($text, $terms)
    {
        // A pattern that finds any of the search words, ignoring upper and lower case
        $quotedTerms = [];
        foreach ($terms as $term) {
            $quotedTerms[] = preg_quote($term, '/');
        }
        $pattern = '/(' . implode('|', $quotedTerms) . ')/iu';

        // Split the text into pieces: the search words, and the text between them.
        // Each piece is made safe for HTML on its own, so a search word never matches inside an HTML code like &amp;
        $pieces = preg_split($pattern, (string) $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        // If the search words could not be used (for example, unreadable characters), show the text without highlighting
        if ($pieces === false) {
            return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
        }

        $html = '';
        foreach ($pieces as $index => $piece) {
            $safePiece = htmlspecialchars($piece, ENT_QUOTES, 'UTF-8');
            $isSearchWord = ($index % 2 === 1);
            if ($isSearchWord) {
                $html = $html . '<mark>' . $safePiece . '</mark>';
            } else {
                $html = $html . $safePiece;
            }
        }

        return $html;
    }

}

?>
