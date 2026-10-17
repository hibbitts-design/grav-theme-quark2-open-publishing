<?php

// Developed with the assistance of Claude Code (claude.ai)
// Quark 2 version of Quark Open Publishing: the same pages, settings and search, on the Quark 2 theme

namespace Grav\Theme;

use Grav\Common\Grav;
use Grav\Common\Theme;
use RocketTheme\Toolbox\Event\Event;

class Quark2OpenPublishing extends Quark2
{
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
            'onSimpleSearchCollection' => ['onSimpleSearchCollection', 0]
        ];
    }

    public function onThemeInitialized()
    {
        parent::onThemeInitialized();

        // Style GitHub-style alerts (> [!NOTE] etc., as in Helios) with the theme's own callout styles (css/callouts.css),
        // unless the GitHub Markdown Alerts plugin's wrapper class has been customised
        if ($this->config->get('plugins.github-markdown-alerts.wrapper_class', 'md-alert md-alert--') === 'md-alert md-alert--') {
            $this->config->set('plugins.github-markdown-alerts.include_css', false);
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

        if ($this->isAdmin() && ($this->grav['config']->get('plugins.shortcode-core.enabled'))) {
            $this->grav['assets']->add('theme://editor-buttons/admin/js/shortcode-pdf.js');
            $this->grav['assets']->add('theme://editor-buttons/admin/js/shortcode-h5p.js');
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

        // only a guide's home page (the Section List page type)
        $guide = $this->grav['pages']->find($guideRoute);
        if (!$guide || $guide->template() !== 'section-list') {
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
            } elseif ($this->isInHiddenTopLevelPage($page)) {
                $pagesToRemove[] = $page;
            } elseif ($scopePage && !$this->isPageOrChildOf($page, $scopePage)) {
                $pagesToRemove[] = $page;
            }
        }
        foreach ($pagesToRemove as $page) {
            $collection->remove($page);
        }
    }

    /**
     * The page that search is limited to, from ?scope=/its-route (added by the search box on a Multi-Page Content page).
     * Only a Section List page is accepted; otherwise (or with no scope) this returns null and the whole site is searched.
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

        // No page there, or it isn't a Section List page: search the whole site
        if (!$scopePage) {
            return null;
        }
        if ($scopePage->template() !== 'section-list') {
            return null;
        }

        return $scopePage;
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
     */
    protected function isInHiddenTopLevelPage($page)
    {
        // the top-level page is the first of the page's parents, or the page itself when it has none
        $topLevelPage = $page;
        $parents = $this->getParentPages($page);
        if (count($parents) > 0) {
            $topLevelPage = $parents[0];
        }

        // hidden only when its settings say visible: false
        $header = $topLevelPage->header();
        if (isset($header->visible) && $header->visible === false) {
            return true;
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
