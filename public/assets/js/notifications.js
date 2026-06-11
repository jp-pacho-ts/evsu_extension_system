document.addEventListener('DOMContentLoaded', function() {
    const wrappers = document.querySelectorAll('.notification-dropdown-wrap');
    if(!wrappers.length) return;

    const closeDropdown = function(wrapper) {
        const panel = wrapper.querySelector('[data-notification-dropdown]');
        const toggle = wrapper.querySelector('[data-notification-dropdown-toggle]');
        if(!panel || !toggle) return;

        panel.hidden = true;
        wrapper.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
    };

    const closeAll = function(except) {
        wrappers.forEach(function(wrapper) {
            if(wrapper !== except) closeDropdown(wrapper);
        });
    };

    const openDropdown = function(wrapper) {
        const panel = wrapper.querySelector('[data-notification-dropdown]');
        const toggle = wrapper.querySelector('[data-notification-dropdown-toggle]');
        if(!panel || !toggle) return;

        closeAll(wrapper);
        panel.hidden = false;
        wrapper.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
    };

    wrappers.forEach(function(wrapper) {
        const toggle = wrapper.querySelector('[data-notification-dropdown-toggle]');
        const closeButton = wrapper.querySelector('[data-notification-dropdown-close]');

        if(toggle) {
            toggle.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();

                if(wrapper.classList.contains('open')) closeDropdown(wrapper);
                else openDropdown(wrapper);
            });
        }

        if(closeButton) {
            closeButton.addEventListener('click', function(event) {
                event.preventDefault();
                closeDropdown(wrapper);
            });
        }
    });

    document.addEventListener('click', function(event) {
        if(event.target.closest('.notification-dropdown-wrap')) return;
        closeAll(null);
    });

    document.addEventListener('keydown', function(event) {
        if(event.key === 'Escape') closeAll(null);
    });

    window.GESESPNotificationDropdownReady = true;
});
