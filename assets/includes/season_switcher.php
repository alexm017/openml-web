<?php
if (defined('ALPHABIT_SEASON_SWITCHER_RENDERED')) {
    return;
}
define('ALPHABIT_SEASON_SWITCHER_RENDERED', true);

$requestUri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
$requestPath = parse_url($requestUri, PHP_URL_PATH);
$isModelPath = is_string($requestPath) && preg_match('#^/model(?:/|$)#', $requestPath) === 1;
$isHomePath = ($requestPath === '/' || $requestPath === '/index.php');
$isAuthPath = in_array($requestPath, ['/login', '/register', '/administrative/login.php', '/administrative/register.php'], true);
$isTrainingDataPath = is_string($requestPath) && preg_match('#^/model/(?:decode|intothedeep)/training(?:/|$)#', $requestPath) === 1;
$isOnlineTrainingPath = is_string($requestPath) && preg_match('#^/model/(?:decode|intothedeep)/online_training_ml(?:/|$)#', $requestPath) === 1;
$showSeasonSwitchButton = $isModelPath && !$isTrainingDataPath && !$isOnlineTrainingPath;

$rawSeasonChoice = isset($_COOKIE['season_choice']) ? (string) $_COOKIE['season_choice'] : 'Decode';
$seasonChoice = ($rawSeasonChoice === 'IntoTheDeep') ? 'IntoTheDeep' : 'Decode';
$seasonButtonLabel = ($seasonChoice === 'Decode')
    ? 'Switch to IntoTheDeep Season'
    : 'Switch to Decode Season';
$siteLang = isset($_COOKIE['site_lang']) ? (string) $_COOKIE['site_lang'] : 'en';
$siteLang = ($siteLang === 'ro') ? 'ro' : 'en';
$docsMenuLabel = ($siteLang === 'ro') ? 'Meniu' : 'Menu';
?>
<style>
    .language-popup-overlay {
        display: none !important;
        visibility: hidden !important;
    }

    .lang-switcher {
        position: fixed;
        top: <?php echo $isHomePath ? '1.06rem' : ($isAuthPath ? '0.72rem' : '0.95rem'); ?>;
        right: <?php echo $isHomePath ? '0.52rem' : '0.55rem'; ?>;
        z-index: 1250;
        display: inline-flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.35rem;
    }

    .lang-switcher-toggle,
    .lang-flag-btn {
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 999px;
        background: rgba(20, 20, 20, 0.9);
        color: #f1f1f1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        line-height: 1;
        cursor: pointer;
        padding: 0;
        transition: transform 0.18s ease, border-color 0.18s ease, background-color 0.18s ease, opacity 0.18s ease;
    }

    .lang-switcher-toggle {
        min-width: 54px;
        height: 34px;
        padding: 0 0.5rem 0 0.42rem;
        gap: 0.3rem;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.32);
        font-size: 17px;
        border: 1px solid rgba(255, 255, 255, 0.24);
        background: rgba(20, 20, 20, 0.94);
        color: #f1f1f1;
    }

    .lang-toggle-flag {
        line-height: 1;
    }

    .lang-toggle-caret {
        display: inline-block;
        color: #f1f1f1;
        font-size: 13px;
        line-height: 1;
        transform-origin: 50% 45%;
        transition: transform 0.18s ease;
    }

    .lang-flag-btn {
        width: 32px;
        height: 32px;
        font-size: 16px;
    }

    .lang-switcher-list {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.35rem;
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        pointer-events: none;
        transform: translateY(-6px);
        transition: max-height 0.22s ease, opacity 0.18s ease, transform 0.18s ease;
    }

    .lang-switcher.is-open .lang-switcher-list {
        max-height: 90px;
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0);
    }

    .lang-switcher-toggle:hover {
        transform: translateY(-1px);
        border-color: rgba(255, 255, 255, 0.44);
        background: rgba(30, 30, 30, 0.96);
    }

    .lang-flag-btn:hover {
        transform: translateY(-1px);
        border-color: rgba(255, 255, 255, 0.42);
    }

    .lang-switcher-toggle[aria-expanded='true'] {
        border-color: rgba(255, 255, 255, 0.5);
        background: rgba(34, 34, 34, 0.97);
    }

    .lang-switcher.is-open .lang-toggle-caret {
        transform: rotate(180deg);
    }

    .lang-flag-btn.is-active {
        border-color: rgba(255, 255, 255, 0.52);
        background: rgba(255, 255, 255, 0.18);
        opacity: 0.72;
    }

    @media (max-width: 980px) {
        .lang-switcher {
            top: <?php echo $isHomePath ? '0.98rem' : ($isModelPath ? '3.82rem' : ($isAuthPath ? '0.42rem' : '0.55rem')); ?>;
            right: <?php echo $isHomePath ? '4.15rem' : ($isModelPath ? '0.72rem' : '0.45rem'); ?>;
        }

        .lang-flag-btn {
            width: 30px;
            height: 30px;
            font-size: 15px;
        }

        .lang-switcher-toggle {
            min-width: 50px;
            height: 32px;
            padding: 0 0.44rem 0 0.38rem;
            gap: 0.24rem;
            font-size: 16px;
        }

        .lang-toggle-caret {
            font-size: 12px;
        }
    }

    <?php if ($showSeasonSwitchButton): ?>
    .season-switch-button {
        position: fixed;
        left: 22px;
        bottom: 22px;
        z-index: 1200;
        padding: 5px 13px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.16);
        background: rgba(10, 10, 10, 0.88);
        color: #f2f2f2;
        font-size: 12.5px;
        line-height: 1.2;
        font-weight: 600;
        white-space: nowrap;
        cursor: pointer;
        backdrop-filter: blur(3px);
        -webkit-backdrop-filter: blur(3px);
        transition: background-color 0.2s ease, transform 0.2s ease;
        animation: fadeIn 1s ease-in-out forwards;
    }

    .season-switch-button:hover {
        background: rgba(24, 24, 24, 0.95);
        transform: translateY(-1px);
    }

    @media (max-width: 900px) {
        .season-switch-button {
            left: 12px;
            bottom: 12px;
            max-width: calc(100vw - 24px);
            font-size: 11.5px;
            padding: 5px 10px;
        }
    }
    <?php endif; ?>
