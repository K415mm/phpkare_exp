/**
 * Main
 */

'use strict';

window.isRtl = window.Helpers.isRtl();
window.isDarkStyle = window.Helpers.isDarkStyle();
let menu,
  animate,
  isHorizontalLayout = false;

if (document.getElementById('layout-menu')) {
  isHorizontalLayout = document.getElementById('layout-menu').classList.contains('menu-horizontal');
}

(function () {
  setTimeout(function () {
    window.Helpers.initCustomOptionCheck();
  }, 1000);

  if (typeof Waves !== 'undefined') {
    Waves.init();
    Waves.attach(
      ".btn[class*='btn-']:not(.position-relative):not([class*='btn-outline-']):not([class*='btn-label-'])",
      ['waves-light']
    );
    Waves.attach("[class*='btn-outline-']:not(.position-relative)");
    Waves.attach("[class*='btn-label-']:not(.position-relative)");
    Waves.attach('.pagination .page-item .page-link');
    Waves.attach('.dropdown-menu .dropdown-item');
    Waves.attach('.light-style .list-group .list-group-item-action');
    Waves.attach('.dark-style .list-group .list-group-item-action', ['waves-light']);
    Waves.attach('.nav-tabs:not(.nav-tabs-widget) .nav-item .nav-link');
    Waves.attach('.nav-pills .nav-item .nav-link', ['waves-light']);
  }

  // Initialize menu
  //-----------------

  let layoutMenuEl = document.querySelectorAll('#layout-menu');
  layoutMenuEl.forEach(function (element) {
    menu = new Menu(element, {
      orientation: isHorizontalLayout ? 'horizontal' : 'vertical',
      closeChildren: isHorizontalLayout ? true : false,
      // ? This option only works with Horizontal menu
      showDropdownOnHover: localStorage.getItem('templateCustomizer-' + templateName + '--ShowDropdownOnHover') // If value(showDropdownOnHover) is set in local storage
        ? localStorage.getItem('templateCustomizer-' + templateName + '--ShowDropdownOnHover') === 'true' // Use the local storage value
        : window.templateCustomizer !== undefined // If value is set in config.js
          ? window.templateCustomizer.settings.defaultShowDropdownOnHover // Use the config.js value
          : true // Use this if you are not using the config.js and want to set value directly from here
    });
    // Change parameter to true if you want scroll animation
    window.Helpers.scrollToActive((animate = false));
    window.Helpers.mainMenu = menu;
  });

  // Initialize menu togglers and bind click on each
  let menuToggler = document.querySelectorAll('.layout-menu-toggle');
  menuToggler.forEach(item => {
    item.addEventListener('click', event => {
      event.preventDefault();
      window.Helpers.toggleCollapsed();
      // Enable menu state with local storage support if enableMenuLocalStorage = true from config.js
      if (config.enableMenuLocalStorage && !window.Helpers.isSmallScreen()) {
        try {
          localStorage.setItem(
            'templateCustomizer-' + templateName + '--LayoutCollapsed',
            String(window.Helpers.isCollapsed())
          );
          // Update customizer checkbox state on click of menu toggler
          let layoutCollapsedCustomizerOptions = document.querySelector('.template-customizer-layouts-options');
          if (layoutCollapsedCustomizerOptions) {
            let layoutCollapsedVal = window.Helpers.isCollapsed() ? 'collapsed' : 'expanded';
            layoutCollapsedCustomizerOptions.querySelector(`input[value="${layoutCollapsedVal}"]`).click();
          }
        } catch (e) {}
      }
    });
  });

  // Menu swipe gesture

  // Detect swipe gesture on the target element and call swipe In
  window.Helpers.swipeIn('.drag-target', function (e) {
    window.Helpers.setCollapsed(false);
  });

  // Detect swipe gesture on the target element and call swipe Out
  window.Helpers.swipeOut('#layout-menu', function (e) {
    if (window.Helpers.isSmallScreen()) window.Helpers.setCollapsed(true);
  });

  // Display in main menu when menu scrolls
  let menuInnerContainer = document.getElementsByClassName('menu-inner'),
    menuInnerShadow = document.getElementsByClassName('menu-inner-shadow')[0];
  if (menuInnerContainer.length > 0 && menuInnerShadow) {
    menuInnerContainer[0].addEventListener('ps-scroll-y', function () {
      if (this.querySelector('.ps__thumb-y').offsetTop) {
        menuInnerShadow.style.display = 'block';
      } else {
        menuInnerShadow.style.display = 'none';
      }
    });
  }

  // Update light/dark image based on current style
  function switchImage(style) {
    if (style === 'system') {
      if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        style = 'dark';
      } else {
        style = 'light';
      }
    }
    const switchImagesList = [].slice.call(document.querySelectorAll('[data-app-' + style + '-img]'));
    switchImagesList.map(function (imageEl) {
      const setImage = imageEl.getAttribute('data-app-' + style + '-img');
      imageEl.src = assetsPath + 'img/' + setImage; // Using window.assetsPath to get the exact relative path
    });
  }

  //Style Switcher (Light/Dark/System Mode)
  let styleSwitcher = document.querySelector('.dropdown-style-switcher');

  // Active class on style switcher dropdown items
  const activeStyle = document.documentElement.getAttribute('data-style');

  // Get style from local storage or use 'system' as default
  let storedStyle =
    localStorage.getItem('templateCustomizer-' + templateName + '--Style') || //if no template style then use Customizer style
    (window.templateCustomizer?.settings?.defaultStyle ?? 'light'); //!if there is no Customizer then use default style as light

  // Set style on click of style switcher item if template customizer is enabled
  if (window.templateCustomizer && styleSwitcher) {
    let styleSwitcherItems = [].slice.call(styleSwitcher.children[1].querySelectorAll('.dropdown-item'));
    styleSwitcherItems.forEach(function (item) {
      item.classList.remove('active');
      item.addEventListener('click', function () {
        let currentStyle = this.getAttribute('data-theme');
        if (currentStyle === 'light') {
          window.templateCustomizer.setStyle('light');
        } else if (currentStyle === 'dark') {
          window.templateCustomizer.setStyle('dark');
        } else {
          window.templateCustomizer.setStyle('system');
        }
      });

      if (item.getAttribute('data-theme') === activeStyle) {
        // Add 'active' class to the item if it matches the activeStyle
        item.classList.add('active');
      }
    });

    // Update style switcher icon based on the stored style

    const styleSwitcherIcon = styleSwitcher.querySelector('i');

    if (storedStyle === 'light') {
      styleSwitcherIcon.classList.add('ti-sun');
      new bootstrap.Tooltip(styleSwitcherIcon, {
        title: 'Light Mode',
        fallbackPlacements: ['bottom']
      });
    } else if (storedStyle === 'dark') {
      styleSwitcherIcon.classList.add('ti-moon-stars');
      new bootstrap.Tooltip(styleSwitcherIcon, {
        title: 'Dark Mode',
        fallbackPlacements: ['bottom']
      });
    } else {
      styleSwitcherIcon.classList.add('ti-device-desktop-analytics');
      new bootstrap.Tooltip(styleSwitcherIcon, {
        title: 'System Mode',
        fallbackPlacements: ['bottom']
      });
    }
  }

  // Run switchImage function based on the stored style
  switchImage(storedStyle);

  // Internationalization (Language Dropdown)
  // ---------------------------------------

  if (typeof i18next !== 'undefined' && typeof i18NextHttpBackend !== 'undefined') {
    i18next
      .use(i18NextHttpBackend)
      .init({
        lng: window.templateCustomizer ? window.templateCustomizer.settings.lang : 'en',
        debug: false,
        fallbackLng: 'en',
        backend: {
          loadPath: assetsPath + 'json/locales/{{lng}}.json'
        },
        returnObjects: true
      })
      .then(function (t) {
        localize();
      });
  }

  let languageDropdown = document.getElementsByClassName('dropdown-language');

  if (languageDropdown.length) {
    let dropdownItems = languageDropdown[0].querySelectorAll('.dropdown-item');

    for (let i = 0; i < dropdownItems.length; i++) {
      dropdownItems[i].addEventListener('click', function () {
        let currentLanguage = this.getAttribute('data-language');
        let textDirection = this.getAttribute('data-text-direction');

        for (let sibling of this.parentNode.children) {
          var siblingEle = sibling.parentElement.parentNode.firstChild;

          // Loop through each sibling and push to the array
          while (siblingEle) {
            if (siblingEle.nodeType === 1 && siblingEle !== siblingEle.parentElement) {
              siblingEle.querySelector('.dropdown-item').classList.remove('active');
            }
            siblingEle = siblingEle.nextSibling;
          }
        }
        this.classList.add('active');

        i18next.changeLanguage(currentLanguage, (err, t) => {
          window.templateCustomizer ? window.templateCustomizer.setLang(currentLanguage) : '';
          directionChange(textDirection);
          if (err) return console.log('something went wrong loading', err);
          localize();
        });
      });
    }
    function directionChange(textDirection) {
      if (textDirection === 'rtl') {
        if (localStorage.getItem('templateCustomizer-' + templateName + '--Rtl') !== 'true')
          window.templateCustomizer ? window.templateCustomizer.setRtl(true) : '';
      } else {
        if (localStorage.getItem('templateCustomizer-' + templateName + '--Rtl') === 'true')
          window.templateCustomizer ? window.templateCustomizer.setRtl(false) : '';
      }
    }
  }

  function localize() {
    let i18nList = document.querySelectorAll('[data-i18n]');
    // Set the current language in dd
    let currentLanguageEle = document.querySelector('.dropdown-item[data-language="' + i18next.language + '"]');

    if (currentLanguageEle) {
      currentLanguageEle.click();
    }

    i18nList.forEach(function (item) {
      item.innerHTML = i18next.t(item.dataset.i18n);
    });
  }

  // Notification
  // ------------
  const notificationMarkAsReadAll = document.querySelector('.dropdown-notifications-all');
  const notificationMarkAsReadList = document.querySelectorAll('.dropdown-notifications-read');

  // Notification: Mark as all as read
  if (notificationMarkAsReadAll) {
    notificationMarkAsReadAll.addEventListener('click', event => {
      notificationMarkAsReadList.forEach(item => {
        item.closest('.dropdown-notifications-item').classList.add('marked-as-read');
      });
    });
  }
  // Notification: Mark as read/unread onclick of dot
  if (notificationMarkAsReadList) {
    notificationMarkAsReadList.forEach(item => {
      item.addEventListener('click', event => {
        item.closest('.dropdown-notifications-item').classList.toggle('marked-as-read');
      });
    });
  }

  // Notification: Mark as read/unread onclick of dot
  const notificationArchiveMessageList = document.querySelectorAll('.dropdown-notifications-archive');
  notificationArchiveMessageList.forEach(item => {
    item.addEventListener('click', event => {
      item.closest('.dropdown-notifications-item').remove();
    });
  });

  // Init helpers & misc
  // --------------------

  // Init BS Tooltip
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Accordion active class
  const accordionActiveFunction = function (e) {
    if (e.type == 'show.bs.collapse' || e.type == 'show.bs.collapse') {
      e.target.closest('.accordion-item').classList.add('active');
    } else {
      e.target.closest('.accordion-item').classList.remove('active');
    }
  };

  const accordionTriggerList = [].slice.call(document.querySelectorAll('.accordion'));
  const accordionList = accordionTriggerList.map(function (accordionTriggerEl) {
    accordionTriggerEl.addEventListener('show.bs.collapse', accordionActiveFunction);
    accordionTriggerEl.addEventListener('hide.bs.collapse', accordionActiveFunction);
  });

  // If layout is RTL add .dropdown-menu-end class to .dropdown-menu
  // if (isRtl) {
  //   Helpers._addClass('dropdown-menu-end', document.querySelectorAll('#layout-navbar .dropdown-menu'));
  // }

  // Auto update layout based on screen size
  window.Helpers.setAutoUpdate(true);

  // Toggle Password Visibility
  window.Helpers.initPasswordToggle();

  // Speech To Text
  window.Helpers.initSpeechToText();

  // Init PerfectScrollbar in Navbar Dropdown (i.e notification)
  window.Helpers.initNavbarDropdownScrollbar();

  let horizontalMenuTemplate = document.querySelector("[data-template^='horizontal-menu']");
  if (horizontalMenuTemplate) {
    // if screen size is small then set navbar fixed
    if (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT) {
      window.Helpers.setNavbarFixed('fixed');
    } else {
      window.Helpers.setNavbarFixed('');
    }
  }

  // On window resize listener
  // -------------------------
  window.addEventListener(
    'resize',
    function (event) {
      // Hide open search input and set value blank
      if (window.innerWidth >= window.Helpers.LAYOUT_BREAKPOINT) {
        if (document.querySelector('.search-input-wrapper')) {
          document.querySelector('.search-input-wrapper').classList.add('d-none');
          document.querySelector('.search-input').value = '';
        }
      }
      // Horizontal Layout : Update menu based on window size
      if (horizontalMenuTemplate) {
        // if screen size is small then set navbar fixed
        if (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT) {
          window.Helpers.setNavbarFixed('fixed');
        } else {
          window.Helpers.setNavbarFixed('');
        }
        setTimeout(function () {
          if (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT) {
            if (document.getElementById('layout-menu')) {
              if (document.getElementById('layout-menu').classList.contains('menu-horizontal')) {
                menu.switchMenu('vertical');
              }
            }
          } else {
            if (document.getElementById('layout-menu')) {
              if (document.getElementById('layout-menu').classList.contains('menu-vertical')) {
                menu.switchMenu('horizontal');
              }
            }
          }
        }, 100);
      }
    },
    true
  );

  // Manage menu expanded/collapsed with templateCustomizer & local storage
  //------------------------------------------------------------------

  // If current layout is horizontal OR current window screen is small (overlay menu) than return from here
  if (isHorizontalLayout || window.Helpers.isSmallScreen()) {
    return;
  }

  // If current layout is vertical and current window screen is > small

  // Auto update menu collapsed/expanded based on the themeConfig
  if (typeof TemplateCustomizer !== 'undefined') {
    if (window.templateCustomizer.settings.defaultMenuCollapsed) {
      window.Helpers.setCollapsed(true, false);
    } else {
      window.Helpers.setCollapsed(false, false);
    }
  }

  // Manage menu expanded/collapsed state with local storage support If enableMenuLocalStorage = true in config.js
  if (typeof config !== 'undefined') {
    if (config.enableMenuLocalStorage) {
      try {
        if (localStorage.getItem('templateCustomizer-' + templateName + '--LayoutCollapsed') !== null)
          window.Helpers.setCollapsed(
            localStorage.getItem('templateCustomizer-' + templateName + '--LayoutCollapsed') === 'true',
            false
          );
      } catch (e) {}
    }
  }
})();

