document.addEventListener('DOMContentLoaded', function() {
    const notificationWrappers = document.querySelectorAll('.notification-dropdown-wrap');
    const accountWrappers = document.querySelectorAll('.account-dropdown');
    if(!notificationWrappers.length && !accountWrappers.length) return;

    const closeNotificationDropdown = function(wrapper) {
        const panel = wrapper.querySelector('[data-notification-dropdown]');
        const toggle = wrapper.querySelector('[data-notification-dropdown-toggle]');
        if(!panel || !toggle) return;

        panel.hidden = true;
        wrapper.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
    };

    const closeAllNotifications = function(except) {
        notificationWrappers.forEach(function(wrapper) {
            if(wrapper !== except) closeNotificationDropdown(wrapper);
        });
    };

    const closeAccountDropdown = function(wrapper) {
        const panel = wrapper.querySelector('[data-account-dropdown]');
        const toggle = wrapper.querySelector('[data-account-dropdown-toggle]');
        if(!panel || !toggle) return;

        panel.hidden = true;
        wrapper.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
    };

    const closeAllAccounts = function(except) {
        accountWrappers.forEach(function(wrapper) {
            if(wrapper !== except) closeAccountDropdown(wrapper);
        });
    };

    const openNotificationDropdown = function(wrapper) {
        const panel = wrapper.querySelector('[data-notification-dropdown]');
        const toggle = wrapper.querySelector('[data-notification-dropdown-toggle]');
        if(!panel || !toggle) return;

        closeAllNotifications(wrapper);
        closeAllAccounts(null);
        panel.hidden = false;
        wrapper.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
    };

    const openAccountDropdown = function(wrapper) {
        const panel = wrapper.querySelector('[data-account-dropdown]');
        const toggle = wrapper.querySelector('[data-account-dropdown-toggle]');
        if(!panel || !toggle) return;

        closeAllAccounts(wrapper);
        closeAllNotifications(null);
        panel.hidden = false;
        wrapper.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
    };

    notificationWrappers.forEach(function(wrapper) {
        const toggle = wrapper.querySelector('[data-notification-dropdown-toggle]');
        const closeButton = wrapper.querySelector('[data-notification-dropdown-close]');

        if(toggle) {
            toggle.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();

                if(wrapper.classList.contains('open')) closeNotificationDropdown(wrapper);
                else openNotificationDropdown(wrapper);
            });
        }

        if(closeButton) {
            closeButton.addEventListener('click', function(event) {
                event.preventDefault();
                closeNotificationDropdown(wrapper);
            });
        }
    });

    accountWrappers.forEach(function(wrapper) {
        const toggle = wrapper.querySelector('[data-account-dropdown-toggle]');

        if(toggle) {
            toggle.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();

                if(wrapper.classList.contains('open')) closeAccountDropdown(wrapper);
                else openAccountDropdown(wrapper);
            });
        }
    });

    document.addEventListener('click', function(event) {
        if(!event.target.closest('.notification-dropdown-wrap')) closeAllNotifications(null);
        if(!event.target.closest('.account-dropdown')) closeAllAccounts(null);
    });

    document.addEventListener('keydown', function(event) {
        if(event.key === 'Escape') {
            closeAllNotifications(null);
            closeAllAccounts(null);
        }
    });

    window.GESESPNotificationDropdownReady = true;
    window.GESESPAccountDropdownReady = true;
});
