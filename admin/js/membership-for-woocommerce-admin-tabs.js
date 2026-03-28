(function($) {
  'use strict';

  function getUrl() {
    return new URL(window.location.href);
  }

  function getTopLink(target) {
    return $('.mfw-admin-tab-link[data-top-tab-target="' + target + '"]');
  }

  function getSubtabLink(target) {
    return $('.mfw-admin-subtab-link[data-subtab-target="' + target + '"]');
  }

  function syncMoreToggle(target) {
    var $more = $('.mfw-admin-tabs-more');
    var $toggle = $more.find('.mfw-admin-tabs-more__toggle');

    if (!$more.length) {
      return;
    }

    var isActive = $more.find('.mfw-admin-tab-link[data-top-tab-target="' + target + '"]').length > 0;
    $more.toggleClass('is-active', isActive);
    $toggle.toggleClass('is-active', isActive);
    $toggle.attr('aria-expanded', $more.hasClass('is-open') ? 'true' : 'false');
  }

  function setTopLinkState(target) {
    $('.mfw-admin-tab-link').each(function() {
      var $link = $(this);
      var isActive = $link.data('top-tab-target') === target;
      $link.toggleClass('is-active', isActive);
      $link.attr('aria-selected', isActive ? 'true' : 'false');
    });

    syncMoreToggle(target);
  }

  function setSubtabLinkState(target) {
    $('.mfw-admin-subtab-link').each(function() {
      var $link = $(this);
      var isActive = $link.data('subtab-target') === target;
      $link.toggleClass('is-active', isActive);
      $link.attr('aria-selected', isActive ? 'true' : 'false');
    });
  }

  function updateIntro(target) {
    var $link = getTopLink(target);
    var $intro = $('#mfw-admin-panel-intro');

    if (!$link.length || !$intro.length) {
      return;
    }

    $('#mfw-admin-active-panel').attr('data-top-panel', target);
    $('#mfw-admin-panel-eyebrow').text($link.data('panel-eyebrow') || '');
    $('#mfw-admin-panel-title').text($link.data('panel-title') || '');
    $('#mfw-admin-panel-desc').text($link.data('panel-desc') || '');
    $('#mfw-admin-panel-cta').text($link.data('panel-cta-label') || '');
    $('#mfw-admin-panel-cta').attr('href', $link.data('panel-cta-url') || '#');

    if (String($link.data('panel-show-intro')) === '1') {
      $intro.removeClass('mfw-admin-panel__intro--hidden');
    } else {
      $intro.addClass('mfw-admin-panel__intro--hidden');
    }
  }

  function setCanvasLoading(isLoading) {
    $('#mfw-admin-panel-canvas').toggleClass('is-loading', isLoading);
  }

  function resizeSelect2() {
    $('.select2-container').css('width', '100%');
  }

  function loadTopTab(target, subtab) {
    if (typeof mfw_admin_param === 'undefined') {
      return;
    }

    setTopLinkState(target);
    updateIntro(target);
    setCanvasLoading(true);

    $.post(mfw_admin_param.ajaxurl, {
      action: 'wps_mfw_load_admin_tab_content',
      nonce: mfw_admin_param.tabs_nonce,
      tab: target,
      mfw_reg_sub_nav: subtab || ''
    }).done(function(response) {
      if (!response || !response.success || !response.data || typeof response.data.html === 'undefined') {
        return;
      }

      var panelCanvas = document.getElementById('mfw-admin-panel-canvas');

      $('#mfw-admin-panel-canvas').html(response.data.html);
      if (typeof window.mfwInitAdminUi === 'function') {
        window.mfwInitAdminUi(panelCanvas);
      }
      $(document).trigger('mfw:panel-loaded', [panelCanvas]);
      resizeSelect2();
      syncSubtabsFromUrl(false);
    }).always(function() {
      setCanvasLoading(false);
    });
  }

  function loadSubtab(target) {
    if (typeof mfw_admin_param === 'undefined') {
      return;
    }

    var $subtabs = $('.mfw-admin-subtabs');
    if (!$subtabs.length) {
      return;
    }

    $subtabs.attr('data-active-subtab', target);
    setSubtabLinkState(target);

    $.post(mfw_admin_param.ajaxurl, {
      action: 'wps_mfw_load_admin_subtab_content',
      nonce: mfw_admin_param.tabs_nonce,
      subtab: target
    }).done(function(response) {
      if (!response || !response.success || !response.data || typeof response.data.html === 'undefined') {
        return;
      }

      var subtabContent = $subtabs.find('[data-subtab-content]').get(0);

      $subtabs.find('[data-subtab-content]').html(response.data.html);
      if (typeof window.mfwInitAdminUi === 'function') {
        window.mfwInitAdminUi(subtabContent);
      }
      $(document).trigger('mfw:subtab-loaded', [subtabContent]);
      resizeSelect2();
    });
  }

  function syncTopTabsFromUrl(forceLoad) {
    var $dashboard = $('.mfw-admin-dashboard');
    var defaultTab = $dashboard.data('default-tab');
    var url = getUrl();
    var target = url.searchParams.get('mfw_tab') || defaultTab;
    var currentTarget = $('#mfw-admin-active-panel').attr('data-top-panel');

    if (!getTopLink(target).length) {
      target = defaultTab;
    }

    setTopLinkState(target);
    updateIntro(target);

    if (forceLoad || currentTarget !== target) {
      loadTopTab(target, url.searchParams.get('mfw_reg_sub_nav') || '');
    }
  }

  function syncSubtabsFromUrl(forceLoad) {
    var url = getUrl();
    var topTarget = url.searchParams.get('mfw_tab') || $('.mfw-admin-dashboard').data('default-tab');
    var $subtabs = $('.mfw-admin-subtabs');

    if (!$subtabs.length || topTarget !== 'membership-for-woocommerce-membership-using-registration-form') {
      return;
    }

    var defaultSubtab = $subtabs.data('default-subtab');
    var target = url.searchParams.get('mfw_reg_sub_nav') || defaultSubtab;
    var currentTarget = $subtabs.attr('data-active-subtab');

    if (!getSubtabLink(target).length) {
      target = defaultSubtab;
    }

    setSubtabLinkState(target);

    if (forceLoad || currentTarget !== target) {
      loadSubtab(target);
    }
  }

  $(document).on('click', '.mfw-admin-tab-link', function(event) {
    event.preventDefault();

    var target = $(this).data('top-tab-target');
    var url = getUrl();

    $('.mfw-admin-tabs-more').removeClass('is-open').find('.mfw-admin-tabs-more__toggle').attr('aria-expanded', 'false');

    url.searchParams.set('mfw_tab', target);
    if (target === 'membership-for-woocommerce-membership-using-registration-form') {
      var defaultSubtab = $('.mfw-admin-subtabs').data('default-subtab') || 'membership-for-woocommerce-add-plans';
      if (!url.searchParams.get('mfw_reg_sub_nav')) {
        url.searchParams.set('mfw_reg_sub_nav', defaultSubtab);
      }
    } else {
      url.searchParams.delete('mfw_reg_sub_nav');
    }

    window.history.pushState({}, '', url.toString());
    syncTopTabsFromUrl(false);
  });

  $(document).on('click', '.mfw-admin-tabs-more__toggle', function(event) {
    event.preventDefault();
    event.stopPropagation();

    var $more = $(this).closest('.mfw-admin-tabs-more');
    var isOpen = $more.hasClass('is-open');

    $('.mfw-admin-tabs-more').removeClass('is-open').find('.mfw-admin-tabs-more__toggle').attr('aria-expanded', 'false');

    if (!isOpen) {
      $more.addClass('is-open');
      $(this).attr('aria-expanded', 'true');
    }
  });

  $(document).on('click', function(event) {
    if (!$(event.target).closest('.mfw-admin-tabs-more').length) {
      $('.mfw-admin-tabs-more').removeClass('is-open').find('.mfw-admin-tabs-more__toggle').attr('aria-expanded', 'false');
    }
  });

  $(document).on('click', '.mfw-admin-subtab-link', function(event) {
    event.preventDefault();

    var target = $(this).data('subtab-target');
    var url = getUrl();

    url.searchParams.set('mfw_tab', 'membership-for-woocommerce-membership-using-registration-form');
    url.searchParams.set('mfw_reg_sub_nav', target);
    window.history.pushState({}, '', url.toString());

    syncSubtabsFromUrl(false);
  });

  $(window).on('popstate', function() {
    syncTopTabsFromUrl(false);
    syncSubtabsFromUrl(false);
  });

  $(function() {
    syncTopTabsFromUrl(false);
    syncSubtabsFromUrl(false);
    resizeSelect2();
  });
})(jQuery);