// ! Removed following code if you do't wish to use jQuery. Remember that navbar search functionality will stop working on removal.
if (typeof $ !== 'undefined') {
  $(function () {
    // ! TODO: Required to load after DOM is ready, did this now with jQuery ready.
    window.Helpers.initSidebarToggle();
    // Toggle Universal Sidebar

    // Navbar Search with autosuggest (typeahead)
    // ? You can remove the following JS if you don't want to use search functionality.
    //----------------------------------------------------------------------------------

    var searchToggler = $('.search-toggler'),
      searchInputWrapper = $('.search-input-wrapper'),
      searchInput = $('.search-input'),
      contentBackdrop = $('.content-backdrop');

    // Open search input on click of search icon
    if (searchToggler.length) {
      searchToggler.on('click', function () {
        if (searchInputWrapper.length) {
          searchInputWrapper.toggleClass('d-none');
          searchInput.focus();
        }
      });
    }
    // Open search on 'CTRL+/'
    $(document).on('keydown', function (event) {
      let ctrlKey = event.ctrlKey,
        slashKey = event.which === 191;

      if (ctrlKey && slashKey) {
        if (searchInputWrapper.length) {
          searchInputWrapper.toggleClass('d-none');
          searchInput.focus();
        }
      }
    });
    // Note: Following code is required to update container class of typeahead dropdown width on focus of search input. setTimeout is required to allow time to initiate Typeahead UI.
    setTimeout(function () {
      var twitterTypeahead = $('.twitter-typeahead');
      searchInput.on('focus', function () {
        if (searchInputWrapper.hasClass('container-xxl')) {
          searchInputWrapper.find(twitterTypeahead).addClass('container-xxl');
          twitterTypeahead.removeClass('container-fluid');
        } else if (searchInputWrapper.hasClass('container-fluid')) {
          searchInputWrapper.find(twitterTypeahead).addClass('container-fluid');
          twitterTypeahead.removeClass('container-xxl');
        }
      });
    }, 10);

    if (searchInput.length) {
      // Filter config
      var filterConfig = function (data) {
        return function findMatches(q, cb) {
          let matches;
          matches = [];
          data.filter(function (i) {
            if (i.name.toLowerCase().startsWith(q.toLowerCase())) {
              matches.push(i);
            } else if (
              !i.name.toLowerCase().startsWith(q.toLowerCase()) &&
              i.name.toLowerCase().includes(q.toLowerCase())
            ) {
              matches.push(i);
              matches.sort(function (a, b) {
                return b.name < a.name ? 1 : -1;
              });
            } else {
              return [];
            }
          });
          cb(matches);
        };
      };

      // Search JSON
      var searchJson = 'search-vertical.json'; // For vertical layout
      if ($('#layout-menu').hasClass('menu-horizontal')) {
        var searchJson = 'search-horizontal.json'; // For vertical layout
      }
      // Search API AJAX call
      var searchData = $.ajax({
        url: assetsPath + 'json/' + searchJson, //? Use your own search api instead
        dataType: 'json',
        async: false
      }).responseJSON;
      // Init typeahead on searchInput
      searchInput.each(function () {
        var $this = $(this);
        searchInput
          .typeahead(
            {
              hint: false,
              classNames: {
                menu: 'tt-menu navbar-search-suggestion',
                cursor: 'active',
                suggestion: 'suggestion d-flex justify-content-between px-4 py-2 w-100'
              }
            },
            // ? Add/Update blocks as per need
            // Pages
            {
              name: 'pages',
              display: 'name',
              limit: 5,
              source: filterConfig(searchData.pages),
              templates: {
                header: '<h6 class="suggestions-header text-primary mb-0 mx-4 mt-3 pb-2">Pages</h6>',
                suggestion: function ({ url, icon, name }) {
                  return (
                    '<a href="' +
                    url +
                    '">' +
                    '<div>' +
                    '<i class="ti ' +
                    icon +
                    ' me-2"></i>' +
                    '<span class="align-middle">' +
                    name +
                    '</span>' +
                    '</div>' +
                    '</a>'
                  );
                },
                notFound:
                  '<div class="not-found px-4 py-2">' +
                  '<h6 class="suggestions-header text-primary mb-2">Pages</h6>' +
                  '<p class="py-2 mb-0"><i class="ti ti-alert-circle ti-xs me-2"></i> No Results Found</p>' +
                  '</div>'
              }
            },
            // Files
            {
              name: 'files',
              display: 'name',
              limit: 4,
              source: filterConfig(searchData.files),
              templates: {
                header: '<h6 class="suggestions-header text-primary mb-0 mx-4 mt-3 pb-2">Files</h6>',
                suggestion: function ({ src, name, subtitle, meta }) {
                  return (
                    '<a href="javascript:;">' +
                    '<div class="d-flex w-50">' +
                    '<img class="me-3" src="' +
                    assetsPath +
                    src +
                    '" alt="' +
                    name +
                    '" height="32">' +
                    '<div class="w-75">' +
                    '<h6 class="mb-0">' +
                    name +
                    '</h6>' +
                    '<small class="text-muted">' +
                    subtitle +
                    '</small>' +
                    '</div>' +
                    '</div>' +
                    '<small class="text-muted">' +
                    meta +
                    '</small>' +
                    '</a>'
                  );
                },
                notFound:
                  '<div class="not-found px-4 py-2">' +
                  '<h6 class="suggestions-header text-primary mb-2">Files</h6>' +
                  '<p class="py-2 mb-0"><i class="ti ti-alert-circle ti-xs me-2"></i> No Results Found</p>' +
                  '</div>'
              }
            },
            // Members
            {
              name: 'members',
              display: 'name',
              limit: 4,
              source: filterConfig(searchData.members),
              templates: {
                header: '<h6 class="suggestions-header text-primary mb-0 mx-4 mt-3 pb-2">Members</h6>',
                suggestion: function ({ name, src, subtitle }) {
                  return (
                    '<a href="app-user-view-account.html">' +
                    '<div class="d-flex align-items-center">' +
                    '<img class="rounded-circle me-3" src="' +
                    assetsPath +
                    src +
                    '" alt="' +
                    name +
                    '" height="32">' +
                    '<div class="user-info">' +
                    '<h6 class="mb-0">' +
                    name +
                    '</h6>' +
                    '<small class="text-muted">' +
                    subtitle +
                    '</small>' +
                    '</div>' +
                    '</div>' +
                    '</a>'
                  );
                },
                notFound:
                  '<div class="not-found px-4 py-2">' +
                  '<h6 class="suggestions-header text-primary mb-2">Members</h6>' +
                  '<p class="py-2 mb-0"><i class="ti ti-alert-circle ti-xs me-2"></i> No Results Found</p>' +
                  '</div>'
              }
            }
          )
          //On typeahead result render.
          .bind('typeahead:render', function () {
            // Show content backdrop,
            contentBackdrop.addClass('show').removeClass('fade');
          })
          // On typeahead select
          .bind('typeahead:select', function (ev, suggestion) {
            // Open selected page
            if (suggestion.url) {
              window.location = suggestion.url;
            }
          })
          // On typeahead close
          .bind('typeahead:close', function () {
            // Clear search
            searchInput.val('');
            $this.typeahead('val', '');
            // Hide search input wrapper
            searchInputWrapper.addClass('d-none');
            // Fade content backdrop
            contentBackdrop.addClass('fade').removeClass('show');
          });

        // On searchInput keyup, Fade content backdrop if search input is blank
        searchInput.on('keyup', function () {
          if (searchInput.val() == '') {
            contentBackdrop.addClass('fade').removeClass('show');
          }
        });
      });

      // Init PerfectScrollbar in search result
      var psSearch;
      $('.navbar-search-suggestion').each(function () {
        psSearch = new PerfectScrollbar($(this)[0], {
          wheelPropagation: false,
          suppressScrollX: true
        });
      });

      searchInput.on('keyup', function () {
        psSearch.update();
      });
    }
  });
}

    let nodes = new vis.DataSet();
    let edges = new vis.DataSet();
    let nodeCount = 0;
    let connectionCount = 0;

    function addNode() {
      const nodeModal = new bootstrap.Modal(document.getElementById('nodeModal'));
      nodeModal.show();
    }

    function saveNode() {
      nodeCount++;
      const nodeName = document.getElementById('nodeName').value;
      const nodeType = document.getElementById('nodeType').value;
      const manufacturer = document.getElementById('manufacturer').value;
      const realName = document.getElementById('realName').value;
      const os = document.getElementById('os').value;
      const version = document.getElementById('version').value;

      if (!nodeName || !nodeType || !manufacturer || !realName || !os || !version) {
        alert("All fields are required!");
        return;
      }

      nodes.add({
        id: nodeCount,
        label: `${nodeName} (${nodeType})`,
        type: nodeType,
        manufacturer: manufacturer,
        realName: realName,
        os: os,
        version: version,
        vulnerabilities: []
      });

      updateHistory('Node', nodeCount, nodeName, nodeType, manufacturer, realName, os, version);
      bootstrap.Modal.getInstance(document.getElementById('nodeModal')).hide();
    }

    function addConnection() {
      const connectionModal = new bootstrap.Modal(document.getElementById('connectionModal'));
      connectionModal.show();
    }

    function saveConnection() {
      connectionCount++;
      const fromNode = document.getElementById('fromNode').value;
      const toNode = document.getElementById('toNode').value;
      const color = document.getElementById('color').value;
      const width = document.getElementById('width').value;

      if (!fromNode || !toNode || !color || !width) {
        alert("All connection details are required!");
        return;
      }

      edges.add({
        id: connectionCount,
        from: fromNode,
        to: toNode,
        color: {
          color: color
        },
        width: width
      });
      updateHistory('Connection', connectionCount, fromNode, toNode);
      bootstrap.Modal.getInstance(document.getElementById('connectionModal')).hide();
    }

    function updateHistory(type, id, nameOrFrom, typeOrTo, manufacturer = '', realName = '', os = '', version = '') {
      const historyContainer = document.getElementById('history');
      let historyHTML = '';
      if (type === 'Node') {
        historyHTML = `
            <div class="card mb-5">
                <div class="card-header" id="heading${id}">
                    <h5 class="mb-0">
                        <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${id}" aria-expanded="true" aria-controls="collapse${id}">
                            Node ${id}: ${nameOrFrom} (${typeOrTo})
                        </button>
                    </h5>
                </div>
                <div id="collapse${id}" class="collapse" aria-labelledby="heading${id}" data-bs-parent="#history">
                    <div class="card-body">
                        <p>Manufacturer: ${manufacturer}</p>
                        <p>Real Name: ${realName}</p>
                        <p>OS: ${os}</p>
                        <p>Version: ${version}</p>
                    </div>
                </div>
            </div>
        `;
      } else if (type === 'Connection') {
        historyHTML = `<p>${type} ${id}: From Node ${nameOrFrom} to Node ${typeOrTo}</p>`;
      }
      historyContainer.insertAdjacentHTML('beforeend', historyHTML);
      updateNetwork();
    }

    function updateNetwork() {
      const container = document.getElementById('network');
      const data = {
        nodes: nodes,
        edges: edges
      };
      const options = {};
      new vis.Network(container, data, options);
    }

    function submitForm() {
      const nodesArray = nodes.get();
      const connectionsArray = edges.get();
    
      const formData = new FormData();
      formData.append('nodes', JSON.stringify(nodesArray));
      formData.append('connections', JSON.stringify(connectionsArray));
      formData.append('project', document.getElementById('project').value);
      formData.append('client', document.getElementById('client').value);
    
      fetch('generate_yaml.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.text())
        .then(data => {
          console.log(data);
        })
        .catch(error => console.error('Error:', error));
    }
    

    // Functions to handle CSV upload data
    function addNodeFromCSV(node) {
      nodeCount++;
      nodes.add({
        id: nodeCount,
        label: `${node.name} (${node.type})`,
        type: node.type,
        manufacturer: node.manufacturer,
        realName: node.realName,
        os: node.os,
        version: node.version,
        vulnerabilities: []
      });
      updateHistory('Node', nodeCount, node.name, node.type, node.manufacturer, node.realName, node.os, node.version);
    }

    function addConnectionFromCSV(connection) {
      connectionCount++;
      edges.add({
        id: connectionCount,
        from: connection.from,
        to: connection.to,
        color: {
          color: connection.color
        },
        width: connection.width
      });
      updateHistory('Connection', connectionCount, connection.from, connection.to);
    }
    // Execute the generated JavaScript code from the CSV upload
   
    document.addEventListener('DOMContentLoaded', function () {
      const analysisModalElement = document.getElementById('analysisModal');
      let analysisModal;
      if (analysisModalElement) {
          analysisModal = new bootstrap.Modal(analysisModalElement);
      }
  
      function fetchAll() {
          if (typeof nodes === 'undefined' || typeof nodes.get === 'undefined') {
              console.error('Nodes object is not defined or does not have a get method.');
          } else {
              const nodeArray = nodes.get(); // Extract the array of nodes
              console.log('Nodes array:', nodeArray);
              fetchAllVulnerabilities(nodeArray);
          }
      }
  
      async function fetchAllVulnerabilities(nodeArray) {
          if (analysisModal) {
              analysisModal.show();
          }
  
          document.body.style.pointerEvents = 'none';
  
          if (!Array.isArray(nodeArray) || nodeArray.length === 0) {
              console.error('Nodes array is not defined or empty.');
              displayModalMessage('No nodes available to fetch vulnerabilities.');
              document.body.style.pointerEvents = 'auto';
              if (analysisModal) {
                  analysisModal.hide();
              }
              return;
          }
  
          for (let i = 0; i < nodeArray.length; i++) {
              const node = nodeArray[i];
              if (!node) {
                  console.error(`Node at index ${i} is undefined.`);
                  displayModalMessage(`Node at index ${i} is undefined.`);
                  continue;
              }
  
              try {
                  await fetchVulnerabilities(node.manufacturer, node.os, node.version, node.type, node.id);
              } catch (error) {
                  console.error('Error fetching vulnerabilities:', error);
                  displayModalMessage(`Error fetching vulnerabilities for node ${node.id}: ${error.message}`);
              }
          }
  
          document.body.style.pointerEvents = 'auto';
          displayModalMessage('Finished fetching vulnerabilities for all nodes.');
          if (analysisModal) {
              analysisModal.hide();
          }
      }
  
      function fetchVulnerabilities(manufacturer, os, version, type, nodeId) {
          return fetch(`fetch_vulnerabilities.php?manufacturer=${manufacturer}&product=${os}&version=${version}&type=${type}`)
              .then(response => response.json())
              .then(data => {
                  if (data.error) {
                      console.error(data.error);
                      displayModalMessage(`Error fetching vulnerabilities for node ${nodeId}: ${data.error}`);
                  } else {
                      const processedVulnerabilities = processVulnerabilities(data.vulnerabilities);
                      nodes.update({
                          id: nodeId,
                          vulnerabilities: processedVulnerabilities
                      });
                      updateHistoryWithVulnerabilities(nodeId, processedVulnerabilities);
                      displayModalMessage(`Fetched vulnerabilities for node ${nodeId}`);
                  }
              })
              .catch(error => {
                  console.error('Error fetching vulnerabilities:', error);
                  displayModalMessage(`Error fetching vulnerabilities for node ${nodeId}: ${error.message}`);
              });
      }
  
      function displayModalMessage(message) {
          const modalBody = document.querySelector('#analysisModal .modal-body');
          const messageElement = document.createElement('p');
          messageElement.textContent = message;
          modalBody.appendChild(messageElement);
      }
  
      function processVulnerabilities(vulnerabilities) {
          const currentYear = new Date().getFullYear();
          const twoYearsAgo = currentYear - 1;
          const severityGroups = {
              high: [],
              critical: [],
              others: {}
          };
  
          vulnerabilities.forEach(vuln => {
              const year = new Date(vuln.cve.lastModified).getFullYear();
              let severity = '';
  
              if (vuln.cve.metrics.cvssMetricV2) {
                  severity = vuln.cve.metrics.cvssMetricV2[0].baseSeverity.toLowerCase();
              } else if (vuln.cve.metrics.cvssMetricV3) {
                  severity = vuln.cve.metrics.cvssMetricV3[0].cvssData.baseSeverity.toLowerCase();
              } else if (vuln.cve.metrics.cvssMetricV31) {
                  severity = vuln.cve.metrics.cvssMetricV31[0].cvssData.baseSeverity.toLowerCase();
              } else if (vuln.cve.metrics.cvssMetricV4) {
                  severity = vuln.cve.metrics.cvssMetricV4[0].cvssData.baseSeverity.toLowerCase();
              }
  
              if (year >= twoYearsAgo && (severity === 'high' || severity === 'critical')) {
                  severityGroups[severity].push(vuln);
              } else {
                  if (!severityGroups.others[year]) {
                      severityGroups.others[year] = {
                          low: 0,
                          medium: 0,
                          high: 0,
                          critical: 0
                      };
                  }
                  severityGroups.others[year][severity]++;
              }
          });
  
          return severityGroups;
      }
  
      function updateHistoryWithVulnerabilities(nodeId, vulnerabilities) {
          const historyContainer = document.getElementById('history');
          const nodeCard = historyContainer.querySelector(`#collapse${nodeId} .card-body`);
          if (nodeCard) {
              let vulnerabilitiesHTML = '<h3>Vulnerabilities:</h3>';
  
              ['high', 'critical'].forEach(severity => {
                  if (vulnerabilities[severity].length > 0) {
                      vulnerabilitiesHTML += `<h4>${severity.charAt(0).toUpperCase() + severity.slice(1)}:</h4>`;
                      vulnerabilities[severity].forEach(vuln => {
                          let severityx = '';
                          if (vuln.cve.metrics.cvssMetricV2) {
                              severityx = vuln.cve.metrics.cvssMetricV2[0].baseSeverity.toLowerCase();
                          } else if (vuln.cve.metrics.cvssMetricV3) {
                              severityx = vuln.cve.metrics.cvssMetricV3[0].cvssData.baseSeverity.toLowerCase();
                          } else if (vuln.cve.metrics.cvssMetricV31) {
                              severityx = vuln.cve.metrics.cvssMetricV31[0].cvssData.baseSeverity.toLowerCase();
                          } else if (vuln.cve.metrics.cvssMetricV4) {
                              severityx = vuln.cve.metrics.cvssMetricV4[0].cvssData.baseSeverity.toLowerCase();
                          }
                          vulnerabilitiesHTML += `
                              <div class="card mb-2">
                                  <div class="card-body">
                                      <h5 class="card-title">${vuln.cve.id}</h5>
                                      <span class="badge bg-danger">${severityx}</span>
                                      <span class="badge bg-secondary">${new Date(vuln.cve.lastModified).toLocaleDateString()}</span>
                                  </div>
                              </div>
                          `;
                      });
                  }
              });
  
              nodeCard.innerHTML = vulnerabilitiesHTML;
          }
      }
  
      // Expose fetchAll to the global scope
      window.fetchAll = fetchAll;
  });
  

    function toggleDarkMode() {
      document.body.classList.toggle('bg-dark');
      document.body.classList.toggle('text-light');
      document.querySelectorAll('.card').forEach(card => {
        card.classList.toggle('bg-dark');
        card.classList.toggle('text-light');
      });
      document.querySelectorAll('.btn').forEach(btn => {
        btn.classList.toggle('btn-light');
        btn.classList.toggle('btn-dark');
      });
    }
    document.addEventListener("DOMContentLoaded", function() {
      // Get the JavaScript code from the data attribute
      var jsDataElement = document.getElementById('js-data');
      var jsCode = jsDataElement.getAttribute('data-js-code');
  
      // Parse the JSON string to get the JavaScript code
      var parsedJsCode = JSON.parse(jsCode);
  
      // Execute the JavaScript code
      new Function(parsedJsCode)();
  });