</style>
<div id="lang-switcher" class="lang-switcher" aria-label="Language switcher">
    <button
        type="button"
        id="lang-switcher-toggle"
        class="lang-switcher-toggle"
        aria-label="Open language menu"
        aria-expanded="false"><span id="lang-toggle-flag" class="lang-toggle-flag">🇺🇸</span><span class="lang-toggle-caret" aria-hidden="true">▾</span></button>
    <div id="lang-switcher-list" class="lang-switcher-list" role="menu" aria-label="Language options">
        <button type="button" class="lang-flag-btn" data-lang="en" aria-label="Switch to English" title="English">🇺🇸</button>
        <button type="button" class="lang-flag-btn" data-lang="ro" aria-label="Switch to Romanian" title="Romana">🇷🇴</button>
    </div>
</div>
<?php if ($showSeasonSwitchButton): ?>
<button
    type="button"
    id="season-switch-button"
    class="season-switch-button"
    data-current-season="<?php echo htmlspecialchars($seasonChoice, ENT_QUOTES, 'UTF-8'); ?>">
    <?php echo htmlspecialchars($seasonButtonLabel, ENT_QUOTES, 'UTF-8'); ?>
</button>
<?php endif; ?>
<script>
    (function () {
        var langCookieName = 'site_lang';
        var seasonCookieName = 'season_choice';
        var docsMenuLabel = <?php echo json_encode($docsMenuLabel); ?>;
        var switchButton = document.getElementById('season-switch-button');
        var langSwitcher = document.getElementById('lang-switcher');
        var langToggleButton = document.getElementById('lang-switcher-toggle');
        var langToggleFlag = document.getElementById('lang-toggle-flag');
        var langList = document.getElementById('lang-switcher-list');

        function setCookie(name, value, days) {
            var expires = '';
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }
            document.cookie = name + '=' + (value || '') + expires + '; path=/; SameSite=Lax';
        }

        function getCookie(name) {
            var nameEQ = name + '=';
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var current = cookies[i];
                while (current.charAt(0) === ' ') {
                    current = current.substring(1, current.length);
                }
                if (current.indexOf(nameEQ) === 0) {
                    return current.substring(nameEQ.length, current.length);
                }
            }
            return null;
        }

        function normalizeLang(value) {
            return value === 'ro' ? 'ro' : 'en';
        }

        function flagByLang(langValue) {
            return normalizeLang(langValue) === 'ro' ? '🇷🇴' : '🇺🇸';
        }

        function setLangMenuOpen(isOpen) {
            if (!langSwitcher || !langToggleButton) {
                return;
            }
            langSwitcher.classList.toggle('is-open', isOpen);
            langToggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }

        function ensureDefaultLanguage() {
            var cookieLang = getCookie(langCookieName);
            if (!cookieLang) {
                setCookie(langCookieName, 'en', 3650);
                return 'en';
            }
            return normalizeLang(cookieLang);
        }

        function removeLegacyLanguagePopups() {
            var overlays = document.querySelectorAll('.language-popup-overlay');
            for (var i = 0; i < overlays.length; i++) {
                var overlay = overlays[i];
                overlay.style.display = 'none';
                if (overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }
            }
        }

        function applyLanguageButtonState(langValue) {
            if (!langSwitcher) {
                return;
            }
            var normalizedLang = normalizeLang(langValue);
            if (langToggleFlag) {
                langToggleFlag.textContent = flagByLang(normalizedLang);
            }
            var buttons = langSwitcher.querySelectorAll('.lang-flag-btn[data-lang]');
            for (var i = 0; i < buttons.length; i++) {
                var button = buttons[i];
                var isActive = button.getAttribute('data-lang') === normalizedLang;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            }
        }

        var activeLang = ensureDefaultLanguage();
        removeLegacyLanguagePopups();
        applyLanguageButtonState(activeLang);

        if (langSwitcher && !langSwitcher.dataset.bound) {
            if (langToggleButton) {
                langToggleButton.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    setLangMenuOpen(!langSwitcher.classList.contains('is-open'));
                });
            }

            if (langList) {
                langList.addEventListener('click', function (event) {
                    var button = event.target && event.target.closest('.lang-flag-btn[data-lang]');
                    if (!button) {
                        return;
                    }
                    var nextLang = normalizeLang(button.getAttribute('data-lang'));
                    setLangMenuOpen(false);
                    if (nextLang === normalizeLang(getCookie(langCookieName))) {
                        applyLanguageButtonState(nextLang);
                        return;
                    }
                    setCookie(langCookieName, nextLang, 3650);
                    applyLanguageButtonState(nextLang);
                    window.location.reload();
                });
            }

            document.addEventListener('click', function (event) {
                if (!langSwitcher.contains(event.target)) {
                    setLangMenuOpen(false);
                }
            });

            window.addEventListener('resize', function () {
                setLangMenuOpen(false);
            });

            langSwitcher.dataset.bound = '1';
        }

        setupDocsMenuToggle();

        if (!switchButton) {
            return;
        }

        function normalizeSeason(value) {
            return value === 'IntoTheDeep' ? 'IntoTheDeep' : 'Decode';
        }

        function seasonToPath(season) {
            return season === 'Decode' ? 'decode' : 'intothedeep';
        }

        function seasonLabel(season) {
            return season === 'Decode'
                ? 'Switch to IntoTheDeep Season'
                : 'Switch to Decode Season';
        }

        function buildTargetUrl(nextSeason) {
            var path = window.location.pathname || '';
            var search = window.location.search || '';
            var hash = window.location.hash || '';
            var modelMatch = path.match(/^\/model\/(decode|intothedeep)(\/.*)?$/i);

            if (!modelMatch) {
                return '/model/' + seasonToPath(nextSeason) + '/overview';
            }

            var restPath = modelMatch[2] || '/overview';
            var slugMatch = restPath.match(/^\/([^\/?#]+)/);
            var slug = slugMatch && slugMatch[1] ? slugMatch[1].toLowerCase() : '';

            var decodeOnlyPages = {
                'apriltag': true,
                'apriltag_code_sample': true,
                'apriltag-code-sample': true,
                'apriltag-implementation': true,
                'autonomous': true,
                'odometry': true,
                'road_runner_056': true,
                'road_runner_10': true,
                'pedro_pathing': true,
                'road-runner-056': true,
                'road-runner-10': true,
                'pedro-pathing': true,
                'auto_aiming_getting_started': true,
                'gyroscope_only': true,
                'camera_only': true,
                'gyroscope_and_camera': true,
                'auto-aiming-getting-started': true,
                'gyroscope-only': true,
                'camera-only': true,
                'gyroscope-and-camera': true
            };

            if (nextSeason === 'IntoTheDeep' && decodeOnlyPages[slug]) {
                restPath = '/overview';
            }

            return '/model/' + seasonToPath(nextSeason) + restPath + search + hash;
        }

        function fallbackPosition() {
            switchButton.style.left = '22px';
            switchButton.style.bottom = '22px';
            switchButton.style.top = 'auto';
        }

        function positionButtonBetweenSeasonAndDocs() {
            var seasonText = document.querySelector('.before_docs');
            var docsText = document.querySelector('.docs');
            if (!seasonText || !docsText) {
                fallbackPosition();
                return;
            }

            var seasonRect = seasonText.getBoundingClientRect();
            var docsRect = docsText.getBoundingClientRect();
            if (!seasonRect.width || !docsRect.width) {
                fallbackPosition();
                return;
            }

            var spacing = 2;
            var buttonHeight = switchButton.offsetHeight || 18;
            var buttonWidth = switchButton.offsetWidth || 190;

            var minTop = seasonRect.bottom + spacing;
            var maxTop = docsRect.top - buttonHeight - spacing;
            var top = maxTop >= minTop
                ? minTop + ((maxTop - minTop) / 2)
                : (docsRect.top - buttonHeight - spacing);

            var centerX = seasonRect.left + (seasonRect.width / 2);
            var left = centerX - (buttonWidth / 2);

            top = Math.max(6, top);
            left = Math.max(8, Math.min(left, window.innerWidth - buttonWidth - 8));

            switchButton.style.top = Math.round(top) + 'px';
            switchButton.style.left = Math.round(left) + 'px';
            switchButton.style.bottom = 'auto';
        }

        function setupDocsMenuToggle() {
            var docsContainer = document.querySelector('.docs-container');
            var backgroundContainer = document.querySelector('.background-container');
            if (!docsContainer || !backgroundContainer) {
                return;
            }

            var mobileQuery = window.matchMedia('(max-width: 980px)');
            var toggleButton = document.getElementById('model-docs-menu-toggle');

            if (!toggleButton) {
                toggleButton = document.createElement('button');
                toggleButton.type = 'button';
                toggleButton.id = 'model-docs-menu-toggle';
                toggleButton.className = 'model-docs-toggle';
                toggleButton.setAttribute('aria-expanded', 'false');
                toggleButton.innerHTML = '<span class="model-docs-toggle-icon" aria-hidden="true"></span><span>' + docsMenuLabel + '</span>';
                backgroundContainer.appendChild(toggleButton);
            }

            function setDocsOpen(isOpen) {
                docsContainer.classList.toggle('is-open', isOpen);
                toggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            }

            if (!toggleButton.dataset.bound) {
                toggleButton.addEventListener('click', function () {
                    setDocsOpen(!docsContainer.classList.contains('is-open'));
                });

                docsContainer.addEventListener('click', function (event) {
                    if (event.target && event.target.closest('a')) {
                        setDocsOpen(false);
                    }
                });

                document.addEventListener('click', function (event) {
                    if (!mobileQuery.matches || !docsContainer.classList.contains('is-open')) {
                        return;
                    }
                    if (toggleButton.contains(event.target) || docsContainer.contains(event.target)) {
                        return;
                    }
                    setDocsOpen(false);
                });

                toggleButton.dataset.bound = '1';
            }

            function syncDocsMenuForViewport() {
                if (!mobileQuery.matches) {
                    setDocsOpen(false);
                    toggleButton.style.display = 'none';
                } else {
                    toggleButton.style.display = 'inline-flex';
                }
            }

            syncDocsMenuForViewport();
            window.addEventListener('resize', syncDocsMenuForViewport);
            window.addEventListener('orientationchange', syncDocsMenuForViewport);
        }

        var detectedSeason = normalizeSeason(switchButton.getAttribute('data-current-season') || getCookie(seasonCookieName));
        if (!getCookie(seasonCookieName)) {
            setCookie(seasonCookieName, 'Decode', 365);
            detectedSeason = 'Decode';
        }

        switchButton.textContent = seasonLabel(detectedSeason);
        positionButtonBetweenSeasonAndDocs();
        setTimeout(positionButtonBetweenSeasonAndDocs, 80);
        window.addEventListener('resize', positionButtonBetweenSeasonAndDocs);
        window.addEventListener('orientationchange', positionButtonBetweenSeasonAndDocs);
        switchButton.addEventListener('click', function () {
            var nextSeason = detectedSeason === 'Decode' ? 'IntoTheDeep' : 'Decode';
            setCookie(seasonCookieName, nextSeason, 365);
            window.location.href = buildTargetUrl(nextSeason);
        });
    })();
</script>
